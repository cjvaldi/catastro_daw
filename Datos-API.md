# 📊 ESTRUCTURA DE DATOS - API DEL CATASTRO

Documentación técnica sobre cómo se reciben y procesan los datos de la API oficial del Catastro Español.

---

## 🔍 1. ENDPOINTS UTILIZADOS

### 1.1. Consulta por Referencia Catastral

**Endpoint:** `/Consulta_DNPRC`

**Parámetros:**
```php
[
    'RefCat' => '2749704YJ0624N0001DI'  // Referencia catastral
]
```

**URL completa:**
```
https://ovc.catastro.meh.es/OVCServWeb/OVCWcfCallejero/COVCCallejero.svc/json/Consulta_DNPRC?RefCat=2749704YJ0624N0001DI
```

---

### 1.2. Consulta por Dirección (Localización)

**Endpoint:** `/Consulta_DNPLOC`

**Parámetros:**
```php
[
    'Provincia' => 'VALENCIA',
    'Municipio' => 'GODELLETA',
    'TipoVia' => 'CL',
    'NombreVia' => 'GUAYANA-MOJONERA',
    'Numero' => '3'
]
```

**URL completa:**
```
https://ovc.catastro.meh.es/OVCServWeb/OVCWcfCallejero/COVCCallejero.svc/json/Consulta_DNPLOC?Provincia=VALENCIA&Municipio=GODELLETA&TipoVia=CL&NombreVia=GUAYANA-MOJONERA&Numero=3
```

---

## 📦 2. ESTRUCTURA DE RESPUESTA JSON

### 2.1. Respuesta Exitosa por Referencia

La API devuelve un JSON con esta estructura:

```json
{
  "consulta_dnprcResult": {
    "control": {
      "cudnp": 1,          // 1 = Datos encontrados, 0 = Sin resultados
      "cucons": 1
    },
    "bico": {
      "bi": {
        "idbi": {
          "rc": {
            "pc1": "2749704",
            "pc2": "YJ0624N",
            "car": "0001",
            "cc1": "DI",
            "cc2": ""
          }
        },
        "dt": {
          "locs": {
            "lous": {
              "lourb": {
                "dir": {
                  "cv": "57",
                  "tv": "CL",
                  "nv": "GUAYANA-MOJONERA",
                  "pnp": "3"
                },
                "loint": {
                  "es": "T",
                  "pt": "OD",
                  "pu": "OS"
                },
                "dp": "46388",
                "dm": "1"
              }
            }
          },
          "np": "VALENCIA",
          "nm": "GODELLETA",
          "loine": {
            "cp": "46"
          }
        },
        "ldt": "CL GUAYANA-MOJONERA 3 46388 GODELLETA (VALENCIA)",
        "debi": {
          "luso": "Residencial",
          "sfc": "57.00",
          "ant": "1975"
        }
      },
      "lcons": [
        {
          "lcd": "VIVIENDA",
          "dtip": "VIVIENDA COLECTIVA",
          "dfcons": {
            "stl": "53.00"
          }
        },
        {
          "lcd": "ELEMENTOS COMUNES",
          "dtip": "N/A",
          "dfcons": {
            "stl": "4.00"
          }
        }
      ]
    }
  }
}
```

---

## 🔑 3. MAPEO DE CAMPOS - REFERENCIA CATASTRAL

### 3.1. Referencia Catastral Completa

```php
$bi = $datos['consulta_dnprcResult']['bico']['bi'];
$rc = $bi['idbi']['rc'];

// Construcción de la referencia completa
$referencia = $rc['pc1'] . $rc['pc2'] . $rc['car'] . $rc['cc1'] . $rc['cc2'];
// Resultado: "2749704YJ0624N0001DI"
```

**Componentes:**
- `pc1`: Parcela catastral 1 (7 dígitos)
- `pc2`: Parcela catastral 2 (7 caracteres alfanuméricos)
- `car`: Cargo (4 caracteres)
- `cc1`: Control 1 (2 letras)
- `cc2`: Control 2 (2 letras, opcional)

---

### 3.2. Provincia y Municipio

```php
$dt = $bi['dt'];

// Código de provincia
$provinciaCodigo = $dt['loine']['cp'];
// Resultado: "46"

// Nombre de provincia
$provinciaNombre = $dt['np'];
// Resultado: "VALENCIA"

// Código de municipio (calculado)
$municipioCodigo = $provinciaCodigo . $dt['cm'];
// Resultado: "46138" (46 + 138)

// Nombre de municipio
$municipioNombre = $dt['nm'];
// Resultado: "GODELLETA"
```

**Campos:**
- `dt['loine']['cp']`: Código de provincia (2 dígitos)
- `dt['np']`: Nombre de provincia
- `dt['cm']`: Código de municipio (3 dígitos)
- `dt['nm']`: Nombre de municipio

---

### 3.3. Dirección Completa

```php
$lourb = $dt['locs']['lous']['lourb'];
$dir = $lourb['dir'];

// Tipo de vía
$tipoVia = $dir['tv'];
// Resultado: "CL" (Calle)

// Nombre de vía
$nombreVia = $dir['nv'];
// Resultado: "GUAYANA-MOJONERA"

// Número de policía
$numero = $dir['pnp'];
// Resultado: "3"

// Código de vía
$codigoVia = $dir['cv'];
// Resultado: "57"
```

**Tipos de vía comunes:**
- `CL`: Calle
- `AV`: Avenida
- `PZ`: Plaza
- `PS`: Paseo
- `CM`: Camino
- `CR`: Carretera
- `TR`: Travesía

---

### 3.4. Localización Interior

```php
$loint = $lourb['loint'];

// Escalera
$escalera = $loint['es'];
// Resultado: "T"

// Planta
$planta = $loint['pt'];
// Resultado: "OD" (Planta baja o "Planta 0 Derecha")

// Puerta
$puerta = $loint['pu'];
// Resultado: "OS" (Sin especificar)

// Código postal
$codigoPostal = $lourb['dp'];
// Resultado: "46388"

// Distrito municipal
$distritoMunicipal = $lourb['dm'];
// Resultado: "1"
```

---

### 3.5. Dirección Texto Completa

```php
// Texto formateado completo (proporcionado por la API)
$direccionTexto = $bi['ldt'];
// Resultado: "CL GUAYANA-MOJONERA 3 46388 GODELLETA (VALENCIA)"
```

---

### 3.6. Datos del Bien Inmueble

```php
$debi = $bi['debi'];

// Uso del inmueble
$uso = $debi['luso'];
// Resultado: "Residencial"

// Superficie construida (m²)
$superficie = $debi['sfc'];
// Resultado: "57.00"

// Año de construcción / Antigüedad
$antiguedad = $debi['ant'];
// Resultado: "1975"
```

**Usos comunes:**
- `Residencial`: Vivienda
- `Oficinas`: Uso terciario
- `Industrial`: Uso industrial
- `Comercial`: Locales comerciales
- `Almacén`: Almacenamiento

---

### 3.7. Unidades Constructivas

```php
$lcons = $datos['consulta_dnprcResult']['bico']['lcons'];

// Iterar sobre cada unidad constructiva
foreach ($lcons as $unidad) {
    $tipoUnidad = $unidad['lcd'];
    // Resultado: "VIVIENDA" o "ELEMENTOS COMUNES"
    
    $tipologia = $unidad['dtip'];
    // Resultado: "VIVIENDA COLECTIVA" o "N/A"
    
    $superficieUnidad = $unidad['dfcons']['stl'];
    // Resultado: "53.00" o "4.00"
}
```

**Tipos de unidades:**
- `VIVIENDA`: Parte residencial
- `ELEMENTOS COMUNES`: Zonas comunes del edificio
- `LOCAL`: Locales comerciales
- `TRASTERO`: Anexos
- `GARAJE`: Plazas de garaje

---

## 🗺️ 4. MAPEO COMPLETO EN CÓDIGO

### 4.1. Ejemplo en PropiedadController

```php
public function guardar(Request $request, CatastroService $catastro)
{
    $referencia = $request->referencia;
    $datos = $catastro->consultarPorReferencia($referencia);
    
    // Navegación por la estructura JSON
    $bi = $datos['consulta_dnprcResult']['bico']['bi'];
    $rc = $bi['idbi']['rc'];
    $dt = $bi['dt'];
    $lourb = $dt['locs']['lous']['lourb'];
    $dir = $lourb['dir'];
    $loint = $lourb['loint'];
    $debi = $bi['debi'];
    
    // Extraer datos
    $refCompleta = $rc['pc1'] . $rc['pc2'] . $rc['car'] . $rc['cc1'] . $rc['cc2'];
    $provinciaCodigo = $dt['loine']['cp'];
    $provinciaNombre = strtoupper($dt['np']);
    $municipioCodigo = $provinciaCodigo . $dt['cm'];
    $municipioNombre = strtoupper($dt['nm']);
    
    // Crear propiedad
    Propiedad::create([
        'user_id' => auth()->id(),
        'referencia_catastral' => $refCompleta,
        'clase' => 'UR', // Urbano
        'provincia_codigo' => $provinciaCodigo,
        'municipio_codigo' => $municipioCodigo,
        'provincia' => $provinciaNombre,
        'municipio' => $municipioNombre,
        'direccion_text' => $bi['ldt'],
        'tipo_via' => $dir['tv'] ?? null,
        'nombre_via' => $dir['nv'] ?? null,
        'numero' => $dir['pnp'] ?? null,
        'escalera' => $loint['es'] ?? null,
        'planta' => $loint['pt'] ?? null,
        'puerta' => $loint['pu'] ?? null,
        'codigo_postal' => $lourb['dp'] ?? null,
        'uso' => $debi['luso'] ?? null,
        'superficie_m2' => (float)($debi['sfc'] ?? 0),
        'antiguedad_anios' => $debi['ant'] 
            ? (date('Y') - (int)$debi['ant']) 
            : null,
        'raw_json' => json_encode($datos),
    ]);
}
```

---

## 🛡️ 5. MANEJO DE ERRORES

### 5.1. Respuesta con Error

```json
{
  "consulta_dnprcResult": {
    "control": {
      "cudnp": 0,        // 0 = Sin resultados
      "cuerr": 1         // 1 = Error
    },
    "lerr": [
      {
        "cod": "41",
        "des": "EL NÚMERO ES OBLIGATORIO."
      }
    ]
  }
}
```

### 5.2. Detección de errores en código

```php
private function tieneErrorApi(array $datos): bool
{
    $control = $datos['consulta_dnprcResult']['control'] 
        ?? $datos['consulta_dnplocResult']['control'] 
        ?? null;
    
    if (!$control) return true;
    
    // Si cuerr > 0, hay error
    if (isset($control['cuerr']) && (int)$control['cuerr'] > 0) {
        return true;
    }
    
    // Si cudnp = 0, sin resultados
    return isset($control['cudnp']) && (int)$control['cudnp'] === 0;
}
```

---

## 📋 6. CAMPOS OPCIONALES Y VALORES NULOS

**Campos que pueden ser NULL:**

```php
// SIEMPRE validar con operador null-coalescing (??)
$escalera = $loint['es'] ?? null;
$planta = $loint['pt'] ?? null;
$puerta = $loint['pu'] ?? null;
$numero = $dir['pnp'] ?? null;
$codigoPostal = $lourb['dp'] ?? null;
```

**Razones por las que pueden ser NULL:**
- Inmuebles sin división interior (sin escalera/planta/puerta)
- Direcciones sin número de policía
- Zonas rurales sin código postal asignado

---

## 🔄 7. BÚSQUEDA POR DIRECCIÓN - DIFERENCIAS

### 7.1. Estructura similar pero con múltiples resultados

```json
{
  "consulta_dnplocResult": {
    "control": {
      "cudnp": 3,  // Número de resultados
      "cucons": 3
    },
    "bico": [
      { "bi": { /* Propiedad 1 */ } },
      { "bi": { /* Propiedad 2 */ } },
      { "bi": { /* Propiedad 3 */ } }
    ]
  }
}
```

### 7.2. Diferencia clave

**Referencia:** `bico.bi` (objeto único)  
**Dirección:** `bico[]` (array de objetos)

```php
// Consulta por referencia
$bi = $datos['consulta_dnprcResult']['bico']['bi'];

// Consulta por dirección
$bicos = $datos['consulta_dnplocResult']['bico'];
foreach ($bicos as $bico) {
    $bi = $bico['bi'];
    // Procesar cada resultado...
}
```

---

## 📊 8. TABLA RESUMEN DE CAMPOS

| Campo | Ruta JSON | Ejemplo | Tipo | Obligatorio |
|-------|-----------|---------|------|-------------|
| **Ref. Catastral** | `bico.bi.idbi.rc.*` | 2749704YJ0624N0001DI | string | ✅ |
| **Provincia Código** | `bi.dt.loine.cp` | 46 | string | ✅ |
| **Provincia Nombre** | `bi.dt.np` | VALENCIA | string | ✅ |
| **Municipio Código** | `cp + dt.cm` | 46138 | string | ✅ |
| **Municipio Nombre** | `bi.dt.nm` | GODELLETA | string | ✅ |
| **Tipo Vía** | `lourb.dir.tv` | CL | string | ✅ |
| **Nombre Vía** | `lourb.dir.nv` | GUAYANA-MOJONERA | string | ✅ |
| **Número** | `lourb.dir.pnp` | 3 | string | ⚠️ |
| **Escalera** | `lourb.loint.es` | T | string | ❌ |
| **Planta** | `lourb.loint.pt` | OD | string | ❌ |
| **Puerta** | `lourb.loint.pu` | OS | string | ❌ |
| **Código Postal** | `lourb.dp` | 46388 | string | ⚠️ |
| **Dirección Texto** | `bi.ldt` | CL GUAYANA... | string | ✅ |
| **Uso** | `bi.debi.luso` | Residencial | string | ✅ |
| **Superficie** | `bi.debi.sfc` | 57.00 | float | ✅ |
| **Año Construcción** | `bi.debi.ant` | 1975 | int | ⚠️ |

**Leyenda:**
- ✅ Obligatorio: Siempre presente
- ⚠️ Opcional: Puede estar ausente
- ❌ Muy opcional: Frecuentemente ausente

---

## 🔗 9. REFERENCIAS ÚTILES

### Documentación Oficial
- **API del Catastro:** https://www.catastro.minhap.es/webinspire/index.html
- **Especificación Técnica:** https://www.catastro.minhap.es/ayuda/lang/castellano/ayuda_webservices.htm

### Ejemplos de Referencias Catastrales
```
Urbanas:
- 2749704YJ0624N0001DI (Godelleta, Valencia)
- 3301204QB6430S0008QR (San Juan de Aznalfarache, Sevilla)

Rústicas:
- 30024A00600088 (Ejemplo genérico)
```

---

## 🛠️ 10. DEBUGGING - CÓMO VER LA ESTRUCTURA COMPLETA

### En Tinker

```bash
php artisan tinker

>>> $service = app(\App\Services\CatastroService::class);
>>> $datos = $service->consultarPorReferencia('2749704YJ0624N0001DI');
>>> print_r($datos);  // Ver estructura completa
>>> dd($datos);       // Dump and die
```

### En el Código

```php
// Guardar JSON completo en archivo para inspección
file_put_contents(
    storage_path('app/catastro_debug.json'),
    json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);
```

---

## ✅ CONCLUSIÓN

Esta estructura de datos es **consistente** en la API del Catastro, pero siempre se debe:

1. ✅ Validar con operador `??` para campos opcionales
2. ✅ Verificar errores con `control['cuerr']` y `control['cudnp']`
3. ✅ Guardar `raw_json` completo para debug
4. ✅ Usar `firstOrCreate` para provincias/municipios

---

**Fecha de actualización:** Febrero 2026  
**Autor:** CJVV-Proyecto CatastroApp  
**Versión API:** Actual del Catastro Español