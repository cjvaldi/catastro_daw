<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CatastroService;
use Illuminate\Support\Facades\Auth;
use App\Models\Propiedad;



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
        // if (!auth()->check() || auth()->user()->rol === 'visitante') {
        //     abort(403, 'Solo usuarios registrados pueden grabar los datos');
        // }
        if (!in_array(auth()->user()->rol, ['admin', 'registrado'])) {
            abort(403);
        }

        // Json completo para almacenar datos
        $datos = json_decode($request->raw_json, true);

        $data = $datos['consulta_dnprcResult'];
        $bi = $data['bico']['bi'];   // Datos principales

        $propiedad = Propiedad::updateOrCreate(
            ['referencia_catastral' => $request->referencia],
            [
                'clase' => $bi['idbi']['cn'] ?? null,
                'provincia' => $bi['dt']['np'] ?? null,
                'municipio' => $bi['dt']['nm'] ?? null,
                'direccion_text' => $bi['ldt'] ?? null,
                'uso' => $bi['debi']['luso'] ?? null,
                'superficie_m2' => (float)($bi['debi']['sfc'] ?? 0),
                'coef_participacion' => str_replace(',', '.', $bi['debi']['cpt'] ?? null),
                'antiguedad_anios' => $bi['debi']['ant'] ?? null,
                'raw_json' => json_encode($datos)
            ]
        );

        // guardar en Unidades constructivas
        // evita duplicados
        $propiedad->unidadesConstructivas()->delete();

        if (isset($data['bico']['Icons'])) {
            foreach ($data['bico']['Icons'] as $unidad) {
                $propiedad->unidadesConstructivas()->create([
                    'tipo_unidad' => $unidad['lcd'] ?? null,
                    'tipologia' => null,
                    'superficie_m2' => null,
                    'localizacion_externa' => null,
                    'raw_json' => json_encode($unidad)
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
