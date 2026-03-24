# Directiva: Estándares de Seguridad (ISO 27001)

Este documento define las políticas de seguridad de la información para el proyecto Consultora, alineadas con los controles de la norma ISO 27001.

## 1. Control de Acceso (A.9)

*   **Autenticación**: Uso obligatorio de `password_hash()` con BCRYPT y `password_verify()`.
*   **Gestión de Sesiones**:
*   Toda página administrativa `admin/` debe verificar `session_start()` y la existencia de `userid`.
*   Uso de `session_regenerate_id(true)` tras el login exitoso para prevenir fijación de sesión.
*   **Principio de Menor Privilegio**: Solo el personal autorizado (Roles en `DbLogin`) puede acceder a los módulos de configuración.
## 2. Criptografía (A.10)

*   **Datos en Reposo**: Las contraseñas NUNCA se almacenan en texto plano.
*   **Datos en Tránsito**: Se recomienda el uso de TLS/SSL (HTTPS) en el entorno de producción.
## 3. Seguridad de las Operaciones (A.12)

*   **Logging**: Registro de eventos de seguridad (Login exitoso, Login fallido, cambios críticos en datos).
*   **Protección contra Malware**: Validación estricta de extensiones en cualquier proceso de subida de archivos (upload).
## 4. Seguridad en el Desarrollo (A.14)

*   **Prevención de Inyección**: Uso MANDATORIO de sentencias preparadas (PDO) para toda consulta SQL.
*   **Sanitización**: Uso de `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` en todas las salidas HTML dinámicas.
*   **Validación de Entradas**: Filtrado de datos recibidos vía POST/GET usando `filter_var()` o validaciones manuales estrictas.
## 5. Pruebas y Validación

*   Toda implementación "grande" debe pasar el plan de pruebas definido en `/tests/manual_tests.md`.