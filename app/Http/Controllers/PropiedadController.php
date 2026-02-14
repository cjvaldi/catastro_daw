<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Propiedad;
use App\Services\CatastroService;
use Illuminate\Support\Facades\Auth;


class PropiedadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $propiedades = Propiedad::with(['provincia','municipio'])->paginate(10);
        return view('propiedades.index',compact('propiedades'));
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
        $propiedad->load(['provincia','municipio','unidades']);
        return view('propiedades.show',compact('propiedad'));
    }

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
            'referencia'=> 'required|string|min:14'
        ]);

        try {
            
            $datos = $catastro->consultarPorReferencia($request->referencia);

            // Json completo para almacenar datos
            $propiedad = Propiedad::updateOrCreate(
                ['referencia_catastral'=>$request->referencia],
                [
                    'clase'=>$datos['consulta_dnp'][' bico'][' bi'][' idbi']['cn'] ?? null,
                    'uso'=>$datos['consulta_dnp'][' bico'][' bi']['debi']['luso'] ?? null,
                    'superficie_m2'=>$datos['consulta_dnp']['bico']['bi']['debi']['sfc'] ?? null,
                    'antiguedad_anios'=>$datos['consulta_dnp']['bico']['bi']['debi']['ant'] ?? null,
                    'raw_json'=>json_encode($datos)
                ]
            );

            return redirect()->route('propiedades.show',$propiedad);

        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }
}
