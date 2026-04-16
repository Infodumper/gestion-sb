# Contratos de Datos — Gestion SB v2.0

> Definiciones canónicas de tablas y contratos de respuesta AJAX.
> **No modificar sin actualizar la Directiva correspondiente en `/directives/`.**

---

## Contrato AJAX Universal

Todo endpoint en el sistema retorna **exactamente** este esquema:

```json
{
  "status":  "ok | error",
  "data":    { ... } | [ ... ] | null,
  "message": "Texto en español legible por el usuario"
}
```

**Códigos HTTP asociados:**

| Situación | HTTP Code | status |
|---|---|---|
| Éxito | 200 | `"ok"` |
| Validación fallida | 400 | `"error"` |
| No autenticado | 401 | `"error"` |
| No encontrado | 404 | `"error"` |
| Error interno | 500 | `"error"` |

---

## Tablas del Sistema

### `clientes`

| Columna | Tipo | Constraint | Descripción |
|---|---|---|---|
| `IdCliente` | INT | PK, AUTO_INCREMENT | Identificador único |
| `Nombre` | VARCHAR(80) | NOT NULL | Nombre de pila |
| `Apellido` | VARCHAR(80) | DEFAULT '' | Apellido |
| `Dni` | VARCHAR(20) | UNIQUE, NULL | DNI / documento |
| `Telefono` | VARCHAR(20) | UNIQUE, NULL | Solo dígitos |
| `FechaNac` | DATE | NULL | Almacenado como `2000-MM-DD` |
| `Promociones` | TINYINT(1) | DEFAULT 1 | 1 = acepta, 0 = no |
| `Estado` | TINYINT(1) | DEFAULT 1 | 1 = activo, 0 = inactivo |
| `FechaCreacion` | DATETIME | DEFAULT NOW() | Timestamp de creación |

**Índices:** `(Telefono)`, `(Apellido, Nombre)`, `(Estado)`

---

### `pedidos`

| Columna | Tipo | Constraint | Descripción |
|---|---|---|---|
| `IdPedido` | INT | PK, AUTO_INCREMENT | Identificador único |
| `IdCliente` | INT | FK → clientes | Cliente asociado |
| `IdVendedor` | INT | FK → DbLogin, NULL | Quién lo tomó |
| `Notas` | TEXT | NULL | Observaciones |
| `Total` | DECIMAL(10,2) | NOT NULL, DEFAULT 0 | Suma de ítems |
| `Estado` | TINYINT | DEFAULT 1 | Ver tabla de estados |
| `FechaCreacion` | DATETIME | DEFAULT NOW() | Timestamp |
| `FechaModif` | DATETIME | NULL, ON UPDATE NOW() | Última modificación |

**Estados de Pedido:**

| Código | Estado | Transiciones |
|---|---|---|
| `1` | Pendiente | → Pagado, → Cancelado |
| `2` | Pagado | → Entregado |
| `3` | Entregado | (final) |
| `0` | Cancelado | (final) |

---

### `ItemsPedido`

| Columna | Tipo | Constraint | Descripción |
|---|---|---|---|
| `IdItem` | INT | PK, AUTO_INCREMENT | — |
| `IdPedido` | INT | FK → Pedidos, CASCADE | Pedido padre |
| `IdProducto` | INT | FK → Productos, NULL | NULL si ítem libre |
| `Descripcion` | VARCHAR(255) | NOT NULL | Descripción del ítem |
| `Cantidad` | INT | DEFAULT 1 | Unidades |
| `PrecioUnitario` | DECIMAL(10,2) | NOT NULL | ⚠️ Congelar al INSERT |
| `Subtotal` | DECIMAL(10,2) | NOT NULL | `Cantidad × PrecioUnitario` |

---

### `contactoswhatsapp`

| Columna | Tipo | Constraint | Descripción |
|---|---|---|---|
| `IdContacto` | INT | PK, AUTO_INCREMENT | — |
| `IdCliente` | INT | FK → clientes | Cliente contactado |
| `Tipo` | VARCHAR(20) | NOT NULL | `'cumple'`, `'habitual'`, `'mensual'` |
| `FechaContacto` | DATETIME | DEFAULT NOW() | Timestamp del contacto |

**Tipos válidos para `Tipo`:** `cumple` · `habitual` · `mensual`

---

### `logs`

| Columna | Tipo | Descripción |
|---|---|---|
| `IdLog` | INT PK | — |
| `IdUsuario` | INT NULL | FK → DbLogin |
| `Nivel` | VARCHAR(20) | `INSERT`, `UPDATE`, `DELETE`, `ERROR`, `AUTH_OK`, `AUTH_FAIL`, `WARN`, `INFO` |
| `Evento` | VARCHAR(100) | Etiqueta corta del evento |
| `Detalle` | TEXT | Descripción completa |
| `Origen` | VARCHAR(100) | Nombre del archivo fuente |
| `Ip` | VARCHAR(45) | IP del cliente |
| `FechaLog` | DATETIME | DEFAULT NOW() |

---

## Campos calculados / Normalizaciones

| Campo | Regla de normalización |
|---|---|
| `Nombre`, `Apellido` | `ucwords(strtolower($input))` antes del INSERT/UPDATE |
| `Telefono` | `preg_replace('/\D/', '', $input)` — solo dígitos |
| `Dni` | `preg_replace('/[\s\-\.]/', '', $input)` — sin separadores |
| `FechaNac` | Almacenar como `2000-MM-DD` (año neutro para que el cumpleaños sea agnóstico al año) |
| `TelefonoWA` | `preg_replace('/\D/', '', $tel)` — para URL de WhatsApp |