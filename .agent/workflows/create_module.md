---
description: Proceso completo para crear un nuevo módulo de negocio en Gestion SB
---

# Workflow: Crear Módulo Nuevo

> Invocar con `/create_module`. Aplica la Regla 1:3 (una Directiva, tres Skills).

---

## Paso 0: Recopilación de Requisitos

Antes de escribir código, confirmar con el usuario:
- ¿Cuál es el nombre del módulo? (ej: `turnos`, `stock`, `proveedores`)
- ¿Cuáles son las entidades principales (tablas)?
- ¿Hay módulos relacionados ya existentes que este deba consultar?

---

## Paso 1: Crear la Directiva

Crear el archivo `/directives/build_<modulo>.md` con esta estructura:

```markdown
## Directiva: <Nombre del Módulo>
> **Skills Asociados:** skill_a, skill_b, skill_c

## Capa 1: Directiva (Objetivo y Alcance)
**Objetivo:** [descripción concisa de qué hace el módulo]

## Capa 2: Orquestación (Procesos de Negocio)
1. [flujo de negocio 1]
2. [flujo de negocio 2]

## Capa 3: Ejecución (Componentes y Archivos)
- **Maestro**: `admin/apps/<modulo>/ver_<modulo>.php`
- **Endpoints AJAX**: `ajax_save_<entidad>.php`, etc.

## Capa 4: Observabilidad
- [métricas y logs específicos]

## Capa 5: Base de Datos (Esquema)
### Tabla: `<Entidad>`
- PK: `Id<Entidad>` (INT, AUTO_INCREMENT)
- [campos críticos con tipos y constraints]
- [relaciones FK con ON DELETE CASCADE si aplica]
```

---

## Paso 2: Crear la Estructura de Carpetas

```powershell
# Crear carpeta del módulo
New-Item -ItemType Directory -Force -Path "admin/apps/<modulo>"
New-Item -ItemType Directory -Force -Path "admin/apps/<modulo>/partials"

# Listar resultado
Get-ChildItem "admin/apps/<modulo>"
```

---

## Paso 3: Crear los Archivos Base

### 3.1 Vista principal: `ver_<modulo>.php`

```php
<?php require_once __DIR__ . '/../../includes/security.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Nombre Módulo] — Gestion SB</title>
    <link rel="stylesheet" href="/styles/colores.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen pb-24">

    <!-- Placa Maestra -->
    <div class="max-w-lg mx-auto p-4">
        <header class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 font-baskerville">[Título del Módulo]</h1>
            <button onclick="abrirModal()" class="btn-primary">+ Nuevo</button>
        </header>

        <!-- Listado de Subplacas -->
        <div id="lista-<modulo>" class="space-y-3">
            <!-- Contenido cargado dinámicamente vía AJAX -->
        </div>
    </div>

    <!-- Bottom Navbar -->
    <?php include __DIR__ . '/../../partials/bottom_navbar.php'; ?>

    <script src="js/<modulo>.js"></script>
</body>
</html>
```

### 3.2 Endpoint AJAX: `ajax_get_<modulo>.php`

```php
<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->prepare("SELECT * FROM <Tabla> WHERE Estado = 1 ORDER BY <campo> ASC");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'ok', 'data' => $data]);
} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al obtener los datos']);
}
exit;
```

### 3.3 Endpoint AJAX: `ajax_save_<entidad>.php`

```php
<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$id    = intval($input['id'] ?? 0);
$campo = trim($input['campo'] ?? '');

if (empty($campo)) {
    echo json_encode(['status' => 'error', 'message' => 'El campo es obligatorio']);
    exit;
}

try {
    if ($id > 0) {
        // UPDATE
        $stmt = $pdo->prepare("UPDATE <Tabla> SET Campo = :campo WHERE Id<Entidad> = :id");
        $stmt->execute([':campo' => $campo, ':id' => $id]);
        log_event('UPDATE', "Entidad ID=$id actualizada", __FILE__);
        echo json_encode(['status' => 'ok', 'message' => 'Actualizado correctamente']);
    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO <Tabla> (Campo, CreadoPor) VALUES (:campo, :uid)");
        $stmt->execute([':campo' => $campo, ':uid' => $_SESSION['userid']]);
        $newId = $pdo->lastInsertId();
        log_event('INSERT', "Nueva entidad creada ID=$newId", __FILE__);
        echo json_encode(['status' => 'ok', 'data' => ['id' => $newId], 'message' => 'Creado correctamente']);
    }
} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar']);
}
exit;
```

---

## Paso 4: Crear la Lógica Frontend JavaScript

Crear `admin/apps/<modulo>/js/<modulo>.js`:

```javascript
// ============================================================
// Módulo: <Nombre>
// Patrón: Carga asíncrona → Render de Subplacas → Feedback Swal
// ============================================================

const API = {
  get: '/admin/apps/<modulo>/ajax_get_<modulo>.php',
  save: '/admin/apps/<modulo>/ajax_save_<entidad>.php',
};

async function cargarLista() {
  try {
    const res = await fetch(API.get);
    const json = await res.json();
    if (json.status !== 'ok') throw new Error(json.message);
    renderLista(json.data);
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
  }
}

function renderLista(items) {
  const contenedor = document.getElementById('lista-<modulo>');
  contenedor.innerHTML = items.length === 0
    ? '<p class="text-center text-gray-400 py-8">No hay registros.</p>'
    : items.map(renderSubplaca).join('');
}

function renderSubplaca(item) {
  return `
    <div class="bg-white rounded-[1.5rem] shadow-sm p-4 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-gray-800">${item.nombre}</h3>
        <p class="text-sm text-gray-500">${item.detalle ?? ''}</p>
      </div>
      <div class="flex gap-2">
        <button onclick="editarItem(${item.id})" class="btn-icon-edit">
          <!-- SVG Editar -->
        </button>
      </div>
    </div>
  `;
}

async function guardarItem(datos) {
  try {
    const res = await fetch(API.save, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    });
    const json = await res.json();
    if (json.status !== 'ok') throw new Error(json.message);
    Swal.fire({ icon: 'success', title: '¡Guardado!', toast: true, timer: 2000, showConfirmButton: false });
    cargarLista();
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
  }
}

// Init
document.addEventListener('DOMContentLoaded', cargarLista);
```

---

## Paso 5: Registrar el Módulo

### 5.1 Registrar en AGENTS.md
Agregar entrada en la tabla "Mapa de Módulos" con: Módulo, Directiva, Skills, Ruta.

### 5.2 Registrar en el Bottom Navbar
Agregar el ícono/enlace del nuevo módulo al `partials/bottom_navbar.php`.

### 5.3 Registrar en el sistema de rutas SPA
Si el router JS del menú principal controla las vistas, agregar la nueva ruta.

---

## Paso 6: Validación Final (Checklist de Entrega)

- [ ] La vista principal usa `security.php` como primera línea
- [ ] Todos los endpoints retornan `{status, data, message}` en JSON
- [ ] Todos los queries usan PDO + Sentencias Preparadas
- [ ] Todas las acciones de write llaman a `log_event()`
- [ ] El listado usa Subplacas (`bg-white rounded-[1.5rem] shadow-sm`)
- [ ] No hay estilos `inline` en el HTML
- [ ] El módulo tiene su Directiva en `/directives/build_<modulo>.md`
- [ ] El módulo fue registrado en la tabla de AGENTS.md
- [ ] Funciona correctamente en viewport 390px (Chrome Mobile)

---

## Paso 7: Documentar Cambios

Actualizar `CHANGELOG.md`:

```markdown
## [<fecha>] — Módulo <Nombre>
### Nuevo
- Módulo `<modulo>` creado: `admin/apps/<modulo>/`
- Directiva registrada: `directives/build_<modulo>.md`
- Endpoints: `ajax_get_<modulo>.php`, `ajax_save_<entidad>.php`
```