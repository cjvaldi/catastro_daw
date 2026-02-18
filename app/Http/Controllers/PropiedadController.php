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
        $query->whereHas('favoritos', function($q) {
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
    public function buscarPorDireccion(Request $request, CatastroService $catastro)
    {
        $request->validate([
            'provincia'  => 'required|string|max:100',
            'municipio'  => 'required|string|max:100',
            'tipo_via'   => 'required|string|max:10',
            'nombre_via' => 'required|string|max:200',
            'numero'     => 'nullable|string|max:10',
        ]);

        try {
            $datos = $catastro->consultarPorDireccion(
                provincia: $request->provincia,
                municipio: $request->municipio,
                tipoVia: $request->tipo_via,
                nombreVia: $request->nombre_via,
                numero: $request->numero ?? '',
            );

            // Registrar búsqueda
            $this->registrarBusqueda(
                tipo: 'direccion',
                query: "{$request->tipo_via} {$request->nombre_via} {$request->numero}, {$request->municipio}",
                referencia: null,
                resultados: $this->contarResultados($datos)
            );

            return view('propiedades.preview-direccion', [
                'datos'  => $datos,
                'filtro' => $request->all(),
                'tipo'   => 'direccion',
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
        return isset($datos['consulta_dnplocResult']['bico'])
            ? count((array)$datos['consulta_dnplocResult']['bico'])
            : 0;
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