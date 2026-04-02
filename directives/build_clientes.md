# Directiva: Gestión Integral de Clientes
> **Skills Asociados:** `client_manager`, `premium_attention`, `client_profiler`

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Establecer un repositorio centralizado y veraz de la base instalada de clientes. El sistema debe permitir una gestión 360° que abarque desde la captura de datos básicos hasta la visualización profunda del historial transaccional.

## Capa 2: Orquestación (Procesos de Gestión)

1. **Captura y Normalización**: Validación por Teléfono y limpieza automática de nombres.
2. **Ciclo de Vida de Contacto (Reglas de Negocio)**:
    *   **WhatsApp Cumple (📲)**: Solo habilitado en la fecha exacta del cumpleaños. Se muestra iluminado (Verde) solo si es el día y no se ha enviado. Cambia a gris tras el envío o si no es la fecha. Alerta al usuario si intenta reenviar.
    *   **Promo Mensual (✓)**: El tilde marca la difusión mensual. Una vez marcado, el sistema previene el re-envío del mismo mes con una advertencia visual y sonora (Swal).
3. **Visión 360° (Ficha del Cliente)**: Agregación visual de pedidos en modales persistentes.

## Capa 3: Ejecución (Componentes y Archivos)

*   **Ubicación Maestro**: `admin/apps/clientes/ver_clientes.php`
*   **Gestión CRM**: `admin/apps/clientes/atencion_cliente.php`
*   **Gestión AJAX**: `ajax_save_client.php`, `ajax_get_client_card.php`, `ajax_mark_contacted.php`.

## Capa 4: Estándar Visual (Regla de las Subplacas)

*   **Contenedor**: Está terminantemente prohibido el uso de texto flotante en los listados. Cada cliente debe habitar en una `subplaca-adn` (bg-white, rounded-[1.8rem], shadow-sm).
*   **Contenido Obligatorio**: Nombre completo (`h3`), Dato contextual (Ej: 🎂 Cumpleaños) y Acciones Rápidas (✏️ Editar / 📲 WhatsApp / ✓ Check).
*   **Navegación**: Transiciones suaves entre el listado maestro y los modales de edición.

## Capa 5: Base de Datos (Esquema y Relaciones)

Este módulo es el "Dueño" (Master Data) de las identidades de clientes.

### Tabla: `Clientes`
- **PK**: `IdCliente` (INT, AUTO_INCREMENT)
- **Campos críticos**:
  - `Telefono`: (VARCHAR 20, **UNIQUE NOT NULL**). *Es la llave de negocio principal. No puede haber dos clientes con el mismo número.*
  - `Dni`: (VARCHAR 20, **UNIQUE**, NULLEABLE).
  - `FechaNac`: (DATE, NULLEABLE). Vital para el módulo de Atención Premium.
  - `Estado`: (TINYINT) `1`: Activo, `0`: Baja, `2`: Inactivo.
- **Restricciones DDL**:
  - Las relaciones hacia afuera (Pedidos, Turnos, Contactos) tienen **ON DELETE CASCADE**. Al borrar al cliente, muere todo su historial transaccional.
