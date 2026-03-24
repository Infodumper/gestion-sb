# Directiva: Módulo de Ventas y Pedidos

## Capa 1: Directiva (Objetivo)

**Objetivo:**
Gestionar el ciclo completo de venta, integrando la toma de pedidos (catálogo o stock) con la facturación y el enriquecimiento de la Ficha del Cliente.

## Capa 2: Orquestación (Procesos de Negocio)

1. **Nueva Venta**:
    * Selección de cliente (buscador dinámico por Nombre/DNI).
    * Selección de productos/servicios con precios sugeridos editables.
    * Cálculo automático de totales, impuestos y descuentos en tiempo real.
2. **Historial y CRM**:
    * Listado cronológico de ventas con estados de entrega.
    * Detalle profundo de ítems por pedido.
3. **Integración 360°**:
    * Al cerrar una venta, la "Ficha del Cliente" debe actualizarse automáticamente con el nuevo historial.

## Capa 3: Ejecución (Estándares de Interfaz)

*   **Input Inteligente**: Autocompletado robusto para productos y clientes mediante AJAX.
*   **Carrito UI**: Interfaz visual de ítems seleccionados con capacidad de remoción rápida antes de la persistencia.
*   **Estado de Transacción**: Control visual claro del estado (Pendiente / Pagado / Entregado).

## Capa 4: Observabilidad

*   **Métricas de Negocio**: Envío de datos al Dashboard central (Total ventas mes, ticket promedio, productos estrella).
*   **Logs**: Registro de cada transición de estado de pedido para auditoría.