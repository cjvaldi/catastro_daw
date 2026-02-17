<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CatastroService;
use Illuminate\Support\Facades\Auth;
use App\Models\Propiedad;
use App\Models\Municipio;
use App\Models\Provincia;



class PropiedadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $propiedades = Propiedad::with(['provincia', 'municipio'])->paginate(10);
        return view('propiedades.index', compact('propiedades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Propiedad $propiedad)
    {
        // dump($propiedad);
        $propiedad->load(['provincia', 'municipio', 'unidadesConstructivas']);
        return view('propiedades.show', compact('propiedad'));
    }

    /**
     * Me permite ver los datos que se envian
     */
    // public function show($id)
    // {
    //     $propiedad = Propiedad::findOrFail($id);
    //     dd($propiedad);
    // }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function buscar(Request $request, CatastroService $catastro)
    {
        $request->validate([
            'referencia' => 'required|string|min:14'
        ]);
        //dd(auth()->user()->rol ?? 'invitado'); // para conprobar si llega el usuario
        try {

            $datos = $catastro->consultarPorReferencia($request->referencia);

            return view('propiedades.preview', [
                'datos' => $datos,
                'referencia' => $request->referencia
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function guardar(Request $request)
{
    if (!in_array(auth()->user()->rol, ['admin','registrado'])) {
        abort(403);
    }

    $datos = json_decode($request->raw_json, true);

    $data = $datos['consulta_dnprcResult'];
    $bico = $data['bico'];
    $bi = $bico['bi'];

    // Construir referencia correctamente
    $rc = $bi['idbi']['rc'];
    $referencia =
        $rc['pc1'] .
        $rc['pc2'] .
        $rc['car'] .
        $rc['cc1'] .
        $rc['cc2'];

    // Datos localización
    $provinciaCodigo = $bi['dt']['loine']['cp'] ?? null;
    $municipioCodigo = $bi['dt']['cmc'] ?? null;
    

    $provinciaNombre = $bi['dt']['np'] ?? null;
    $municipioNombre = $bi['dt']['nm'] ?? null;

    // Crear provincia si no existe
    Provincia::firstOrCreate(
        ['codigo' => $provinciaCodigo],
        ['nombre' => $provinciaNombre]
    );

    // Crear municipio si no existe
    Municipio::firstOrCreate(
        ['codigo' => $municipioCodigo],
        [
            'nombre' => $municipioNombre,
            'provincia_codigo' => $provinciaCodigo
        ]
    );

    // Guardar propiedad
    $propiedad = Propiedad::updateOrCreate(
        ['referencia_catastral' => $referencia,
        'user_id' => auth()->id()],
        [
            'user_id' => auth()->id(),
            'clase' => $bi['idbi']['cn'] ?? null,
            'provincia_codigo' => $provinciaCodigo,
            'municipio_codigo' => $municipioCodigo,
            'provincia' => $provinciaNombre,
            'municipio' => $municipioNombre,
            'direccion_text' => $bi['ldt'] ?? null,
            'uso' => $bi['debi']['luso'] ?? null,
            'superficie_m2' => (float)($bi['debi']['sfc'] ?? 0),
            'coef_participacion' =>
                str_replace(',', '.', $bi['debi']['cpt'] ?? null),
            'antiguedad_anios' => $bi['debi']['ant'] ?? null,
            'raw_json' => $datos // 👈 guardar como array
        ]
    );

    // ==========================
    // UNIDADES CONSTRUCTIVAS
    // ==========================

    $propiedad->unidadesConstructivas()->delete();

    if (isset($bico['lcons'])) {

        foreach ($bico['lcons'] as $unidad) {

            $propiedad->unidadesConstructivas()->create([
                'tipo_unidad' => $unidad['lcd'] ?? null,
                'tipologia' => $unidad['dvcons']['dtip'] ?? null,
                'superficie_m2' => $unidad['dfcons']['stl'] ?? null,
                'localizacion_externa' =>
                    $unidad['dt']['lourb']['loint']['es'] ?? null,
                'raw_json' => $unidad
            ]);
        }
    }

    return redirect()->route('propiedades.show', $propiedad);
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
