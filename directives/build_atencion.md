# Directiva: CRM (Atención Premium)

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Fomentar la lealtad y el valor de vida del cliente (LTV) mediante un seguimiento proactivo de fechas especiales, el reconocimiento de la recurrencia y la gestión eficiente de campañas de difusión masiva. El sistema debe transformar la información transaccional en acciones relacionales de alto impacto.

## Capa 2: Orquestación (Componentes Relacionales)

1. **Panel de Fidelización (Cumpleaños)**:
    * **Lógica Proactiva**: Identificación de clientes con cumpleaños en el mes actual.
    * **Protocolo de Acción**: Resaltado visual de eventos del día (`HOY`) y validación de fecha para el envío de saludos personalizados.
2. **Segmentación VIP (Clientes Habituales)**:
    * **Criterio de Recurrencia**: Clientes con 3 o más pedidos/servicios en el periodo mensual.
    * **Acción**: Facilitar el contacto de agradecimiento o reserva prioritaria.
3. **Motor de Difusión (Bulk Messaging)**:
    * **Extracción de Datos**: Generación de listas normalizadas en formato CSV (Nombre, Teléfono) para integración con herramientas externas de difusión.

## Capa 3: Ejecución (Arquitectura Técnica)

*   **Página Principal**: `admin/apps/clientes/atencion_cliente.php`
*   **Gestión de Envíos**: `admin/apps/clientes/launcher_wa.php` (Lanzador intermedio para protocolos `whatsapp://`).
*   **Trazabilidad**: Registro de contactos en tiempo real mediante `ajax_mark_contacted.php` para evitar duplicidad de comunicaciones.
*   **Interfaz**: Diseño de "Chapitas" premium con estados visuales (contactado/pendiente).

## Capa 4: Observabilidad

*   **Identidad Visual**: Uso de la paleta **Verde Esmeralda** y sombras suaves para una estética de lujo.
*   **Métricas de Contacto**: Registro de cada interacción en los logs del sistema para auditoría de acciones de fidelización.