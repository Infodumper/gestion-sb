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