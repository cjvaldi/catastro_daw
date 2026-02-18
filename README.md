# 🏠 CatastroApp - Sistema de Consulta Catastral

Sistema web para consultar información catastral de propiedades en España utilizando la API oficial del Catastro.

## 🚀 Características

- 🔍 **Búsqueda por referencia catastral** (pública)
- 📍 **Búsqueda por dirección** (Premium)
- 💾 **Guardar propiedades** favoritas
- 📝 **Sistema de notas** privadas/públicas
- 📊 **Historial** de búsquedas con paginación
- 🖨️ **Impresión** de fichas A4 profesionales
- 🔧 **Panel de administración** completo
- ⭐ **Sistema Freemium** (Visitante/Premium/Admin)

## 🛠️ Tecnologías

- **Backend:** Laravel 11
- **Base de datos:** MySQL 8.0
- **Frontend:** HTML5, CSS3 puro (sin frameworks)
- **Autenticación:** Laravel Breeze
- **API Externa:** API REST del Catastro Español

## 📋 Requisitos del Sistema

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 8.0
- Git

## 🔧 Instalación

### 1. Clonar el repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd catastro_daw
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar variables de entorno
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar base de datos

Edita el archivo `.env` con tus credenciales de MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=catastro_daw
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 5. Crear base de datos
```bash
# Crear la base de datos en MySQL
mysql -u root -p
CREATE DATABASE catastro_daw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 6. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

Este comando creará:
- ✅ Todas las tablas necesarias
- ✅ Provincias 
- ✅ Municipios 
- ✅ 3 usuarios de prueba (ver abajo)
- ✅ 4 propiedades de prueba 

### 7. Iniciar el servidor
```bash
php artisan serve
```

Accede en: **http://127.0.0.1:8000**

---

## 👥 Usuarios de Prueba

El seeder crea automáticamente estos usuarios:

| Email | Contraseña | Rol | Permisos |
|-------|------------|-----|----------|
| admin@catastro.test | Admin1234! | **Administrador** | Todos los permisos + Panel Admin |
| premium@catastro.test | Premium1234! | **Premium** | Búsqueda avanzada + Favoritos + Notas |
| visitante@catastro.test | Visitante1234! | **Visitante** | Búsqueda básica + Guardar propiedades |

---

## 📂 Estructura del Proyecto
```
catastro_daw/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PropiedadController.php
│   │   │   ├── Admin/AdminController.php
│   │   │   └── UpgradeController.php
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── CheckActivo.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Propiedad.php
│   │   ├── Favorito.php
│   │   ├── Nota.php
│   │   ├── Busqueda.php
│   │   └── LogApi.php
│   └── Services/
│       └── CatastroService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/views/
│   ├── layouts/
│   ├── auth/
│   ├── propiedades/
│   └── admin/
├── public/
│   ├── css/
│   │   └── app.css
│   └── favicon.ico
└── routes/
    └── web.php
```

---

## 🎯 Funcionalidades por Rol

### 🌐 Anónimo (Sin registro)
- Búsqueda por referencia catastral
- Ver información básica de propiedades

### 👤 Visitante (Registro gratuito)
- Todo lo de Anónimo +
- Guardar propiedades
- Ver historial de búsquedas
- Imprimir fichas A4

### ⭐ Premium (Upgrade gratuito)
- Todo lo de Visitante +
- Búsqueda por dirección (calle, número, municipio)
- Marcar propiedades como favoritas
- Crear notas privadas o públicas
- Filtrar: Todas / Solo Favoritas

### 🔧 Administrador
- Todo lo de Premium +
- Dashboard con estadísticas
- Gestionar usuarios (cambiar roles, activar/desactivar)
- Ver logs de API con métricas
- Monitoreo completo del sistema

---

## 📖 Documentación Adicional

- **Manual de Uso:** Disponible en `/manual` dentro de la aplicación
- **Comparativa del Proyecto:** Ver `COMPARATIVA_PROYECTO.md`

---

## ⚠️ Limitaciones Conocidas

### API del Catastro
La API pública del Catastro (`Consulta_DNPLOC`) tiene restricciones no documentadas en la búsqueda por dirección.

**Solución implementada:**
- Sistema híbrido: intenta API real primero
- Fallback: muestra datos de ejemplo + referencias reales
- Los usuarios ven claramente qué datos son simulados

### Verificación de Email
Las vistas están preparadas pero la verificación por email no está activada en desarrollo local.

---

## 🔒 Seguridad

- ✅ Autenticación con Laravel Breeze
- ✅ Middleware de roles personalizado
- ✅ Protección CSRF en formularios
- ✅ Validación de datos en servidor
- ✅ Passwords hasheados con Bcrypt
- ✅ Logs de todas las llamadas API

---

## 🧪 Limpieza y Mantenimiento
```bash
# Limpiar caché
php artisan optimize:clear

# Regenerar base de datos (CUIDADO: borra todos los datos)
php artisan migrate:fresh --seed

# Ver rutas disponibles
php artisan route:list

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 🚀 Despliegue en Producción

### Antes de subir a hosting:

1. **Configurar `.env` para producción:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

2. **Optimizar rendimiento:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Asegurar permisos:**
```bash
chmod -R 775 storage bootstrap/cache

win
attrib -r bootstrap\cache /s /d
```

---

## 🤝 Contribuir

Este es un proyecto académico. Pull requests son bienvenidos para mejoras.

---

## 📄 Licencia

Proyecto académico - Uso educativo - DAW 2026

---

## 👨‍💻 Autor

Cristian Valdivieso Valenzuela - Proyecto Desarrollo Aplicaciones Web

---

## 🎓 Contexto Académico

Proyecto desarrollado como parte del módulo de **Desarrollo de Aplicaciones Web** utilizando:
- ✅ Laravel 11 (framework PHP)
- ✅ HTML5 y CSS3 puro (sin frameworks CSS)
- ✅ MySQL (base de datos relacional)
- ✅ API REST del Catastro Español
- ✅ Git para control de versiones

---

**Estado:** ✅ Funcional y Completado  
**Calidad:** ⭐⭐⭐⭐⭐ Profesional  
**Documentación:** 📚 Completa