# Directiva: Gestión de Pedidos y Transacciones

## Capa 1: Directiva (Objetivo)

**Objetivo:**
Administrar el ciclo de vida de los pedidos, garantizando el registro preciso de ventas, la asociación con clientes y la integridad de los montos totales.

## Capa 2: Orquestación (Flujos de Negocio)

1. **Ciclo de Pedido**:
    * **Creación**: Selección de cliente y agregación de productos/servicios.
    * **Validación**: Comprobación de integridad de datos y cálculo de totales en servidor.
    * **Persistencia**: Registro atómico de cabecera (`pedidos`) y líneas de detalle (`items_pedido`).

2. **Estados del Pedido**:
    * `1`: Pendiente / Registrado.
    * `2`: Pagado.
    * `3`: Entregado / Finalizado.

## Capa 3: Ejecución (Implementación)

*   **Nomenclatura**: Uso estricto de tablas `Pedidos` e `ItemsPedido`.
*   **Archivos Clave**:
    *   `index.php`: Listado maestro de pedidos con filtros por fecha y cliente.
    *   `nuevo_pedido.php`: Formulario dinámico con soporte para autocompletado.
    *   `ajax_save_pedido.php`: Engine de guardado con manejo de transacciones PDO (`beginTransaction`, `commit`).
    *   `ver_pedido.php`: Vista de detalle y generación de comprobante visual.

## Capa 4: Observabilidad

*   **KPIs**: Los totales de pedidos exitosos deben impactar en tiempo real los indicadores del Dashboard.
*   **Validación**: Cada ítem guardado debe referenciar un `IdProducto` válido para asegurar la integridad de la base de datos.

### Caso de Uso: Registrar Pedido (Skill asociado: sales_manager)

- **Actor (A.9 Control de Acceso)**: Usuario logueado (Validación de `$_SESSION['userid']` requerida).
- **Acción**: Registrar un nuevo pedido en la base de datos.
- **Contexto**: El pedido fue solicitado por el cliente externamente (WhatsApp, teléfono, presencial, etc).
- **Inputs**: 
  - `cliente_id`
  - Array bidimensional de `productos` y `cantidades`
  - *(Todos los inputs deben filtrarse vía POST).*

- **Orquestación y Validación (Capa 2)**:
  - Verificar que el `cliente_id` existe (evitar claves foráneas huérfanas).
  - Validar que los datos del pedido tienen formato correcto y hay stock suficiente.

- **Ejecución y Transacción (Capa 3 y A.14)**:
  - Guardar el pedido usando exclusivamente **PDO (Sentencias Preparadas)** para evitar Inyección SQL.
  - Registrar la clave foránea del creador (`IdUsuario = $_SESSION['userid']`) para garantizar trazabilidad estricta (ISO 27001).

- **Observabilidad (Capa 4)**:
  - Invocar obligatoriamente `log_event($pdo, 'NUEVO_PEDIDO', "Pedido registrado para Cliente ID: {cliente_id}")`.

- **Feedback en Interfaz (SPA & Sistema de Placas Independientes)**:
  - Mostrar confirmación de éxito utilizando **SweetAlert2**.
  - Si se muestra el pedido creado, debe renderizarse dentro de una "Subplaca" (`bg-white rounded-[1.8rem] shadow-sm`).

## Capa 5: Base de Datos (Esquema y Relaciones)

Módulo transaccional principal y de alta volatilidad. Maneja un esquema "Cabecera - Detalle" (Header-Line).

### Tabla: `Pedidos` (Cabecera)
- **PK**: `IdPedido` (INT, AUTO_INCREMENT)
- **FK1**: `IdCliente` (Apunta a `Clientes.IdCliente`, **ON DELETE CASCADE**).
- **FK2**: `IdUsuario` (Apunta a `DbLogin.IdUsuario`, registra quién facturó).
- **Campos críticos**:
  - `Fecha`: (DATETIME, DEFAULT CURRENT_TIMESTAMP).
  - `Total`: DECIMAL(10, 2) calculado automáticamente en base de datos local.
  - `Estado`: (TINYINT) `1`: Pendiente, `2`: Pagado, `3`: Entregado.

### Tabla: `ItemsPedido` (Detalle)
- **PK**: `IdItem` (INT, AUTO_INCREMENT)
- **FK1**: `IdPedido` (Apunta a `Pedidos.IdPedido`, **ON DELETE CASCADE**).
- **FK2**: `IdProducto` (Apunta a `Productos.IdProducto`, **ON DELETE CASCADE**).
- **Campos críticos**:
  - `Cantidad`: (INT, NOT NULL, DEFAULT 1).
  - `PrecioUnitario`: DECIMAL(10, 2). *Se debe capturar el precio del producto AL MOMENTO de la venta para independizar el registro de la inflación (historical snapshot).*
