<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LogApi;

/**
 * Servicio para interactuar con la API oficial del Catastro Español
 * 
 * Este servicio proporciona métodos para consultar información catastral
 * de propiedades inmobiliarias utilizando la API REST del Catastro.
 * Incluye manejo de errores, logging automático y validaciones.
 * 
 * @package App\Services
 * @author Cristian Valdivieso
 * @version 1.0
 * @link https://www.catastro.minhap.es/webinspire/index.html Documentación API Catastro
 */
class CatastroService
{
    /**
     * URL base de la API del Catastro
     * 
     * @var string
     */
    protected string $baseUrl = 'https://ovc.catastro.meh.es/OVCServWeb/OVCWcfCallejero/COVCCallejero.svc/json';

    /**
     * Consulta información de una propiedad por su referencia catastral
     * 
     * Realiza una consulta al endpoint Consulta_DNPRC de la API del Catastro
     * para obtener todos los datos de una propiedad identificada por su
     * referencia catastral única.
     * 
     * La referencia catastral puede tener:
     * - 14 caracteres (formato base): Identificación básica del inmueble
     * - 20 caracteres (formato completo): Incluye dígitos de control adicionales
     * 
     * @param string $referencia Referencia catastral del inmueble (14 o 20 caracteres alfanuméricos)
     * 
     * @return array Datos completos del inmueble en formato JSON decodificado
     * 
     * @throws \InvalidArgumentException Si el formato de la referencia es inválido
     * @throws \Exception Si la API no responde o devuelve un error
     * 
     * @example
     * $datos = $catastro->consultarPorReferencia('2749704YJ0624N0001DI');
     * // Retorna array con toda la información catastral del inmueble
     */
    public function consultarPorReferencia(string $referencia): array
    {
        // Normalizar entrada: eliminar espacios y convertir a mayúsculas
        $referencia = strtoupper(trim($referencia));

        // Validar formato antes de llamar a la API
        if (!$this->validarReferenciaCatastral($referencia)) {
            throw new \InvalidArgumentException(
                'Formato de referencia catastral inválido. Debe tener 14 o 20 caracteres alfanuméricos.'
            );
        }

        return $this->llamarApi('/Consulta_DNPRC', ['RefCat' => $referencia]);
    }

    /**
     * Consulta propiedades por dirección postal (función Premium)
     * 
     * Realiza una consulta al endpoint Consulta_DNPLOC de la API del Catastro
     * para buscar inmuebles según su ubicación física. Puede devolver múltiples
     * resultados si hay varios inmuebles en la dirección especificada.
     * 
     * NOTA: Esta API tiene limitaciones conocidas y puede no devolver resultados
     * incluso para direcciones válidas. Se recomienda usar búsqueda por referencia
     * cuando sea posible.
     * 
     * @param string $provincia Nombre de la provincia (ej: "VALENCIA")
     * @param string $municipio Nombre del municipio (ej: "GODELLETA")
     * @param string $tipoVia Tipo de vía (ej: "CL" para calle, "AV" para avenida)
     * @param string $nombreVia Nombre de la vía sin tipo (ej: "MAYOR")
     * @param string|null $numero Número de policía (opcional, ej: "3", "25B")
     * 
     * @return array Array con uno o más inmuebles encontrados
     * 
     * @throws \Exception Si la API no responde o devuelve un error
     * 
     * @example
     * $datos = $catastro->consultarPorDireccion(
     *     'VALENCIA',
     *     'GODELLETA',
     *     'CL',
     *     'GUAYANA-MOJONERA',
     *     '3'
     * );
     */
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
            'Numero' => $numero ? trim($numero) : null,
        ]);
    }

    /**
     * Realiza la llamada HTTP a la API del Catastro y gestiona la respuesta
     * 
     * Método privado que centraliza toda la lógica de comunicación con la API:
     * - Realiza la petición HTTP GET con timeout de 15 segundos
     * - Registra métricas de rendimiento (duración en ms)
     * - Guarda logs detallados en base de datos para auditoría
     * - Maneja errores HTTP y errores internos de la API
     * - Extrae y propaga mensajes de error específicos
     * 
     * @param string $endpoint Ruta del endpoint (ej: '/Consulta_DNPRC')
     * @param array $params Parámetros de la consulta como array asociativo
     * 
     * @return array Respuesta JSON decodificada de la API
     * 
     * @throws \Exception Si hay error HTTP, error de conexión, o error en la respuesta de la API
     * 
     * @internal
     */
    private function llamarApi(string $endpoint, array $params): array
    {
        // Marcar tiempo de inicio para métricas de rendimiento
        $inicio = microtime(true);

        try {
            // Realizar petición HTTP con timeout de 15 segundos
            $response = Http::timeout(15)->get(
                "{$this->baseUrl}{$endpoint}",
                $params
            );

            // Calcular duración de la petición en milisegundos
            $duracion = (int)((microtime(true) - $inicio) * 1000);

            // Registrar todas las llamadas para auditoría y monitoreo
            $this->registrarLog(
                endpoint: $endpoint,
                params: $params,
                responseCode: $response->status(),
                duracion: $duracion,
                responseJson: $response->body(),
            );

            // Verificar código de respuesta HTTP
            if (!$response->successful()) {
                throw new \Exception(
                    "Error en la API del Catastro. Código HTTP: {$response->status()}"
                );
            }

            $datos = $response->json();

            // La API del Catastro devuelve HTTP 200 incluso con errores internos
            // Por eso es necesario verificar el campo 'control' en la respuesta
            if ($this->tieneErrorApi($datos)) {
                // Intentar extraer mensaje de error específico de la API
                $lerr = $datos['consulta_dnprcResult']['lerr']
                    ?? $datos['consulta_dnplocResult']['lerr']
                    ?? null;

                $mensajeError = 'No se encontraron resultados para los datos proporcionados.';

                // Si la API proporciona un mensaje más específico, usarlo
                if ($lerr && isset($lerr[0]['des'])) {
                    $mensajeError = $lerr[0]['des'];
                }

                throw new \Exception($mensajeError);
            }

            return $datos;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Error de conexión: servidor no alcanzable o timeout de red
            $duracion = (int)((microtime(true) - $inicio) * 1000);

            $this->registrarLog(
                endpoint: $endpoint,
                params: $params,
                responseCode: 0,
                duracion: $duracion,
                errorCode: 'CONNECTION_ERROR',
                errorDesc: $e->getMessage(),
            );

            throw new \Exception(
                'No se puede conectar con la API del Catastro. Por favor, inténtelo más tarde.'
            );
        }
    }

    /**
     * Valida el formato de una referencia catastral española
     * 
     * Las referencias catastrales españolas tienen dos formatos válidos:
     * 
     * - Formato base (14 caracteres): 7 dígitos + 7 caracteres alfanuméricos
     *   Ejemplo: 2749704YJ0624N
     * 
     * - Formato completo (20 caracteres): Formato base + 4 caracteres + 2 letras de control
     *   Ejemplo: 2749704YJ0624N0001DI
     * 
     * Solo se permiten dígitos (0-9) y letras mayúsculas (A-Z).
     * 
     * @param string $rc Referencia catastral a validar
     * 
     * @return bool True si el formato es válido, false en caso contrario
     * 
     * @internal
     */
    private function validarReferenciaCatastral(string $rc): bool
    {
        // Expresión regular que valida:
        // - 14 caracteres alfanuméricos obligatorios
        // - Opcionalmente, 4 caracteres alfanuméricos + 2 letras más
        return preg_match('/^[0-9A-Z]{14}([0-9A-Z]{4}[A-Z]{2})?$/', $rc);
    }

    /**
     * Detecta si la respuesta de la API contiene un error interno
     * 
     * La API del Catastro siempre devuelve HTTP 200, incluso cuando hay errores.
     * Los errores se detectan analizando el objeto 'control' en la respuesta JSON:
     * 
     * - control.cuerr > 0: Hay errores en la petición
     * - control.cudnp = 0: No se encontraron resultados
     * - control ausente: Estructura de respuesta inválida
     * 
     * @param array $datos Array JSON decodificado de la respuesta de la API
     * 
     * @return bool True si hay error, false si la respuesta es válida
     * 
     * @internal
     */
    private function tieneErrorApi(array $datos): bool
    {
        // Buscar el objeto 'control' en ambos tipos de respuesta
        $control = $datos['consulta_dnprcResult']['control']
            ?? $datos['consulta_dnplocResult']['control']
            ?? null;

        // Si no hay objeto control, la respuesta es inválida
        if (!$control) {
            return true;
        }

        // cuerr > 0 indica errores de validación o procesamiento
        if (isset($control['cuerr']) && (int)$control['cuerr'] > 0) {
            return true;
        }

        // cudnp = 0 indica que no se encontraron resultados
        return isset($control['cudnp']) && (int)$control['cudnp'] === 0;
    }

    /**
     * Registra información detallada de cada llamada a la API en la base de datos
     * 
     * Guarda métricas y datos de cada petición para:
     * - Auditoría y trazabilidad
     * - Monitoreo de rendimiento (duración en ms)
     * - Detección de patrones de error
     * - Análisis de uso por usuario
     * 
     * Si el registro falla, se loguea el error pero no se interrumpe el flujo
     * de la aplicación (fail-safe).
     * 
     * @param string $endpoint Endpoint consultado (ej: '/Consulta_DNPRC')
     * @param array $params Parámetros enviados en la petición
     * @param int $responseCode Código de respuesta HTTP (ej: 200, 404, 500)
     * @param int $duracion Tiempo de respuesta en milisegundos
     * @param string|null $responseJson Cuerpo completo de la respuesta (opcional)
     * @param string|null $errorCode Código de error interno si aplica (opcional)
     * @param string|null $errorDesc Descripción del error si aplica (opcional)
     * 
     * @return void
     * 
     * @internal
     */
    private function registrarLog(
        string $endpoint,
        array $params,
        int $responseCode,
        int $duracion,
        ?string $responseJson = null,
        ?string $errorCode = null,
        ?string $errorDesc = null,
    ): void {
        try {
            LogApi::create([
                'usuario_id' => auth()->id(), // Null para usuarios anónimos
                'endpoint' => $endpoint,
                'params_json' => $params,
                'response_code' => $responseCode,
                'duration_ms' => $duracion,
                'response_json' => $responseJson,
                'error_code' => $errorCode,
                'error_desc' => $errorDesc,
            ]);
        } catch (\Exception $e) {
            // No interrumpir el flujo si falla el logging
            // Solo registrar el error en logs de Laravel para debug
            Log::error('Error al registrar log de API del Catastro: ' . $e->getMessage());
        }
    }
}