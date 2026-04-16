## Directiva: Módulo de Ventas y Pedidos
> **Skills Asociados:** `sales_manager` · `catalog_manager` · `billing_helper`
> **Versión:** 2.0 · **Estado:** Producción

---

## Capa 1: Objetivo y Alcance

**Misión del módulo:**
Gestionar el ciclo completo de venta — desde la toma del pedido hasta el registro del cobro — integrando la selección de productos/servicios, el cálculo de totales y la actualización automática de la Ficha 360° del cliente.

**Alcance:**
- Registro de pedidos con cabecera + detalle (ítems)
- Carrito de compra con precios editables
- Historial de ventas con estados y filtros
- Métricas de negocio (ventas del mes, ticket promedio)

---

## Capa 2: Orquestación (Procesos de Negocio)

### 1. Flujo de Nueva Venta

```
Seleccionar Cliente → Armar Carrito → Revisar Total → Confirmar → Persistir (transacción atómica) → Feedback
```

1. **Selección de cliente**: Autocompletado dinámico por nombre o teléfono (AJAX con debounce 300ms).
2. **Armar carrito**: Agregar ítems del catálogo o ingresar descripción libre + precio.
3. **Cálculo en tiempo real**: Total se recalcula en el cliente (JS) ante cada cambio de cantidad.
4. **Confirmación**: SweetAlert2 de confirmación antes de persistir.
5. **Persistencia atómica**: INSERT en `Pedidos` + INSERT en `ItemsPedido` dentro de una sola transacción PDO.
6. **Feedback**: Swal con número de pedido y total.

### 2. Estados del Pedido

| Código | Estado | Color | Acción permitida |
|---|---|---|---|
| `1` | Pendiente | Ámbar | → Pagado, → Cancelado |
| `2` | Pagado | Esmeralda | → Entregado |
| `3` | Entregado | Azul | (estado final) |
| `0` | Cancelado | Rojo | (estado final) |

**Regla**: No existen transiciones hacia atrás. Un pedido Entregado no puede volver a Pendiente.

### 3. Integración 360°
Al cerrar una venta, la Ficha 360° del cliente debe actualizarse con el nuevo historial en la próxima apertura (carga fresca desde la BD, no cache del DOM).

---

## Capa 3: Ejecución (Componentes y Archivos)

| Archivo | Rol |
|---|---|
| `admin/apps/ventas/ver_ventas.php` | Vista historial de ventas |
| `admin/apps/pedidos/ver_pedidos.php` | Vista toma de pedidos / carrito |
| `admin/apps/pedidos/ajax_save_pedido.php` | Endpoint CREATE pedido (transacción) |
| `admin/apps/pedidos/ajax_get_pedidos.php` | Endpoint GET listado |
| `admin/apps/pedidos/ajax_update_status.php` | Endpoint UPDATE estado |
| `admin/apps/pedidos/ajax_get_pedido_detalle.php` | Endpoint GET ítems de un pedido |
| `admin/apps/ventas/ajax_get_metricas.php` | Endpoint GET métricas del mes |

---

## Capa 4: Estándar Visual

- **Carrito UI**: Panel lateral o bottom-sheet con la lista de ítems, donde cada ítem vive en su propia Subplaca.
- **Historial**: Cada pedido = Subplaca con estado badge (`badge-pendiente`, `badge-pagado`, etc.), nombre del cliente, total y fecha.
- **Selector de cliente**: Input con autocompletado tipo `datalist` o dropdown personalizado.
- **Botón "Confirmar Pedido"**: Deshabilitado si el carrito está vacío o no hay cliente seleccionado.

---

## Capa 5: Base de Datos

### Tabla: `Pedidos`

```sql
CREATE TABLE Pedidos (
    IdPedido       INT           NOT NULL AUTO_INCREMENT,
    IdCliente      INT           NOT NULL,
    IdVendedor     INT           NULL,               -- FK a DbLogin
    Notas          TEXT          NULL,
    Total          DECIMAL(10,2) NOT NULL DEFAULT 0,
    Estado         TINYINT       NOT NULL DEFAULT 1, -- 1=Pendiente, 2=Pagado, 3=Entregado, 0=Cancelado
    FechaCreacion  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FechaModif     DATETIME      NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (IdPedido),
    FOREIGN KEY (IdCliente)  REFERENCES Clientes(IdCliente)  ON DELETE CASCADE,
    FOREIGN KEY (IdVendedor) REFERENCES DbLogin(IdUsuario)   ON DELETE SET NULL,
    INDEX idx_cliente_estado (IdCliente, Estado),
    INDEX idx_fecha (FechaCreacion)
);
```

### Tabla: `ItemsPedido`

```sql
CREATE TABLE ItemsPedido (
    IdItem         INT           NOT NULL AUTO_INCREMENT,
    IdPedido       INT           NOT NULL,
    IdProducto     INT           NULL,               -- FK a Productos (puede ser NULL si es ítem libre)
    Descripcion    VARCHAR(255)  NOT NULL,
    Cantidad       INT           NOT NULL DEFAULT 1,
    PrecioUnitario DECIMAL(10,2) NOT NULL,           -- Congelar precio AL MOMENTO del INSERT
    Subtotal       DECIMAL(10,2) NOT NULL,           -- Cantidad × PrecioUnitario

    PRIMARY KEY (IdItem),
    FOREIGN KEY (IdPedido)    REFERENCES Pedidos(IdPedido)   ON DELETE CASCADE,
    FOREIGN KEY (IdProducto)  REFERENCES Productos(IdProducto) ON DELETE SET NULL
);
```

### Consulta Tipo: Métricas del Mes

```sql
SELECT
    COUNT(*)         AS total_pedidos,
    SUM(Total)       AS ventas_mes,
    AVG(Total)       AS ticket_promedio,
    COUNT(DISTINCT IdCliente) AS clientes_unicos
FROM Pedidos
WHERE Estado IN (2, 3)  -- Solo Pagados y Entregados
  AND MONTH(FechaCreacion) = MONTH(NOW())
  AND YEAR(FechaCreacion)  = YEAR(NOW());
```

---

## Reglas de Negocio Críticas

| Regla | Descripción |
|---|---|
| **Atomicidad** | INSERT en `Pedidos` + todos sus `ItemsPedido` en una transacción. Si falla un ítem, rollback completo |
| **Precio congelado** | `PrecioUnitario` se captura al momento del INSERT, no desde la tabla de productos |
| **Subtotal en BD** | `Subtotal = Cantidad × PrecioUnitario` se guarda en la BD (no solo calculado en PHP) |
| **Total en cabecera** | El campo `Total` en `Pedidos` se actualiza al final de la transacción |
| **FK no huérfana** | Validar `IdCliente > 0` y que exista en BD antes de iniciar la transacción |