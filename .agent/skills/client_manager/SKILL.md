---
name: client_manager
description: Especialista transaccional (CRUD) y middleware de validación de datos de clientes.
directiva: directives/build_clientes.md
---

# Skill: client_manager

## 1. Rol y Responsabilidad

Eres el agente **client_manager**. Gobiernan tu comportamiento dos mandatos:

1. **Integridad de datos**: La base de clientes debe estar limpia, sin duplicados y siempre normalizada.
2. **UI "Subplaca" premium**: El listado de clientes nunca muestra datos sueltos — cada cliente habita en su propia tarjeta visual.

Leer la directiva completa antes de ejecutar: `directives/build_clientes.md`.

---

## 2. Patrones de Código Obligatorios

### A. Endpoint de Alta de Cliente (`ajax_save_client.php`)

```php
<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$input    = json_decode(file_get_contents('php://input'), true);
$nombre   = trim($input['nombre'] ?? '');
$telefono = preg_replace('/\D/', '', trim($input['telefono'] ?? '')); // Solo dígitos
$dni      = preg_replace('/[\s\-]/', '', trim($input['dni'] ?? ''));
$fechaNac = $input['fecha_nac'] ?? null;
$id       = intval($input['id'] ?? 0);

// Validación básica
if (empty($nombre) || strlen($telefono) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Nombre y teléfono son obligatorios']);
    exit;
}

try {
    // Prevención de duplicados por teléfono
    $check = $pdo->prepare("SELECT IdCliente FROM Clientes WHERE Telefono = :tel AND IdCliente != :id");
    $check->execute([':tel' => $telefono, ':id' => $id]);
    if ($check->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Ya existe un cliente con ese teléfono']);
        exit;
    }

    if ($id > 0) {
        $stmt = $pdo->prepare("
            UPDATE Clientes SET NombreCompleto = :nombre, Telefono = :tel, Dni = :dni, FechaNac = :fnac
            WHERE IdCliente = :id
        ");
        $stmt->execute([':nombre' => $nombre, ':tel' => $telefono, ':dni' => $dni ?: null, ':fnac' => $fechaNac, ':id' => $id]);
        log_event('UPDATE', "Cliente ID=$id actualizado por uid=" . $_SESSION['userid'], __FILE__);
        echo json_encode(['status' => 'ok', 'message' => 'Cliente actualizado correctamente']);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO Clientes (NombreCompleto, Telefono, Dni, FechaNac, CreadoPor, Estado)
            VALUES (:nombre, :tel, :dni, :fnac, :uid, 1)
        ");
        $stmt->execute([':nombre' => $nombre, ':tel' => $telefono, ':dni' => $dni ?: null, ':fnac' => $fechaNac, ':uid' => $_SESSION['userid']]);
        $newId = $pdo->lastInsertId();
        log_event('INSERT', "Nuevo cliente ID=$newId creado por uid=" . $_SESSION['userid'], __FILE__);
        echo json_encode(['status' => 'ok', 'data' => ['id' => $newId], 'message' => 'Cliente registrado correctamente']);
    }
} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor']);
}
exit;
```

### B. Render de Subplaca de Cliente (JS)

```javascript
function renderClienteSubplaca(c) {
  const iniciales = c.nombre.split(' ').map(p => p[0]).slice(0,2).join('').toUpperCase();
  const esCumple  = c.es_cumple_hoy === '1';

  return `
    <div class="bg-white rounded-[1.8rem] shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <!-- Avatar con iniciales -->
      <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-sm"
           style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary))">
        ${iniciales}
      </div>

      <!-- Info principal -->
      <div class="flex-1 min-w-0">
        <h3 class="font-semibold text-gray-800 truncate">${c.nombre}</h3>
        <p class="text-sm text-gray-500">${c.telefono}</p>
        ${esCumple ? '<span class="text-xs text-emerald-600 font-medium">🎂 ¡Cumpleaños hoy!</span>' : ''}
      </div>

      <!-- Acciones rápidas -->
      <div class="flex gap-1 flex-shrink-0">
        <button onclick="editarCliente(${c.id})" title="Editar"
                class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
          <!-- SVG Editar -->
          <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </button>

        <a href="https://wa.me/54${c.telefono}?text=${encodeURIComponent('Hola ' + c.nombre + '!')}"
           target="_blank"
           class="w-9 h-9 rounded-full bg-emerald-100 hover:bg-emerald-200 flex items-center justify-center transition-colors"
           title="WhatsApp">
          <!-- SVG WhatsApp -->
          <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.113 1.528 5.84L.057 23.999l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.865 0-3.605-.507-5.102-1.388l-.366-.217-3.737.98.997-3.648-.239-.376A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
          </svg>
        </a>
      </div>
    </div>
  `;
}
```

### C. Búsqueda Dinámica (Frontend)

```javascript
let debounceTimer;
document.getElementById('buscador').addEventListener('input', function() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => buscarClientes(this.value.trim()), 300);
});

async function buscarClientes(query) {
  const res  = await fetch(`ajax_search_client.php?q=${encodeURIComponent(query)}`);
  const json = await res.json();
  if (json.status === 'ok') renderLista(json.data);
}
```

---

## 3. Reglas de Negocio Críticas

| Regla | Implementación |
|---|---|
| **Teléfono único** | `UNIQUE` en BD + check PDO antes del INSERT |
| **Normalización telefónica** | `preg_replace('/\D/', '', $tel)` — solo dígitos |
| **Normalización DNI** | `preg_replace('/[\s\-]/', '', $dni)` |
| **Estado al crear** | Siempre `Estado = 1` (Activo) en el INSERT |
| **Trazabilidad** | Guardar `$_SESSION['userid']` como `CreadoPor` |
| **Borrado lógico** | Nunca `DELETE` — cambiar `Estado = 0` |

---

## 4. Checklist Antes de Entregar

- [ ] ¿El teléfono se normaliza con `preg_replace('/\D/', '', $tel)` antes del INSERT?
- [ ] ¿Hay verificación de duplicado por teléfono antes del INSERT?
- [ ] ¿Todos los queries usan PDO Prepared Statements?
- [ ] ¿Se llama a `log_event()` en cada INSERT/UPDATE?
- [ ] ¿El listado renderiza Subplacas (no tablas HTML ni texto suelto)?
- [ ] ¿El avatar usa iniciales con gradiente (no emojis de persona)?
- [ ] ¿Los botones de WhatsApp y llamada tienen áreas táctiles mínimas de 44px?
- [ ] ¿El buscador usa `debounce` de al menos 300ms?
