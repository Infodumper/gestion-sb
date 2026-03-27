# Directiva: Sistema de Autenticación y Acceso
> **Skills Asociados:** `login_manager`, `auth_authenticator`, `session_guard`

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Garantizar un acceso seguro, biométrico y fluido al panel de gestión de Stefy Barroso. El sistema debe proteger la integridad de los datos de clientes y ventas, manteniendo una sesión persistente pero segura (JWT/PHP Sessions) sin necesidad de recargar la página.

## Capa 2: Orquestación (Procesos de Seguridad)

1. **Flujo de Acceso**:
    * Verificación de credenciales vía AJAX.
    * Generación de variables de sesión seguras.
    * Redirección controlada estilo SPA al Dashboard.
2. **Persistencia y Control**:
    * Middleware (`security.php`) que verifica el estado de la sesión en cada petición.
    * Manejo de expiración de sesiones para evitar accesos no autorizados en dispositivos compartidos.
3. **Capa Visual**:
    * Diseño minimalista y responsive enfocado en la facilidad de entrada desde móviles.

## Capa 3: Ejecución (Scripts y Recursos)

* **Maestro**: `admin/login.php`
* **Lógica Backend**: `admin/ajax_login.php`
* **Guardias**:
    * `includes/security.php`: Protección de archivos raíz.
    * `includes/db.php`: Conexión segura bajo estándares PDO.

## Capa 4: Observabilidad

* **Logging de Intentos**: Registro de accesos fallidos y exitosos en la auditoría del sistema.
* **Alertas**: Feedback inmediato mediante SweetAlert2 para errores de credenciales o de red.
