# Convenciones del Sistema — Gestion SB v2.0

> Referencia rápida de todos los estándares de codificación.
> Ver `AGENTS.md` para el contexto completo.
> **Estas convenciones son no negociables.**

---

## 1. Naming Conventions

| Elemento | Convención | Ejemplo correcto | ❌ Incorrecto |
|---|---|---|---|
| Archivos AJAX | `ajax_<verbo>_<entidad>.php` | `ajax_save_client.php` | `saveCliente.php` |
| Archivos de vista | `ver_<entidad>.php` | `ver_clientes.php` | `clients.php` |
| Archivos parciales | `modal_<entidad>_<accion>.php` | `modal_nuevo_cliente.php` | `new_client_modal.php` |
| Tablas de BD | PascalCase | `Clientes`, `ItemsPedido` | `clientes`, `items_pedido` |
| PKs de BD | `Id<Entidad>` | `IdCliente`, `IdPedido` | `id`, `cliente_id` |
| Variables PHP | camelCase | `$idCliente`, `$telVal` | `$id_cliente`, `$TEL` |
| Funciones PHP | camelCase | `log_event()`, `clean_phone_wa()` | `logEvent()`, `CleanPhone()` |
| Funciones JS | camelCase | `abrirModalNuevo()`, `renderLista()` | `open_modal()`, `RenderList()` |
| ID de elementos HTML | kebab-case | `modal-nuevo-cliente` | `modalNuevoCliente` (en HTML) |

---

## 2. Idioma

| Contexto | Idioma | Ejemplo |
|---|---|---|
| Texto visible al usuario | **Español** | `"Cliente guardado correctamente"` |
| Variables y funciones PHP | Inglés | `$clientId`, `function saveClient()` |
| Funciones y variables JS | Inglés | `async function loadList()` |
| Comentarios de código | Español o inglés (consistente en el archivo) | `// Verificar duplicado de teléfono` |
| Nombres de columnas BD | PascalCase inglés | `FechaNac`, `TelefonoWA` |

---

## 3. PHP — Reglas Estrictas

```php
// ✅ CORRECTO — PDO + Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM Clientes WHERE IdCliente = ?");
$stmt->execute([$id]);

// ❌ PROHIBIDO — concatenación directa
$result = mysqli_query($conn, "SELECT * WHERE id = $id");

// ✅ CORRECTO — Contrato JSON
echo json_encode(['status' => 'ok', 'data' => $data, 'message' => 'OK']);

// ❌ PROHIBIDO — Contrato legacy
echo json_encode(['success' => true, 'result' => $data]);

// ✅ CORRECTO — Primer require en todo archivo privado
require_once __DIR__ . '/../../../includes/security.php';

// ❌ PROHIBIDO — session_start() manual en endpoints
session_start();
if (!isset($_SESSION['userid'])) { ... }
```

---

## 4. JavaScript — Reglas Estrictas

```javascript
// ✅ CORRECTO — async/await con manejo de error
async function guardar(datos) {
    try {
        const res  = await fetch(URL, { method: 'POST', body: formData });
        const json = await res.json();
        if (json.status !== 'ok') throw new Error(json.message);
        // Usar json.data aquí
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
    }
}

// ❌ PROHIBIDO — Legacy promise chain con data.success
fetch(URL).then(r => r.json()).then(data => {
    if (data.success) { ... }
});

// ✅ CORRECTO — SweetAlert2 para todo feedback
Swal.fire({ icon: 'success', title: '¡Guardado!', timer: 1800, showConfirmButton: false });

// ❌ PROHIBIDO — alert() nativo
alert('Guardado');
```

---

## 5. CSS / HTML — Reglas Estrictas

```html
<!-- ✅ CORRECTO — Clases de colores.css -->
<div class="subplaca-adn">...</div>
<button class="btn-primary">Guardar</button>
<span class="badge badge-pagado">Pagado</span>

<!-- ❌ PROHIBIDO — inline styles -->
<div style="background: white; border-radius: 24px;">...</div>

<!-- ❌ PROHIBIDO — style blocks en HTML -->
<style>
  .mi-clase { color: red; }
</style>

<!-- ✅ CORRECTO — SVG para íconos funcionales -->
<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">...</svg>

<!-- ❌ PROHIBIDO — emojis como íconos funcionales -->
<button>📊 Exportar</button>
```

---

## 6. Logging — Reglas

```php
// ✅ SIEMPRE log_event() en escrituras de BD
$stmt->execute($params);
log_event('INSERT', "Nuevo cliente ID=$id creado", __FILE__);

// log_event() en errores capturados
} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
}

// Niveles disponibles:
// INSERT · UPDATE · DELETE · AUTH_OK · AUTH_FAIL · ERROR · WARN · INFO
```

---

## 7. Indentación y Formato

- **PHP**: 4 espacios (sin tabs)
- **JavaScript**: 2 espacios
- **HTML**: 4 espacios
- **Largo de línea máximo**: 120 caracteres
- **Apertura de llaves**: misma línea en PHP (`function foo() {`)
