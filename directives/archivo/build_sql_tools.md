# Directiva: Herramientas de Administración SQL (SQL Tools)

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Proveer una interfaz de administración de bajo nivel para la manipulación y auditoría de la base de datos `consultora_belleza`. Diseñada para perfiles técnicos, permite la ejecución de scripts de mantenimiento y visualización directa de esquemas.

## Capa 2: Orquestación (Componentes)

1. **Gestión de Conexión**: Centralizar el acceso PDO mediante el motor de conexión del sistema.
2. **Explorador de Esquemas**: Visualización de tablas, índices y restricciones (FOREIGN KEYS).
3. **Consola de Consultas**: Ejecución controlada de sentencias SQL (Select, Insert, Update, Describe).

## Capa 3: Ejecución (Estructura Técnica)

*   **Ubicación**: `admin/apps/sql/`
*   **Archivos Críticos**:
    *   `index.php`: Dashboard técnico (Listado de tablas y estado del servidor).
    *   `consulta.php`: Interfaz de consola con resaltado de sintaxis simulado.
    *   `ver_tabla.php`: Renderizado dinámico de tablas con soporte para metadatos.
    *   `actualizar_db.php`: Script de migración no destructiva para evolución del esquema.

## Capa 4: Observabilidad y Seguridad

*   **Auditoría**: Toda consulta ejecutada vía consola debe quedar registrada en el log de eventos (`log_event`).
*   **Seguridad Crítica**: 
    *   Acceso restringido estrictamente a usuarios con rol `admin_gral`.
    *   Implementación de bloques `try-catch` para capturar errores de sintaxis sin exponer el stack trace al usuario final.