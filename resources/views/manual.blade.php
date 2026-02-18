@extends('layouts.app')

@section('title', 'Manual de Uso - Catastro DAW')

@section('content')
    <div class="container-narrow">
        <div class="card">
            <h2 class="card-header">📖 Manual de Uso - Catastro DAW</h2>

            <div class="info-box info-box-blue">
                <strong>ℹ️ Guía completa del sistema</strong>
                <p>Todo lo que necesitas saber para usar CatastroApp.</p>
            </div>

            {{-- SECCIÓN GENERAL (TODOS LOS USUARIOS) --}}
            
            {{-- 1. INTRODUCCIÓN --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #1f2937;">
                1. 📋 Introducción
            </h3>
            <p>
                <strong>CatastroApp</strong> es una aplicación web que permite consultar información 
                catastral de propiedades en España utilizando la API oficial del Catastro. 
                Puedes buscar inmuebles por referencia catastral o por dirección (Premium), 
                guardar tus propiedades favoritas y gestionar notas.
            </p>

            {{-- 2. TIPOS DE USUARIO --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #1f2937;">
                2. 👥 Tipos de Usuario 
            </h3>
            
            <div class="grid grid-2" style="gap: 16px; margin-top: 16px;">
                <div class="card" style="background: #f0f9ff;">
                    <h4 style="margin-bottom: 8px;">🌐 Anónimo</h4>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8; font-size: 14px;">
                        <li>Buscar por referencia catastral</li>
                        <li>Ver información básica de propiedades</li>
                    </ul>
                </div>

                <div class="card" style="background: #eff6ff;">
                    <h4 style="margin-bottom: 8px;">👤 Visitante (Gratis)</h4>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8; font-size: 14px;">
                        <li>Todo lo de Anónimo</li>
                        <li>Guardar propiedades</li>
                        <li>Ver historial de búsquedas</li>
                        <li>Imprimir fichas</li>
                    </ul>
                </div>

                <div class="card" style="background: #fef3c7;">
                    <h4 style="margin-bottom: 8px;">⭐ Premium</h4>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8; font-size: 14px;">
                        <li>Todo lo de Visitante</li>
                        <li>Búsqueda por dirección</li>
                        <li>Marcar favoritos</li>
                        <li>Crear notas privadas/públicas</li>
                        <li>Filtros avanzados</li>
                    </ul>
                </div>

                <div class="card" style="background: #fee2e2;">
                    <h4 style="margin-bottom: 8px;">🔧 Administrador</h4>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8; font-size: 14px;">
                        <li>Todo lo de Premium</li>
                        <li>Gestionar usuarios y roles</li>
                        <li>Ver logs del sistema</li>
                        <li>Estadísticas completas</li>
                    </ul>
                </div>
            </div>

            {{-- 3. BÚSQUEDA POR REFERENCIA --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #1f2937;">
                3. 🔍 Búsqueda por Referencia Catastral
            </h3>
            <p><strong>Disponible para:</strong> Todos los usuarios</p>
            <ol style="list-style: decimal; margin-left: 20px; line-height: 1.8;">
                <li>Ve a la página de <strong>Inicio</strong></li>
                <li>Introduce la referencia catastral (14 o 20 caracteres)</li>
                <li>Haz clic en <strong>"Buscar Propiedad"</strong></li>
                <li>Visualiza la información completa del inmueble</li>
            </ol>
            <div class="info-box info-box-blue" style="margin-top: 12px;">
                <strong>💡 Ejemplo de referencia:</strong>
                <code style="background: white; padding: 4px 8px; border-radius: 4px;">2749704YJ0624N0001DI</code>
            </div>

            {{-- 4. REGISTRO E INICIO DE SESIÓN --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #1f2937;">
                4. 📝 Registro e Inicio de Sesión
            </h3>
            <p><strong>Para crear una cuenta:</strong></p>
            <ol style="list-style: decimal; margin-left: 20px; line-height: 1.8;">
                <li>Haz clic en <strong>"Registrarse"</strong> en el menú superior</li>
                <li>Completa el formulario con tu nombre, email y contraseña</li>
                <li>Tu cuenta se creará como <strong>Visitante</strong></li>
                <li>Podrás mejorar a <strong>Premium</strong> desde tu perfil (gratis)</li>
            </ol>

            {{-- 5. FUNCIONES VISITANTE --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #1f2937;">
                5. 📂 Funciones para Visitantes
            </h3>
            
            <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">5.1. Guardar Propiedades</h4>
            <p>Después de buscar una propiedad, haz clic en <strong>"💾 Guardar Propiedad"</strong>. 
            Podrás ver todas tus propiedades guardadas en <strong>"📂 Mis Propiedades"</strong>.</p>

            <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">5.2. Historial de Búsquedas</h4>
            <p>Accede a <strong>"📊 Historial"</strong> para ver todas tus búsquedas anteriores. 
            Puedes repetir cualquier búsqueda con un solo clic.</p>

            <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">5.3. Imprimir Fichas</h4>
            <p>En el detalle de cualquier propiedad, haz clic en <strong>"🖨️ Imprimir"</strong> 
            para obtener una ficha profesional en formato A4.</p>

            {{-- 6. FUNCIONES PREMIUM --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #92400e;">
                6. ⭐ Funciones Premium
            </h3>

            <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">6.1. Búsqueda por Dirección</h4>
            <p>Accede desde el <strong>Dashboard</strong> al formulario de búsqueda por dirección. 
            Completa los campos: Provincia, Municipio, Tipo de Vía, Nombre de Vía y Número.</p>
            <div class="info-box info-box-yellow" style="margin-top: 8px;">
                <strong>⚠️ Nota:</strong> La API del Catastro puede tener limitaciones. 
                En ese caso, se mostrarán propiedades de ejemplo.
            </div>

            <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">6.2. Favoritos</h4>
            <p>En el detalle de una propiedad, usa el botón <strong>"⭐ Añadir a Favoritos"</strong>. 
            Filtra tus favoritos en <strong>"Mis Propiedades" → "⭐ Favoritas"</strong>.</p>

            <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">6.3. Notas</h4>
            <p>Añade notas privadas o públicas a cualquier propiedad guardada. 
            Las notas privadas solo las ves tú, las públicas son visibles para otros usuarios.</p>

            {{-- 7. PREGUNTAS FRECUENTES --}}
            <h3 style="margin-top: 32px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #1f2937;">
                7. ❓ Preguntas Frecuentes
            </h3>

            <div style="margin-top: 16px;">
                <h4 style="font-weight: 600; margin-bottom: 4px;">¿Cómo obtengo la referencia catastral?</h4>
                <p style="margin-bottom: 16px; color: #4b5563;">
                    Puedes encontrarla en recibos del IBI, escrituras de propiedad o en la 
                    <a href="https://www1.sedecatastro.gob.es/CYCBienInmueble/OVCListaBienes.aspx" 
                       target="_blank" style="color: #2563eb; text-decoration: underline;">
                        Sede Electrónica del Catastro
                    </a>.
                </p>

                <h4 style="font-weight: 600; margin-bottom: 4px;">¿Cuánto cuesta la cuenta Premium?</h4>
                <p style="margin-bottom: 16px; color: #4b5563;">
                    Durante el período académico, el upgrade a Premium es <strong>totalmente gratuito</strong>.
                </p>

                <h4 style="font-weight: 600; margin-bottom: 4px;">¿Los datos son oficiales?</h4>
                <p style="margin-bottom: 16px; color: #4b5563;">
                    Sí, utilizamos la API oficial del Catastro español. Los datos son públicos y oficiales.
                </p>

                <h4 style="font-weight: 600; margin-bottom: 4px;">¿Por qué la búsqueda por dirección no funciona siempre?</h4>
                <p style="margin-bottom: 16px; color: #4b5563;">
                    La API pública del Catastro tiene limitaciones documentadas en búsquedas por dirección. 
                    Por eso mostramos datos de ejemplo cuando falla. La búsqueda por referencia siempre funciona.
                </p>
            </div>

            @if(auth()->check() && auth()->user()->isAdmin())
                {{-- ====================================================================
                     SECCIÓN EXCLUSIVA ADMINISTRADOR
                     ==================================================================== --}}
                <div style="margin-top: 48px; padding-top: 32px; border-top: 3px solid #ef4444;">
                    <div class="info-box info-box-red">
                        <strong>🔐 SECCIÓN EXCLUSIVA PARA ADMINISTRADORES</strong>
                        <p style="margin-top: 4px;">La siguiente información solo es visible para usuarios con rol de Administrador.</p>
                    </div>

                    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 20px; font-weight: 600; color: #991b1b;">
                        8. 🔧 Panel de Administración
                    </h3>

                    <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">8.1. Dashboard Admin</h4>
                    <p>Accede desde el menú superior en <strong>"🔧 Admin"</strong>. 
                    Verás estadísticas en tiempo real:</p>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8;">
                        <li>Total de usuarios registrados</li>
                        <li>Usuarios Premium activos</li>
                        <li>Propiedades guardadas en el sistema</li>
                        <li>Búsquedas realizadas</li>
                    </ul>

                    <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">8.2. Gestión de Usuarios</h4>
                    <p><strong>Funciones disponibles:</strong></p>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8;">
                        <li><strong>Cambiar rol:</strong> Promover usuarios de Visitante a Premium o viceversa</li>
                        <li><strong>Activar/Desactivar:</strong> Bloquear temporalmente el acceso de usuarios</li>
                        <li><strong>Ver información:</strong> Email, fecha de registro, estado actual</li>
                    </ul>
                    <div class="info-box info-box-yellow" style="margin-top: 8px;">
                        <strong>⚠️ Importante:</strong> Los usuarios Admin no pueden ser modificados 
                        por otros administradores para evitar conflictos de permisos.
                    </div>

                    <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">8.3. Logs de API</h4>
                    <p>Monitorea todas las llamadas a la API del Catastro:</p>
                    <ul style="list-style: disc; margin-left: 20px; line-height: 1.8;">
                        <li><strong>Usuario:</strong> Quién realizó la consulta</li>
                        <li><strong>Endpoint:</strong> Tipo de búsqueda (por referencia o dirección)</li>
                        <li><strong>Parámetros:</strong> Datos enviados a la API</li>
                        <li><strong>Estado:</strong> Código de respuesta HTTP y duración en ms</li>
                        <li><strong>Errores:</strong> Detección automática de fallos de la API</li>
                    </ul>

                    <h4 style="margin-top: 16px; font-size: 16px; font-weight: 600;">8.4. Buenas Prácticas Admin</h4>
                    <div class="card" style="background: #fef3c7; margin-top: 8px;">
                        <ul style="list-style: none; padding: 0; line-height: 2;">
                            <li>✅ Revisa periódicamente los logs para detectar errores de la API</li>
                            <li>✅ Verifica que usuarios Premium utilizan las funciones avanzadas</li>
                            <li>✅ Desactiva temporalmente usuarios sospechosos de abuso</li>
                            <li>✅ Monitorea la duración de las llamadas API (> 5000ms indica problemas)</li>
                            <li>❌ No cambies roles sin verificar la identidad del usuario</li>
                            <li>❌ No desactives usuarios sin motivo justificado documentado</li>
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Soporte --}}
            <div class="info-box info-box-green" style="margin-top: 32px;">
                <h4 style="margin-bottom: 8px;">💬 ¿Necesitas ayuda?</h4>
                <p>Este es un proyecto académico. Para más información contacta con el desarrollador.</p>
            </div>

            <div class="btn-group" style="margin-top: 24px;">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    ← Volver al Inicio
                </a>
            </div>
        </div>
    </div>
@endsection