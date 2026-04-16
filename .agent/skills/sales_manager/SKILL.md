---
name: sales_manager
description: Especialista en procesos de venta, lógica transaccional, carritos de compra y gestión de pedidos por catálogo.
directiva: directives/build_ventas.md, directives/build_pedidos.md
---

# Skill: sales_manager

## 1. Rol y Responsabilidad

Eres el agente **sales_manager**. Tu mandato es garantizar **integridad transaccional absoluta** en el ciclo de vida de ventas y pedidos. Un pedido es una operación compuesta (cabecera + ítems) — se persiste completo o no se persiste nada.

Leer directivas antes de ejecutar: `directives/build_ventas.md` y `directives/build_pedidos.md`.

---

## 2. Patrones de Código Obligatorios

### A. Registro de Pedido con Transacción PDO (`ajax_save_pedido.php`)

```php
<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$input     = json_decode(file_get_contents('php://input'), true);
$idCliente = intval($input['id_cliente'] ?? 0);
$items     = $input['items'] ?? [];
$notas     = trim($input['notas'] ?? '');

// Validaciones previas a la transacción
if ($idCliente <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar un cliente']);
    exit;
}
if (empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'El pedido debe tener al menos un ítem']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Insertar cabecera del pedido
    $stmtPedido = $pdo->prepare("
        INSERT INTO Pedidos (IdCliente, IdVendedor, Notas, Estado, FechaCreacion)
        VALUES (:id_cliente, :id_vendedor, :notas, 1, NOW())
    ");
    $stmtPedido->execute([
        ':id_cliente'  => $idCliente,
        ':id_vendedor' => $_SESSION['userid'],
        ':notas'       => $notas,
    ]);
    $idPedido = $pdo->lastInsertId();

    // 2. Insertar ítems (capturar precio AL MOMENTO, no calcular después)
    $stmtItem = $pdo->prepare("
        INSERT INTO ItemsPedido (IdPedido, IdProducto, Descripcion, Cantidad, PrecioUnitario, Subtotal)
        VALUES (:id_pedido, :id_producto, :descripcion, :cantidad, :precio, :subtotal)
    ");

    $totalPedido = 0;
    foreach ($items as $item) {
        $cantidad  = max(1, intval($item['cantidad']));
        $precio    = floatval($item['precio_unitario']);
        $subtotal  = round($cantidad * $precio, 2);
        $totalPedido += $subtotal;

        $stmtItem->execute([
            ':id_pedido'   => $idPedido,
            ':id_producto' => intval($item['id_producto'] ?? 0) ?: null,
            ':descripcion' => trim($item['descripcion']),
            ':cantidad'    => $cantidad,
            ':precio'      => $precio,
            ':subtotal'    => $subtotal,
        ]);
    }

    // 3. Actualizar total en la cabecera
    $pdo->prepare("UPDATE Pedidos SET Total = :total WHERE IdPedido = :id")
        ->execute([':total' => $totalPedido, ':id' => $idPedido]);

    $pdo->commit();

    log_event('INSERT', "Pedido ID=$idPedido creado. Cliente=$idCliente. Total=$totalPedido", __FILE__);
    echo json_encode([
        'status'  => 'ok',
        'data'    => ['id_pedido' => $idPedido, 'total' => $totalPedido],
        'message' => 'Pedido registrado correctamente'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    log_event('ERROR', "Rollback en pedido. " . $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar el pedido. Operación revertida.']);
}
exit;
```

### B. Carrito UI (JavaScript)

```javascript
// ============================================================
// Sistema de Carrito — sales_manager
// ============================================================

let carrito = [];

function agregarAlCarrito(producto) {
  const idx = carrito.findIndex(i => i.id_producto === producto.id_producto);
  if (idx >= 0) {
    carrito[idx].cantidad++;
  } else {
    carrito.push({ ...producto, cantidad: 1 });
  }
  renderCarrito();
}

function quitarDelCarrito(idProducto) {
  carrito = carrito.filter(i => i.id_producto !== idProducto);
  renderCarrito();
}

function renderCarrito() {
  const contenedor = document.getElementById('carrito-items');
  const total      = carrito.reduce((acc, i) => acc + i.precio_unitario * i.cantidad, 0);

  if (carrito.length === 0) {
    contenedor.innerHTML = '<p class="text-center text-gray-400 py-6">El carrito está vacío</p>';
  } else {
    contenedor.innerHTML = carrito.map(item => `
      <div class="bg-white rounded-[1.5rem] shadow-sm p-3 flex items-center justify-between mb-2">
        <div>
          <p class="font-semibold text-gray-800 text-sm">${item.descripcion}</p>
          <p class="text-xs text-gray-500">$${item.precio_unitario.toFixed(2)} × ${item.cantidad}</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="font-bold text-emerald-600">$${(item.precio_unitario * item.cantidad).toFixed(2)}</span>
          <button onclick="quitarDelCarrito(${item.id_producto})"
                  class="w-7 h-7 rounded-full bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs font-bold transition-colors">
            ✕
          </button>
        </div>
      </div>
    `).join('');
  }

  document.getElementById('total-pedido').textContent = `$${total.toFixed(2)}`;
  document.getElementById('btn-confirmar').disabled = carrito.length === 0;
}

async function confirmarPedido() {
  const idCliente = parseInt(document.getElementById('cliente-select').value);
  if (!idCliente) {
    Swal.fire({ icon: 'warning', title: 'Falta el cliente', text: 'Seleccioná un cliente antes de confirmar' });
    return;
  }

  const payload = {
    id_cliente: idCliente,
    items: carrito.map(i => ({
      id_producto:    i.id_producto,
      descripcion:    i.descripcion,
      cantidad:       i.cantidad,
      precio_unitario: i.precio_unitario,
    })),
    notas: document.getElementById('notas-pedido')?.value ?? '',
  };

  const btn = document.getElementById('btn-confirmar');
  btn.disabled = true;
  btn.textContent = 'Guardando...';

  try {
    const res  = await fetch('ajax_save_pedido.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const json = await res.json();

    if (json.status !== 'ok') throw new Error(json.message);

    carrito = [];
    renderCarrito();
    Swal.fire({ icon: 'success', title: `Pedido #${json.data.id_pedido} registrado`, text: `Total: $${json.data.total.toFixed(2)}`, confirmButtonText: 'Aceptar' });

  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error al guardar', text: err.message });
  } finally {
    btn.disabled = false;
    btn.textContent = 'Confirmar Pedido';
  }
}
```

### C. Cambio de Estado del Pedido

```php
// ajax_update_status_pedido.php
$idPedido  = intval($input['id_pedido']);
$nuevoEstado = intval($input['estado']); // 1=Pendiente, 2=Pagado, 3=Entregado, 0=Cancelado

$estados_validos = [0, 1, 2, 3];
if (!in_array($nuevoEstado, $estados_validos)) {
    echo json_encode(['status' => 'error', 'message' => 'Estado no válido']);
    exit;
}

$stmt = $pdo->prepare("UPDATE Pedidos SET Estado = :estado, FechaModificacion = NOW() WHERE IdPedido = :id");
$stmt->execute([':estado' => $nuevoEstado, ':id' => $idPedido]);
log_event('UPDATE', "Pedido ID=$idPedido → Estado=$nuevoEstado por uid=" . $_SESSION['userid'], __FILE__);
```

---

## 3. Estados del Pedido

| Código | Estado | Color UI |
|---|---|---|
| `1` | Pendiente | `text-amber-600 bg-amber-50` |
| `2` | Pagado | `text-emerald-600 bg-emerald-50` |
| `3` | Entregado | `text-blue-600 bg-blue-50` |
| `0` | Cancelado | `text-red-500 bg-red-50` |

---

## 4. Reglas de Negocio Críticas

| Regla | Implementación |
|---|---|
| **Atomicidad** | Cabecera + ítems dentro de un solo `beginTransaction()` / `commit()` |
| **Precio congelado** | El `PrecioUnitario` se captura al momento del insert, no se recalcula |
| **Validación client-side** | No enviar al servidor si el carrito está vacío o no hay cliente |
| **Estado inicial** | Siempre `Estado = 1` (Pendiente) al crear |
| **Trazabilidad de vendedor** | `$_SESSION['userid']` como `IdVendedor` |

---

## 5. Checklist Antes de Entregar

- [ ] ¿El INSERT de pedido está envuelto en `beginTransaction()` / `commit()` / `rollBack()`?
- [ ] ¿El `PrecioUnitario` se captura en el momento del INSERT y no se calcula posterior?
- [ ] ¿Se verifica que `id_cliente > 0` antes de procesar?
- [ ] ¿Se llama a `log_event()` tanto en éxito como en error (con rollback)?
- [ ] ¿El carrito UI respeta las Subplacas (`bg-white rounded-[1.5rem] shadow-sm`)?
- [ ] ¿El feedback de éxito usa `Swal.fire()` con el número de pedido y total?
- [ ] ¿Existe validación de estado al cambiar (`in_array($estado, [0,1,2,3])`)?
