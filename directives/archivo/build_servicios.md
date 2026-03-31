# Directiva: Ecosistema de Servicios y Stock

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Centralizar la oferta de valor de la consultora, integrando la prestación de servicios profesionales con la gestión inteligente de inventario y la fidelización de clientes habituales. Este módulo es el motor principal de la rentabilidad del negocio.

## Capa 2: Orquestación (Procesos Integrados)

1. **Gestión de Servicios (Notas de Trabajo)**:
    * Captura de valor en tiempo real con precios sugeridos.
    * Registro de profesionales asignados por cada servicio.
2. **Control de Inventario (KPIs de Stock)**:
    * Monitoreo de niveles críticos de productos.
    * Automatización de entradas y salidas ligadas a la venta.
3. **Fidelización (CRM)**:
    * Cruce de datos de servicios para identificar clientes VIP.
    * Gestión de acciones de marketing relacional (cumpleaños).

## Capa 3: Ejecución (Estructura de Carpetas)

*   **Módulo de Notas**: `admin/apps/servicios/nota_trabajo.php`
*   **Módulo de Productos/Stock**: `admin/apps/stock/stock.php`
*   **Módulo de Atención**: `admin/apps/clientes/atencion_cliente.php`

### Estándares Técnicos:
*   Uso de **SQL Snapshots** para congelar el precio de venta en el momento de la prestación del servicio.
*   Interfaz de búsqueda unificada para productos y servicios en formularios de carga.

## Capa 4: Observabilidad

*   **Prioridad en Dashboard**: Ocupa la posición líder (índice 0) por su relevancia operativa.
*   **Métricas Transversales**: El sistema reportará el "Servicio más solicitado" y el "Producto de mayor rotación" al tablero de control central.

## Capa 5: Base de Datos (Esquema y Relaciones)

Maneja datos estáticos de valor de negocio (Servicios) y asignación transaccional (Turnos).

### Tabla: `Servicios`
- **PK**: `IdServicio` (INT, AUTO_INCREMENT)
- **Campos críticos**:
  - `Duracion`: (INT) Comentada como duración lógica en "minutos".
  - `Precio`: DECIMAL(10, 2).
  - `Estado`: TINYINT (1) Activo por defecto.

### Tabla: `Turnos`
- **PK**: `IdTurno` (INT, AUTO_INCREMENT)
- **FK1**: `IdCliente` (**ON DELETE CASCADE**).
- **FK2**: `IdServicio` (**ON DELETE CASCADE**).
- **Campos críticos**:
  - `FechaTurno`: DATETIME obligatorio, dicta el cronograma.
  - `Estado`: TINYINT `1`: Pendiente, `2`: Completado, `0`: Cancelado.
