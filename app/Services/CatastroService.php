<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CatastroService
{
    protected string $baseUrl = 'https://ovc.catastro.meh.es/OVCServWeb/OVCWcfCallejero/COVCCallejero.svc/json';

    public function consultarPorReferencia(string $referencia)
    {
        $response = Http::timeout(15)->get(
            "{$this->baseUrl}/Consulta_DNPRC",
            ['RefCat' => $referencia]
        );

        if (!$response->successful()) {
            throw new \Exception('Error al consulta la API del catastro');
        }
        // dd($response->json());  // ver datos entrantes
        return $response->json();
    }
}
