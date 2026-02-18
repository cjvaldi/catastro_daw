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

class PropiedadController extends Controller
{
    public function index(Request $request)
    {
        // Si el usuario está autenticado, mostrar solo sus propiedades
        // Si es anónimo, mostrar las propiedades públicas (o ninguna)
        // Filtro Todas/Favoritas en listado premium

        if (!auth()->check()) {
            $propiedades = collect();
            return view('propiedades.index', compact('propiedades'));
        }

        $query = Propiedad::where('user_id', auth()->id())
            ->with(['provincia', 'municipio']);

        // Filtro de favoritos (solo para Premium)
        if (auth()->user()->isPremium() && $request->filtro === 'favoritas') {
            $query->whereHas('favoritos', function ($q) {
                $q->where('usuario_id', auth()->id());
            });
        }

        $propiedades = $query->latest()->paginate(15);

        return view('propiedades.index', compact('propiedades'));
    }

    public function show(Propiedad $propiedad)
    {
        // dump($propiedad);
        $propiedad->load(['provincia', 'municipio', 'unidadesConstructivas']);
        return view('propiedades.show', compact('propiedad'));
    }
    // Busqueda por referencia - Público
    public function buscar(Request $request, CatastroService $catastro)
    {
        $request->validate([
            'referencia' => 'required|string|min:14|max:20'
        ]);
        // ✅ DEBUG TEMPORAL
        \Log::info('=== BÚSQUEDA INICIADA ===', [
            'referencia' => $request->referencia,
        ]);
        try {
            $datos = $catastro->consultarPorReferencia($request->referencia);

            // Registrar busqqueda si esta autenticado
            $this->registrarBusqueda(
                tipo: 'referencia',
                query: $request->referencia,
                referencia: $request->referencia,
                resultados: 1
            );

            return view('propiedades.preview', [
                'datos'      => $datos,
                'referencia' => $request->referencia,
                'tipo'       => 'referencia',
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // Busqueda por Dirección - Solo Premium
    // En Desarrollo por requerimientos estrictos del Api
    public function buscarPorDireccion(Request $request, CatastroService $catastro)
    {
        $request->validate([
            'provincia' => 'required|string|min:3|max:50',
            'municipio' => 'required|string|min:3|max:50',
            'tipo_via' => 'required|string|max:10',
            'nombre_via' => 'required|string|min:3|max:100',
            'numero' => 'required|string|max:10',
        ]);

        try {
            $datos = $catastro->consultarPorDireccion(
                provincia: $request->provincia,
                municipio: $request->municipio,
                tipoVia: $request->tipo_via,
                nombreVia: $request->nombre_via,
                numero: $request->numero
            );

            $simulado = false;
        } catch (\Exception $e) {
            // Si falla la API real, usar datos simulados para demo
            \Log::info('API falló, usando datos simulados: ' . $e->getMessage());

            $datos = $this->obtenerDatosSimulados($request);
            $simulado = true;
        }

        // Registrar búsqueda
        $queryText = "{$request->tipo_via} {$request->nombre_via} {$request->numero}, " .
            "{$request->municipio} ({$request->provincia})";

        if (auth()->check()) {
            Busqueda::create([
                'usuario_id' => auth()->id(),
                'query_text' => $queryText,
                'referencia_busqueda' => null,
                'params_json' => [
                    'tipo' => 'direccion',
                    'provincia' => $request->provincia,
                    'municipio' => $request->municipio,
                    'tipo_via' => $request->tipo_via,
                    'nombre_via' => $request->nombre_via,
                    'numero' => $request->numero,
                ],
                'result_count' => $this->contarResultados($datos),
            ]);
        }

        return view('propiedades.preview-direccion', [
            'datos' => $datos,
            'filtro' => [
                'provincia' => $request->provincia,
                'municipio' => $request->municipio,
                'tipo_via' => $request->tipo_via,
                'nombre_via' => $request->nombre_via,
                'numero' => $request->numero,
            ],
            'simulado' => $simulado,
        ]);
    }

    /**
     * Genera datos simulados para demostración cuando la API falla
     * La implementación por dirección esta demorando el proyecto con datos reales, simular datos
     */
    private function obtenerDatosSimulados(Request $request): array
    {
        // Crear 2-3 resultados simulados basados en la búsqueda
        $resultados = [];

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
                    'ldt' => strtoupper("{$request->tipo_via} {$request->nombre_via} {$request->numero} "  . 
                        "Esc {$i} Pta A {$request->municipio} ({$request->provincia})" ."( EJEMPLO BUSQUEDA)"),
                    'dt' => [
                        'np' => strtoupper($request->provincia),
                        'nm' => strtoupper($request->municipio),
                    ],
                    'debi' => [
                        'luso' => $i == 1 ? 'Residencial' : 'Oficinas',
                        'sfc' => rand(50, 150),
                        'ant' => rand(10, 50),
                    ],
                     '_simulado' => true,    //  Flag para identificar simulados
                ]
            ];
        }

        // Referencias REALES conocidas que funcionan en la API
        $referenciasReales = [
            [
                'pc1' => '2749704',
                'pc2' => 'YJ0624N',
                'car' => '0001',
                'cc1' => 'DI',
                'cc2' => '',
                'direccion' => 'CL GUAYANA-MOJONERA 3',
                'uso' => 'Residencial',
                'superficie' => '57.00',
                'antiguedad' => '1975',
            ],
            [
                'pc1' => '3301204',
                'pc2' => 'QB6430S',
                'car' => '0008',
                'cc1' => 'QR',
                'cc2' => '',
                'direccion' => 'CL BRIHUEGA 6',
                'uso' => 'Residencial',
                'superficie' => '100.00',
                'antiguedad' => '1980',
            ],
        ];

        // $resultados = [];

        foreach ($referenciasReales as $i => $ref) {
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
                    'ldt' => strtoupper($ref['direccion'] . " (EJEMPLO DEMO)"),
                    'dt' => [
                        'np' => strtoupper($request->provincia),
                        'nm' => strtoupper($request->municipio),
                    ],
                    'debi' => [
                        'luso' => $ref['uso'],
                        'sfc' => $ref['superficie'],
                        'ant' => $ref['antiguedad'],
                    ],
                    '_simulado' => false, // Flag para identificar reales
                ]
            ];
        }

        return [
            'consulta_dnplocResult' => [
                'control' => [
                    'cudnp' => count($resultados),
                    'cucons' => count($resultados),
                ],
                'bico' => $resultados,
            ]
        ];
    }


    // Guardar - Solo Premium
    public function guardar(Request $request)
    {
        $datos = json_decode($request->raw_json, true);

        if (!$datos) {
            return back()->with('error', 'Datos invalidos.');
        }

        // // ✅ DEBUG: Ver estructura completa que llega
        // \Log::info('=== ESTRUCTURA COMPLETA ===');
        // \Log::info('Keys nivel 1: ' . json_encode(array_keys($datos)));

        // if (isset($datos['consulta_dnprcResult'])) {
        //     \Log::info('Tiene consulta_dnprcResult');
        //     $data = $datos['consulta_dnprcResult'];

        //     if (isset($data['bico']['bi']['dt']['locs'])) {
        //         \Log::info('locs: ' . json_encode($data['bico']['bi']['dt']['locs']));
        //     } else {
        //         \Log::info('NO TIENE locs');
        //     }
        // }

        $data = $datos['consulta_dnprcResult'];
        $bico = $data['bico'];
        $bi = $bico['bi'];

        $rc = $bi['idbi']['rc'];

        // Construir referencia correctamente
        $referencia = $rc['pc1'] . $rc['pc2'] . $rc['car'] . $rc['cc1'] . $rc['cc2'];

        // Datos localización
        $provinciaCodigo = $bi['dt']['loine']['cp'] ?? null;
        $municipioCodigo = $bi['dt']['cmc'] ?? null;
        $provinciaNombre = $bi['dt']['np'] ?? null;
        $municipioNombre = $bi['dt']['nm'] ?? null;

        // ✅ DEBUG: Ver qué trae $lourb
        $lourb = $bi['dt']['locs']['lous']['lourb'] ?? null;
        $dir   = $lourb['dir'] ?? [];
        $loint = $lourb['loint'] ?? [];

        // dd([
        //     'lourb_existe' => !is_null($lourb),
        //     'dir' => $dir,
        //     'loint' => $loint,
        //     'tv' => $dir['tv'] ?? 'NO EXISTE',
        //     'nv' => $dir['nv'] ?? 'NO EXISTE',
        //     'pnp' => $dir['pnp'] ?? 'NO EXISTE',
        // ]);

        // Crear provincia si no existe
        Provincia::firstOrCreate(
            ['codigo' => $provinciaCodigo],
            ['nombre' => $provinciaNombre]
        );

        // Crear municipio si no existe
        Municipio::firstOrCreate(
            ['codigo' => $municipioCodigo],
            ['nombre' => $municipioNombre, 'provincia_codigo' => $provinciaCodigo]
        );

        // Guardar propiedad
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

        // ==========================
        // Regenerar unidades constructivas
        // ==========================

        $propiedad->unidadesConstructivas()->delete();

        if (isset($bico['lcons'])) {
            foreach ($bico['lcons'] as $unidad) {
                $propiedad->unidadesConstructivas()->create([
                    'tipo_unidad'           => $unidad['lcd'] ?? null,
                    'tipologia'             => $unidad['dvcons']['dtip'] ?? null,
                    'superficie_m2'         => $unidad['dfcons']['stl'] ?? null,
                    'localizacion_externa'  => $unidad['dt']['lourb']['loint']['es'] ?? null,
                    'raw_json'              => $unidad,
                ]);
            }
        }

        return redirect()
            ->route('propiedades.show', $propiedad)
            ->with('success', 'Propiedad guardadd correctamente.');
    }

    // Hisorial de Busqueda
    public function historial()
    {
        $busquedas = Busqueda::where('usuario_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('propiedades.historial', compact('busquedas'));
    }

    // Registrar busquedas
    private function registrarBusqueda(
        string $tipo,
        string $query,
        ?string $referencia,
        int $resultados
    ): void {
        if (!auth()->check()) return;

        Busqueda::create([
            'usuario_id'        => auth()->id(),
            'query_text'        => $query,
            'referencia_busqueda' => $referencia,
            'params_json'         => ['tipo' => $tipo, 'query' => $query],
            'result_count'      => $resultados,
        ]);
    }

    //Contar resultados de direccion
    private function contarResultados(array $datos): int
    {
        $result = $datos['consulta_dnplocResult'] ?? [];
        $bicos = $result['bico'] ?? [];

        // Si es un solo resultado, viene como objeto
        if (isset($bicos['bi'])) {
            return 1;
        }

        // Si son múltiples, viene como array
        return count($bicos);
    }


    // FAVORITOS  (añadir O quitar)
    public function toggleFavorito(Propiedad $propiedad)
    {
        $favorito = $propiedad->favoritos()
            ->where('usuario_id', auth()->id())
            ->first();

        if ($favorito) {
            // Quitar de favoritos
            $favorito->delete();
            return back()->with('success', 'Propiedad eliminada de favoritos.');
        } else {
            // Añadir a favoritos
            $propiedad->favoritos()->create([
                'usuario_id' => auth()->id(),
            ]);
            return back()->with('success', 'Propiedad añadida a favoritos.');
        }
    }

    // Guardar nota
    public function guardarNota(Request $request, Propiedad $propiedad)
    {
        $request->validate([
            'contenido' => 'required|string|max:1000',
            'tipo' => 'required|in:privada,publica',
        ]);

        $propiedad->notas()->create([
            'usuario_id' => auth()->id(),
            'texto' => $request->contenido,
            'tipo' => $request->tipo,
        ]);

        return back()->with('success', 'Nota añadida correctamente.');
    }

    // Eliminar nota
    public function eliminarNota(Propiedad $propiedad, Nota $nota)
    {
        // Verificar que la nota pertenece al usuario
        if ($nota->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar esta nota.');
        }

        $nota->delete();

        return back()->with('success', 'Nota eliminada correctamente.');
    }

    public function testApi(Request $request, CatastroService $catastro)
    {
        $request->validate([
            'referencia' => 'required|string|min:14'
        ]);

        $datos = $catastro->consultarPorReferencia($request->referencia);

        dump($datos); // solo para comprobaciones y comprobar la estructura real
    }
}

//fin controlador    