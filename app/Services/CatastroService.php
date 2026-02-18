<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LogApi;


class CatastroService
{
    protected string $baseUrl = 'https://ovc.catastro.meh.es/OVCServWeb/OVCWcfCallejero/COVCCallejero.svc/json';

    // Consulta por Referncia Catastral
    public function consultarPorReferencia(string $referencia): array
    {
        // Limpiar espacios
        $referencia =  strtoupper(trim($referencia));

        // Validad formato de la referencia catastral RC
        if (!$this->validarReferenciaCatastral($referencia)) {
            throw new \InvalidArgumentException((
                'Formato de referencia catastral inválido. Debe tener de 14 a 20 caracteres.'
            ));
        }

        return $this->llamarApi('/Consulta_DNPRC', ['RefCat' => $referencia]);
    }

    // Consulta por Dirección (Premium)
    public function consultarPorDireccion(
        string $provincia,
        string $municipio,
        string $tipoVia,
        string $nombreVia,
        ?string $numero = null
    ): array {
        return $this->llamarApi('/Consulta_DNPLOC', [
            'Provincia' => strtoupper(trim($provincia)),
            'Municipio' => strtoupper(trim($municipio)),
            'TipoVia' => strtoupper(trim($tipoVia)),
            'NombreVia' => strtoupper(trim($nombreVia)),
            'Numero' => trim($numero),
        ]);
    }

    private function llamarApi(string $endpoint, array $params): array
    {
        $inicio = microtime(true);

        try {
            $response = Http::timeout(15)->get(
                "{$this->baseUrl}{$endpoint}",
                $params
            );

            $duracion =  (int)((microtime(true) - $inicio) * 1000);

            // Registrar en logs_api
            $this->registrarLog(
                endpoint: $endpoint,
                params: $params,
                responseCode: $response->status(),
                duracion: $duracion,
                responseJson: $response->body(),
            );

            if (!$response->successful()) {
                throw new \Exception(
                    "Error en la API del Catastro. Código: {$response->status()}"
                );
            }
            $datos = $response->json();

            // Verificando errros interno del Api
            if ($this->tieneErrorApi($datos)) {
                // Extraer mensaje de error específico si existe
                $lerr = $datos['consulta_dnprcResult']['lerr']
                    ?? $datos['consulta_dnplocResult']['lerr']
                    ?? null;

                $mensajeError = 'No se encontraron resultados.';

                if ($lerr && isset($lerr[0]['des'])) {
                    $mensajeError = $lerr[0]['des'];
                }
                throw new \Exception($mensajeError);
            }

            return $datos;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->registrarLog(
                endpoint: $endpoint,
                params: $params,
                responseCode: 0,
                duracion: (int)((microtime(true) - $inicio) * 1000),
                errorCode: 'CONNECTION_ERROR',
                errorDesc: $e->getMessage(),
            );

            throw new \Exception(
                'No se puede conectart con la Api del Catastro. Intentelo más tarde.'
            );
        }
    }

    // Validar formato RC -referencia catastral
    private function validarReferenciaCatastral(string $rc): bool
    {
        // RC urbana 14 caracteres base o 20 caracteres completa
        return preg_match('/^[0-9A-Z]{14}([0-9A-Z]{4}[A-Z]{2})?$/', $rc);
    }

    // Detectar error interno del Api
    private function tieneErrorApi(array $datos): bool
    {
        // la Api del catastro devuelve 200 aunque haya error
        $control = $datos['consulta_dnprcResult']['control']
            ?? $datos['consulta_dnplocResult']['control']
            ?? null;

        if (!$control) return true;

        // Si hay errores (cuerr > 0)
        if (isset($control['cuerr']) && (int)$control['cuerr'] > 0) {
            return true;
        }

        // cudnp = 0 significa sin resultados
        return isset($control['cudnp']) && (int)$control['cudnp'] === 0;
    }

    private function registrarLog(
        string $endpoint,
        array $params,
        int $responseCode,
        int $duracion,
        ?string $responseJson =  null,
        ?string $errorCode = null,
        ?string $errorDesc = null,
    ): void {
        try {
            LogApi::create([
                'usuario_id' => auth()->id(),
                'endpoint' => $endpoint,
                'params_json' => $params,
                'response_code' => $responseCode,
                'duration_ms' => $duracion,
                'response_json' => $responseJson,
                'error_code' => $errorCode,
                'error_desc' => $errorDesc,
            ]);
        } catch (\Exception $e) {
            // No se interrumple el flujo si falla el log
            Log::error('Error registrando log API: ' . $e->getMessage());
        }
    }
}
