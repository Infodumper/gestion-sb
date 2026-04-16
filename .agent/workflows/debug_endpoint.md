---
description: Depurar un endpoint AJAX que no responde correctamente o devuelve errores
---

# Workflow: Depurar Endpoint AJAX `/debug_endpoint`

> Usar cuando un endpoint retorna 500, JSON malformado, o el frontend no recibe lo esperado.

---

## Paso 0: Síntomas y Datos de Entrada

Recopilar antes de proceder:

```
Endpoint: admin/apps/<modulo>/ajax_<verbo>_<entidad>.php
Método: GET | POST
Payload enviado: { ... }
Error recibido: [status code] + [body]
Contexto: ¿Desde qué vista se llama?
```

---

## Paso 1: Verificar el Flujo de Inclusión

El orden de includes en un endpoint correcto es **siempre** este:

```php
<?php
// ① Security PRIMERO — hace session_start() y check_auth()
require_once __DIR__ . '/../../../includes/security.php';

// ② DB SEGUNDO — necesita que session exista
require_once __DIR__ . '/../../../includes/db.php';

// ③ Utils TERCERO (si se necesita)
require_once __DIR__ . '/../../../includes/utils.php';

// ④ Header JSON — ANTES de cualquier output
header('Content-Type: application/json; charset=utf-8');
```

**Error común**: `header()` after body started → PHP generó output antes del header.

---

## Paso 2: Diagnóstico por Código de Error

### 🔴 HTTP 500 (Error interno)

```php
// Activar modo debug TEMPORALMENTE (solo en desarrollo):
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ⚠️ Remover antes de producción
```

Causas típicas:
- Query SQL con columna/tabla inexistente
- `$pdo` no disponible porque `db.php` falló silenciosamente
- Exception no capturada fuera del try/catch

### 🔴 HTTP 401 (No autenticado)

```php
// Verificar en security.php qué condición falló:
var_dump($_SESSION); // ¿Existe 'userid'?
```

Causas típicas:
- `session_start()` llamado dos veces (conflicto con security.php)
- Cookie de sesión expirada (timeout de 8h)
- Endpoint cargado en un context sin sesión (iframe cross-origin)

### 🟡 200 pero JSON malformado (SyntaxError en JS)

PHP imprimió algo ANTES o DESPUÉS del JSON. Checklist:

```php
// ① Buscar BOM o whitespace antes de <?php
// ② Buscar echo/print fuera del JSON
// ③ Buscar warnings de PHP incluidos en la salida

// FIX: Capturar y descartar output previo
ob_start();
// ... includes ...
ob_clean(); // Limpiar todo lo que PHP haya impreso
header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultado);
```

### 🟡 200 pero `data` es null o vacío

```php
// Verificar que fetchAll/fetch retorna datos
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
error_log("Rows count: " . count($rows)); // Temporal
```

Causas típicas:
- Filtro WHERE demasiado restrictivo (`Estado = 1` cuando todos son `Estado = 0`)
- Parámetro bindeado como string cuando BD espera int

---

## Paso 3: Validar el Contrato de Respuesta

Todo endpoint correcto pasa este test:

```javascript
// En DevTools console del navegador:
fetch('/admin/apps/<modulo>/ajax_<verbo>_<entidad>.php')
  .then(r => r.json())
  .then(json => {
    console.assert(json.status === 'ok' || json.status === 'error', 'Falta status');
    console.assert('message' in json, 'Falta message');
    console.log('✅ Contrato OK:', json);
  })
  .catch(err => console.error('❌ JSON inválido:', err));
```

---

## Paso 4: Validar el Consumo en el Frontend

El JS que consume el endpoint debe seguir este patrón:

```javascript
// ✅ PATRÓN CORRECTO
const res  = await fetch(url, options);
const json = await res.json();

if (json.status !== 'ok') {
    Swal.fire({ icon: 'error', title: 'Error', text: json.message });
    return;
}

// Usar json.data aquí
renderLista(json.data);

// ❌ PATRÓN INCORRECTO (legacy)
if (data.success) { ... }     // success no existe en el contrato
if (data[0].campo) { ... }    // data puede ser null
```

---

## Paso 5: Checklist de Resolución

- [ ] El endpoint empieza con `security.php` (no `session_start()` manual)
- [ ] `header('Content-Type: application/json')` antes de cualquier echo
- [ ] El try/catch captura `Exception` (no solo `PDOException`)
- [ ] En el catch: `http_response_code(500)` + `log_event('ERROR', ...)` + `json_encode error`
- [ ] El JS lee `json.status === 'ok'` (no `json.success`)
- [ ] El JS accede a `json.data` (no `json` directamente)
- [ ] No hay `print_r`, `var_dump` ni `echo` sueltos en el endpoint

---

## Referencia: Template de Endpoint Correcto

```php
<?php
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response('error', null, 'Método no permitido', 405);
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    json_response('error', null, 'ID inválido', 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM Tabla WHERE IdEntidad = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        json_response('error', null, 'Registro no encontrado', 404);
    }

    json_response('ok', $row, 'Éxito');

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    json_response('error', null, 'Error interno del servidor', 500);
}
```
