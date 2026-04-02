# Directiva: CRM (Atención Premium)
> **Skills Asociados:** `premium_attention`, `client_manager`, `client_profiler`, `sales_manager`

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Fomentar la lealtad y el valor de vida del cliente (LTV) mediante un seguimiento proactivo de fechas especiales, el reconocimiento de la recurrencia y la gestión eficiente de campañas de difusión masiva. El sistema debe transformar la información transaccional en acciones relacionales de alto impacto.

## Capa 2: Orquestación (Componentes Relacionales)

1. **Panel de Fidelización (Cumpleaños)**:
    * **Lógica Proactiva**: Identificación de clientes con cumpleaños en el mes actual. Solo el botón de WhatsApp (📲) se ilumina el día correspondiente.
    * **Protocolo de Acción**: El saludo de felicitación solo se permite una vez por día. Si ya fue enviado, el botón se vuelve gris y se previene el reenvío con alertas de confirmación.
2. **Segmentación VIP (Clientes Habituales)**:
    * **Criterio de Recurrencia**: Clientes con la mayor cantidad de compras acumuladas o servicios recientes.
    * **Acción Mensual (✓)**: Se permite un "Check" mensual para registrar la difusión de promociones, previniendo duplicados durante el mismo ciclo.
3. **Motor de Difusión (Bulk Messaging)**:
    * **Extracción**: Copiado rápido de listas (Nombre/Teléfono) para integración con herramientas externas. *(Nota: Sección oculta actualmente en la interfaz, pero preservada en el código).*

## Capa 3: Ejecución (Arquitectura Técnica)

*   **Página Principal**: `admin/apps/clientes/atencion_cliente.php`
*   **Gestión de Envíos**: `admin/apps/clientes/launcher_wa.php`.
*   **Trazabilidad**: Registro de contactos en tiempo real mediante `ajax_mark_contacted.php`.
*   **Interfaz ADN**: Diseño de "Subplacas" con estados visuales claros (iluminado/gris).

## Capa 4: Observabilidad

*   **Identidad Visual**: Uso de paleta Verde Esmeralda (Marca) y tipografía Poppins.
*   **Métricas de Contacto**: Registro de cada interacción para auditoría de acciones de fidelización y métricas de LTV.
*   **Feedback de Usuario**: Uso centralizado de SweetAlert2 (`Swal.fire`) con botones de "Aceptar" para validar reglas de negocio.

## Capa 5: Base de Datos (Esquema y Relaciones)

Este módulo no almacena entidades absolutas (como Clientes), sino su *trazabilidad de relación y fidelización*.

### Tabla: `ContactosWhatsapp`
- **PK**: `IdContacto` (INT, AUTO_INCREMENT)
- **FK**: `IdCliente` (Apunta a `Clientes.IdCliente`, **ON DELETE CASCADE**).
- **Campos críticos**:
  - `Tipo`: (ENUM: 'cumple', 'habitual', 'mensual'). *Solo admite estos valores cerrados de negocio.*
  - `FechaContacto`: (DATETIME) Guarda el `CURRENT_TIMESTAMP` automático por defecto.
- **Lógica asociada**: Esta tabla se lee para saber si el contacto "ya fue realizado este mes/día" cambiando los estados visuales en la interfaz. Al fallar un FK por borrado de cliente original, el log se elimina apegado a la regla de borrado en cascada.
