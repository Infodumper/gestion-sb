# Directiva: Gestión de Notas de Trabajo

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Garantizar la trazabilidad absoluta de las prestaciones de servicios profesionales. El sistema debe capturar el quién (profesional), el a quién (cliente), el qué (servicios) y el cuánto (precio pactado), asegurando que los cambios futuros en los precios de catálogo no afecten los registros históricos.

## Capa 2: Orquestación (Flujos de Datos)

1. **Snapshot de Valor**: Al momento de registrar la nota, el sistema consulta el precio actual del catálogo pero permite su ajuste manual. El valor final se persiste como un dato independiente (PVP histórico).
2. **Integridad Referencial**: Obligatoriedad de vincular cada nota a un usuario activo (`profesional`) y un cliente registrado.
3. **Consolidación de Datos**: Generación de vistas relacionales para auditoría y reportes de rendimiento por profesional.

## Capa 3: Ejecución (Implementación Técnica)

*   **Ubicación**: `admin/apps/servicios/`
*   **Tablas de Impacto**:
    *   `notatrabajo`: Cabecera de la transacción.
    *   `detallesnotatrabajo`: Líneas de detalle con snapshot de precio.
*   **Arquitectura AJAX**:
    *   `nota_trabajo.php`: Cargador dinámico de filas.
    *   `ajax_guardar_nota.php`: Transaccionalidad atómica (Insert Header -> Get ID -> Insert Details).

## Capa 4: Observabilidad

*   **Identidad Visual**: Implementación estricta de la paleta **Verde Esmeralda** y tipografía **Poppins**.
*   **Feedback de Auditoría**: Uso de SweetAlert2 para notificar el éxito de la persistencia y registro automático en el log del sistema.