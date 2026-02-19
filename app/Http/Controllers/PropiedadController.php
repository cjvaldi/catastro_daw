<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CatastroService;
use Illuminate\Support\Facades\Auth;
use App\Models\Propiedad;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Busqueda;
use App\Models\Nota;
use InvalidArgumentException;

/**
 * Controlador principal para la gestión de propiedades catastrales
 * 
 * Gestiona todas las operaciones relacionadas con propiedades inmobiliarias:
 * - Búsqueda por referencia catastral (público)
 * - Búsqueda por dirección (solo Premium)
 * - Guardado y almacenamiento de propiedades
 * - Gestión de favoritos (solo Premium)
 * - Sistema de notas privadas/públicas (solo Premium)
 * - Historial de búsquedas
 * 
 * @package App\Http\Controllers
 * @author Cristian Valdivieso
 * @version 1.0
 */
class PropiedadController extends Controller
{
    /**
     * Muestra el listado de propiedades guardadas del usuario autenticado
     * 
     * Funcionalidades:
     * - Usuarios anónimos: No ven propiedades (colección vacía)
     * - Usuarios autenticados: Ven solo sus propiedades guardadas
     * - Usuarios Premium: Pueden filtrar entre "Todas" o "Solo Favoritas"
     * 
     * Incluye paginación de 15 elementos por página y eager loading de relaciones
     * para optimizar consultas a la base de datos.
     * 
     * @param Request $request Incluye parámetro opcional 'filtro' (valores: 'favoritas')
     * 
     * @return \Illuminate\View\View Vista con listado paginado de propiedades
     */
    public function index(Request $request)
    {
        // Usuarios no autenticados ven lista vacía
        if (!auth()->check()) {
            $propiedades = collect();
            return view('propiedades.index', compact('propiedades'));
        }

        // Construir query base: solo propiedades del usuario actual
        $query = Propiedad::where('user_id', auth()->id())
            ->with(['provincia', 'municipio']); // Eager loading para optimización

        // Filtro "Solo Favoritas" (funcionalidad exclusiva Premium)
        if (auth()->user()->isPremium() && $request->filtro === 'favoritas') {
            $query->whereHas('favoritos', function ($q) {
                $q->where('usuario_id', auth()->id());
            });
        }

        // Ordenar por más recientes y paginar
        $propiedades = $query->latest()->paginate(15);

        return view('propiedades.index', compact('propiedades'));
    }

    /**
     * Muestra el detalle completo de una propiedad específica
     * 
     * Carga todas las relaciones necesarias:
     * - Provincia y municipio (datos geográficos)
     * - Unidades constructivas (subdivisiones del inmueble)
     * 
     * @param Propiedad $propiedad Modelo con route model binding automático
     * 
     * @return \Illuminate\View\View Vista de detalle con toda la información
     */
    public function show(Propiedad $propiedad)
    {
        // Cargar relaciones para evitar N+1 queries
        $propiedad->load(['provincia', 'municipio', 'unidadesConstructivas']);
        
        return view('propiedades.show', compact('propiedad'));
    }

    /**
     * Busca una propiedad por su referencia catastral (acceso público)
     * 
     * Esta es la función principal de búsqueda, accesible para todos los usuarios
     * (autenticados y anónimos). Consulta la API oficial del Catastro y muestra
     * una vista previa de los datos obtenidos.
     * 
     * Si el usuario está autenticado, registra la búsqueda en su historial.
     * 
     * @param Request $request Debe incluir 'referencia' (14-20 caracteres alfanuméricos)
     * @param CatastroService $catastro Servicio inyectado para consultas API
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *         Vista de preview con datos, o redirect con error
     * 
     * @throws \InvalidArgumentException Si el formato de la referencia es inválido
     * @throws \Exception Si la API no responde o no encuentra resultados
     */
    public function buscar(Request $request, CatastroService $catastro)
    {
        // Validar formato de entrada
        $request->validate([
            'referencia' => 'required|string|min:14|max:20'
        ]);

        // DEBUG: Descomentar para depuración en desarrollo
        // \Log::info('=== BÚSQUEDA INICIADA ===', [
        //     'referencia' => $request->referencia,
        //     'usuario' => auth()->id() ?? 'anónimo',
        // ]);

        try {
            // Consultar API del Catastro
            $datos = $catastro->consultarPorReferencia($request->referencia);

            // Registrar en historial si el usuario está autenticado
            $this->registrarBusqueda(
                tipo: 'referencia',
                query: $request->referencia,
                referencia: $request->referencia,
                resultados: 1
            );

            // Mostrar vista previa con datos obtenidos
            return view('propiedades.preview', [
                'datos'      => $datos,
                'referencia' => $request->referencia,
                'tipo'       => 'referencia',
            ]);

        } catch (\InvalidArgumentException $e) {
            // Error de validación (formato incorrecto)
            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            // Error de API o conexión
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Busca propiedades por dirección postal (solo usuarios Premium)
     * 
     * LIMITACIÓN CONOCIDA: La API pública del Catastro tiene restricciones
     * no documentadas en este endpoint. Cuando falla, se activa automáticamente
     * un sistema de fallback que muestra datos de ejemplo (2 simulados + 2 reales)
     * claramente identificados con badges.
     * 
     * Esta implementación permite demostrar la funcionalidad completa incluso
     * cuando la API externa presenta problemas.
     * 
     * @param Request $request Debe incluir: provincia, municipio, tipo_via, nombre_via, numero
     * @param CatastroService $catastro Servicio inyectado para consultas API
     * 
     * @return \Illuminate\View\View Vista con resultados (reales o simulados con indicador)
     */
    public function buscarPorDireccion(Request $request, CatastroService $catastro)
    {
        // Validar todos los campos obligatorios
        $request->validate([
            'provincia'   => 'required|string|min:3|max:50',
            'municipio'   => 'required|string|min:3|max:50',
            'tipo_via'    => 'required|string|max:10',
            'nombre_via'  => 'required|string|min:3|max:100',
            'numero'      => 'required|string|max:10',
        ]);

        try {
            // Intentar consultar API real del Catastro
            $datos = $catastro->consultarPorDireccion(
                provincia: $request->provincia,
                municipio: $request->municipio,
                tipoVia: $request->tipo_via,
                nombreVia: $request->nombre_via,
                numero: $request->numero
            );

            $simulado = false;

        } catch (\Exception $e) {
            // Si la API falla, activar modo demostración con datos de ejemplo
            \Log::info('API Catastro falló en búsqueda por dirección. Usando fallback simulado.', [
                'error' => $e->getMessage(),
                'direccion' => $request->nombre_via
            ]);

            $datos = $this->obtenerDatosSimulados($request);
            $simulado = true;
        }

        // Construir texto descriptivo de la búsqueda
        $queryText = "{$request->tipo_via} {$request->nombre_via} {$request->numero}, " .
                     "{$request->municipio} ({$request->provincia})";

        // Registrar en historial si está autenticado
        if (auth()->check()) {
            Busqueda::create([
                'usuario_id'          => auth()->id(),
                'query_text'          => $queryText,
                'referencia_busqueda' => null,
                'params_json'         => [
                    'tipo'        => 'direccion',
                    'provincia'   => $request->provincia,
                    'municipio'   => $request->municipio,
                    'tipo_via'    => $request->tipo_via,
                    'nombre_via'  => $request->nombre_via,
                    'numero'      => $request->numero,
                ],
                'result_count' => $this->contarResultados($datos),
            ]);
        }

        // Mostrar resultados con indicador de si son reales o simulados
        return view('propiedades.preview-direccion', [
            'datos'    => $datos,
            'filtro'   => [
                'provincia'   => $request->provincia,
                'municipio'   => $request->municipio,
                'tipo_via'    => $request->tipo_via,
                'nombre_via'  => $request->nombre_via,
                'numero'      => $request->numero,
            ],
            'simulado' => $simulado,
        ]);
    }

    /**
     * Genera datos de ejemplo cuando la API del Catastro falla (sistema de fallback)
     * 
     * Debido a las limitaciones de la API pública en búsquedas por dirección,
     * este método proporciona datos de demostración que incluyen:
     * - 2 propiedades simuladas (con referencias aleatorias y datos ficticios)
     * - 2 propiedades reales (referencias catastrales verificadas y funcionales)
     * 
     * Cada resultado incluye un flag '_simulado' para identificación en la vista.
     * 
     * @param Request $request Solicitud original con datos de búsqueda
     * 
     * @return array Array con estructura idéntica a la respuesta real de la API
     * 
     * @internal Este método solo se llama cuando falla la API real
     */
    private function obtenerDatosSimulados(Request $request): array
    {
        $resultados = [];

        // Generar 2 propiedades simuladas para demostración
        for ($i = 1; $i <= 2; $i++) {
            $resultados[] = [
                'bi' => [
                    'idbi' => [
                        'rc' => [
                            'pc1' => str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                            'pc2' => 'YJ0624N',
                            'car' => str_pad($i, 4, '0', STR_PAD_LEFT),
                            'cc1' => chr(rand(65, 90)) . chr(rand(65, 90)),
                            'cc2' => '',
                        ]
                    ],
                    'ldt' => strtoupper(
                        "{$request->tipo_via} {$request->nombre_via} {$request->numero} " .
                        "Esc {$i} Pta A {$request->municipio} ({$request->provincia})"
                    ),
                    'dt' => [
                        'np' => strtoupper($request->provincia),
                        'nm' => strtoupper($request->municipio),
                    ],
                    'debi' => [
                        'luso' => $i == 1 ? 'Residencial' : 'Oficinas',
                        'sfc'  => rand(50, 150),
                        'ant'  => rand(10, 50),
                    ],
                    '_simulado' => true, // Flag de identificación
                ]
            ];
        }

        // Añadir 2 referencias reales conocidas que funcionan en la API
        $referenciasReales = [
            [
                'pc1'        => '2749704',
                'pc2'        => 'YJ0624N',
                'car'        => '0001',
                'cc1'        => 'DI',
                'cc2'        => '',
                'direccion'  => 'CL GUAYANA-MOJONERA 3',
                'uso'        => 'Residencial',
                'superficie' => '57.00',
                'antiguedad' => '1975',
            ],
            [
                'pc1'        => '3301204',
                'pc2'        => 'QB6430S',
                'car'        => '0008',
                'cc1'        => 'QR',
                'cc2'        => '',
                'direccion'  => 'CL BRIHUEGA 6',
                'uso'        => 'Residencial',
                'superficie' => '100.00',
                'antiguedad' => '1980',
            ],
        ];

        // Procesar referencias reales
        foreach ($referenciasReales as $ref) {
            $resultados[] = [
                'bi' => [
                    'idbi' => [
                        'rc' => [
                            'pc1' => $ref['pc1'],
                            'pc2' => $ref['pc2'],
                            'car' => $ref['car'],
                            'cc1' => $ref['cc1'],
                            'cc2' => $ref['cc2'],
                        ]
                    ],
                    'ldt' => strtoupper($ref['direccion']),
                    'dt' => [
                        'np' => strtoupper($request->provincia),
                        'nm' => strtoupper($request->municipio),
                    ],
                    'debi' => [
                        'luso' => $ref['uso'],
                        'sfc'  => $ref['superficie'],
                        'ant'  => $ref['antiguedad'],
                    ],
                    '_simulado' => false, // Flag: estos son reales
                ]
            ];
        }

        // Devolver estructura idéntica a respuesta real de la API
        return [
            'consulta_dnplocResult' => [
                'control' => [
                    'cudnp'  => count($resultados),
                    'cucons' => count($resultados),
                ],
                'bico' => $resultados,
            ]
        ];
    }

    /**
     * Guarda o actualiza una propiedad en la base de datos (usuarios autenticados)
     * 
     * Proceso completo:
     * 1. Decodifica JSON raw con datos completos de la API
     * 2. Extrae y normaliza todos los campos relevantes
     * 3. Crea provincia y municipio si no existen (firstOrCreate)
     * 4. Guarda/actualiza la propiedad con updateOrCreate
     * 5. Regenera unidades constructivas (elimina antiguas y crea nuevas)
     * 
     * Utiliza updateOrCreate para evitar duplicados cuando el usuario guarda
     * la misma propiedad varias veces.
     * 
     * @param Request $request Debe incluir 'raw_json' con la respuesta completa de la API
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige al detalle de la propiedad guardada
     */
    public function guardar(Request $request)
    {
        // Decodificar JSON raw que viene del formulario hidden
        $datos = json_decode($request->raw_json, true);

        if (!$datos) {
            return back()->with('error', 'Datos inválidos o corruptos.');
        }

        // DEBUG: Descomentar para ver estructura completa en desarrollo
        // \Log::info('=== GUARDANDO PROPIEDAD ===', [
        //     'usuario' => auth()->id(),
        //     'keys' => array_keys($datos),
        // ]);

        // Navegar por la estructura JSON de la API
        $data = $datos['consulta_dnprcResult'];
        $bico = $data['bico'];
        $bi   = $bico['bi'];
        $rc   = $bi['idbi']['rc'];

        // Construir referencia catastral completa
        $referencia = $rc['pc1'] . $rc['pc2'] . $rc['car'] . $rc['cc1'] . $rc['cc2'];

        // Extraer datos de localización geográfica
        $provinciaCodigo = $bi['dt']['loine']['cp'] ?? null;
        $municipioCodigo = $bi['dt']['cmc'] ?? null;
        $provinciaNombre = $bi['dt']['np'] ?? null;
        $municipioNombre = $bi['dt']['nm'] ?? null;

        // Extraer datos de dirección postal
        $lourb = $bi['dt']['locs']['lous']['lourb'] ?? null;
        $dir   = $lourb['dir'] ?? [];
        $loint = $lourb['loint'] ?? [];

        // Crear provincia si no existe (gestión automática de maestros)
        Provincia::firstOrCreate(
            ['codigo' => $provinciaCodigo],
            ['nombre' => $provinciaNombre]
        );

        // Crear municipio si no existe
        Municipio::firstOrCreate(
            ['codigo' => $municipioCodigo],
            ['nombre' => $municipioNombre, 'provincia_codigo' => $provinciaCodigo]
        );

        // Guardar o actualizar propiedad (evita duplicados)
        $propiedad = Propiedad::updateOrCreate(
            [
                'referencia_catastral' => $referencia,
                'user_id' => auth()->id()
            ],
            [
                'clase'               => $bi['idbi']['cn'] ?? null,
                'provincia_codigo'    => $provinciaCodigo,
                'municipio_codigo'    => $municipioCodigo,
                'provincia'           => $provinciaNombre,
                'municipio'           => $municipioNombre,
                'direccion_text'      => $bi['ldt'] ?? null,
                'tipo_via'            => $dir['tv'] ?? null,
                'nombre_via'          => $dir['nv'] ?? null,
                'numero'              => $dir['pnp'] ?? null,
                'bloque'              => $loint['bq'] ?? null,
                'escalera'            => $loint['es'] ?? null,
                'planta'              => $loint['pt'] ?? null,
                'puerta'              => $loint['pu'] ?? null,
                'codigo_postal'       => $lourb['dp'] ?? null,
                'uso'                 => $bi['debi']['luso'] ?? null,
                'superficie_m2'       => (float)($bi['debi']['sfc'] ?? 0),
                'coef_participacion'  => str_replace(',', '.', $bi['debi']['cpt'] ?? null),
                'antiguedad_anios'    => $bi['debi']['ant'] ?? null,
                'raw_json'            => $datos,
            ]
        );

        // Regenerar unidades constructivas (elementos del inmueble)
        // Eliminamos las antiguas para evitar duplicados
        $propiedad->unidadesConstructivas()->delete();

        // Crear nuevas unidades constructivas si existen en los datos
        if (isset($bico['lcons'])) {
            foreach ($bico['lcons'] as $unidad) {
                $propiedad->unidadesConstructivas()->create([
                    'tipo_unidad'          => $unidad['lcd'] ?? null,
                    'tipologia'            => $unidad['dvcons']['dtip'] ?? null,
                    'superficie_m2'        => $unidad['dfcons']['stl'] ?? null,
                    'localizacion_externa' => $unidad['dt']['lourb']['loint']['es'] ?? null,
                    'raw_json'             => $unidad,
                ]);
            }
        }

        return redirect()
            ->route('propiedades.show', $propiedad)
            ->with('success', 'Propiedad guardada correctamente.');
    }

    /**
     * Muestra el historial de búsquedas del usuario autenticado
     * 
     * Incluye tanto búsquedas por referencia como por dirección,
     * ordenadas de más reciente a más antigua, con paginación de 20 elementos.
     * 
     * @return \Illuminate\View\View Vista con historial paginado
     */
    public function historial()
    {
        $busquedas = Busqueda::where('usuario_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('propiedades.historial', compact('busquedas'));
    }

    /**
     * Registra una búsqueda en el historial del usuario
     * 
     * Solo registra si el usuario está autenticado. Los usuarios anónimos
     * pueden buscar pero no se guarda su historial.
     * 
     * @param string $tipo Tipo de búsqueda: 'referencia' o 'direccion'
     * @param string $query Texto de la búsqueda realizada
     * @param string|null $referencia Referencia catastral si aplica
     * @param int $resultados Número de resultados encontrados
     * 
     * @return void
     * 
     * @internal
     */
    private function registrarBusqueda(
        string $tipo,
        string $query,
        ?string $referencia,
        int $resultados
    ): void {
        // Solo registrar para usuarios autenticados
        if (!auth()->check()) {
            return;
        }

        Busqueda::create([
            'usuario_id'          => auth()->id(),
            'query_text'          => $query,
            'referencia_busqueda' => $referencia,
            'params_json'         => ['tipo' => $tipo, 'query' => $query],
            'result_count'        => $resultados,
        ]);
    }

    /**
     * Cuenta el número de resultados en una respuesta de búsqueda por dirección
     * 
     * La API del Catastro devuelve formatos diferentes según el número de resultados:
     * - 1 resultado: 'bico' es un objeto con 'bi'
     * - Múltiples: 'bico' es un array de objetos
     * 
     * @param array $datos Respuesta JSON decodificada de la API
     * 
     * @return int Número de propiedades encontradas
     * 
     * @internal
     */
    private function contarResultados(array $datos): int
    {
        $result = $datos['consulta_dnplocResult'] ?? [];
        $bicos  = $result['bico'] ?? [];

        // Si tiene clave 'bi', es un único resultado (objeto)
        if (isset($bicos['bi'])) {
            return 1;
        }

        // Si no, es un array de resultados
        return is_array($bicos) ? count($bicos) : 0;
    }

    /**
     * Añade o quita una propiedad de favoritos (toggle)
     * 
     * Funcionalidad exclusiva para usuarios Premium. Si la propiedad ya está
     * en favoritos, la elimina. Si no está, la añade.
     * 
     * @param Propiedad $propiedad Propiedad a marcar/desmarcar (route model binding)
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige a la página anterior con mensaje
     */
    public function toggleFavorito(Propiedad $propiedad)
    {
        // Verificar si ya está en favoritos
        $favorito = $propiedad->favoritos()
            ->where('usuario_id', auth()->id())
            ->first();

        if ($favorito) {
            // Ya es favorito → Quitar de favoritos
            $favorito->delete();
            return back()->with('success', 'Propiedad eliminada de favoritos.');
        } else {
            // No es favorito → Añadir a favoritos
            $propiedad->favoritos()->create([
                'usuario_id' => auth()->id(),
            ]);
            return back()->with('success', 'Propiedad añadida a favoritos.');
        }
    }

    /**
     * Guarda una nota en una propiedad (funcionalidad Premium)
     * 
     * Las notas pueden ser:
     * - Privadas: Solo visibles para el usuario que las creó
     * - Públicas: Visibles para todos los usuarios de la aplicación
     * 
     * @param Request $request Debe incluir 'contenido' (max 1000 caracteres) y 'tipo' (privada/publica)
     * @param Propiedad $propiedad Propiedad donde se añade la nota (route model binding)
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige a la página anterior con mensaje
     */
    public function guardarNota(Request $request, Propiedad $propiedad)
    {
        // Validar entrada
        $request->validate([
            'contenido' => 'required|string|max:1000',
            'tipo'      => 'required|in:privada,publica',
        ]);

        // Crear nota asociada a la propiedad
        $propiedad->notas()->create([
            'usuario_id' => auth()->id(),
            'texto'      => $request->contenido,
            'tipo'       => $request->tipo,
        ]);

        return back()->with('success', 'Nota añadida correctamente.');
    }

    /**
     * Elimina una nota de una propiedad
     * 
     * Solo el autor de la nota puede eliminarla. Se verifica la propiedad
     * mediante comparación de usuario_id.
     * 
     * @param Propiedad $propiedad Propiedad que contiene la nota (route model binding)
     * @param Nota $nota Nota a eliminar (route model binding)
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige a la página anterior con mensaje
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no es el autor
     */
    public function eliminarNota(Propiedad $propiedad, Nota $nota)
    {
        // Verificar que la nota pertenece al usuario autenticado
        if ($nota->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar esta nota.');
        }

        $nota->delete();

        return back()->with('success', 'Nota eliminada correctamente.');
    }

    // DEBUG: Método de prueba - Eliminar en producción
    // /**
    //  * Método de testing para verificar estructura de respuesta de la API
    //  * 
    //  * @deprecated Solo para desarrollo - Eliminar antes de deployment
    //  * @internal
    //  */
    // public function testApi(Request $request, CatastroService $catastro)
    // {
    //     $request->validate([
    //         'referencia' => 'required|string|min:14'
    //     ]);
    //
    //     $datos = $catastro->consultarPorReferencia($request->referencia);
    //     dump($datos); // Inspeccionar estructura
    // }
}