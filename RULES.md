# RULES — Gestion SB Workspace

> Este archivo es leído automáticamente por el agente al inicio de cada sesión.
> Define las reglas de comportamiento que **nunca pueden ser ignoradas**.

---

## REGLA 0: Protocolo de Inicio (Obligatorio)

Al empezar cualquier sesión en este workspace:

1. Leer `AGENTS.md` completo
2. Leer `.agent/workflow_registry.md` para seleccionar el proceso correcto
3. Consultar memoria persistente con `mem_context()` para recuperar contexto anterior
4. Verificar el código existente relevante **antes** de generar código nuevo

❌ **Prohibido escribir código antes de ejecutar estos 4 pasos.**

---

## REGLA 1: El Contrato JSON es Inviolable

Todo endpoint AJAX del sistema retorna:

```json
{ "status": "ok | error", "data": { ... } | null, "message": "..." }
```

- `status`: siempre presente, siempre `"ok"` o `"error"`
- `message`: siempre en español
- `data`: presente en éxito, `null` en error

El JS que consume el endpoint siempre usa `json.status === 'ok'`.
**No existe `json.success`. No existe `json.result`. No existe `json.error`.**

---

## REGLA 2: Security First

```php
// Primera línea de TODA vista o endpoint privado:
require_once __DIR__ . '/../../../includes/security.php';
```

`security.php`:
- Inicia la sesión (no llamar `session_start()` manualmente)
- Ejecuta `check_auth()` automáticamente
- Provee `json_response()`, `s()`, `log_event()`

**Prohibido**: bypass de auth por `REMOTE_ADDR`, `$_GET['debug']`, o cualquier condición especial.

---

## REGLA 3: Zero Inline Styles

```html
<!-- ❌ PROHIBIDO en cualquier archivo del proyecto -->
<div style="background: white; border-radius: 24px;">

<!-- ❌ PROHIBIDO -->
<style>.mi-clase { ... }</style>

<!-- ✅ CORRECTO — usar clases de colores.css o Tailwind -->
<div class="subplaca-adn">
```

Si falta una clase en `colores.css`, **agregarla al Design System**, no usar `style=""`.

---

## REGLA 4: PDO Siempre

```php
// ❌ PROHIBIDO en cualquier circunstancia
$result = mysqli_query($conn, "SELECT ... WHERE id = $id");

// ✅ OBLIGATORIO
$stmt = $pdo->prepare("SELECT ... WHERE IdEntidad = ?");
$stmt->execute([$id]);
```

---

## REGLA 5: Log Everything que Escribe

```php
// Después de CADA INSERT / UPDATE / DELETE:
log_event('INSERT', "Descripción: quién, qué, id", __FILE__);
```

**Niveles**: `INSERT` · `UPDATE` · `DELETE` · `AUTH_OK` · `AUTH_FAIL` · `ERROR` · `WARN`

---

## REGLA 6: Design System de Subplacas

**Cada entidad de negocio vive dentro de una Subplaca.** No hay texto flotante en listados.

```html
<!-- Subplaca estándar -->
<div class="subplaca">...</div>

<!-- Subplaca ADN (más énfasis) -->
<div class="subplaca-adn">
    <div class="subplaca-acento"></div>
    <div class="subplaca-cuerpo">...</div>
</div>
```

---

## REGLA 7: Navegación SPA — Sin Recargas

- La navegación entre módulos se hace via `fetch()` → inyectar en `<main id="contenedor-principal">`
- **Prohibido**: `<a href="...">` para navegación entre módulos principales
- Búsquedas y filtros: **AJAX con debounce**, no `<form method="GET">`

---

## REGLA 8: Feedback con SweetAlert2 Siempre

```javascript
// ✅ Éxito
Swal.fire({ icon: 'success', title: '¡Guardado!', timer: 1800, showConfirmButton: false });

// ✅ Error
Swal.fire({ icon: 'error', title: 'Error', text: json.message });

// ❌ PROHIBIDO
alert('Guardado');
```

---

## REGLA 9: Memoria Persistente

Al terminar cualquier tarea significativa, guardar en memoria:

```
mem_save(title, type, content)
```

Guardar después de:
- Bug corregido → `type: 'bugfix'`
- Decisión de arquitectura → `type: 'decision'`
- Descubrimiento no obvio → `type: 'discovery'`
- Patrón establecido → `type: 'pattern'`

Al finalizar la sesión: `mem_session_summary(...)`.

---

## REGLA 10: No Duplicate Logic

Antes de crear cualquier función, clase o helper:

1. Buscar en `includes/utils.php` — helpers PHP compartidos
2. Buscar en `styles/colores.css` — estilos y componentes CSS
3. Buscar en `utils/logger.php` — logging
4. Buscar en los archivos existentes del módulo

Si ya existe algo parecido: **reutilizar, no duplicar**.

---

## Tabla de Prohibiciones Absolutas

| Prohibido | Alternativa |
|---|---|
| `mysqli_*` | PDO + prepared statements |
| `style="..."` en HTML | Clases de `colores.css` o Tailwind |
| `<style>` blocks en PHP/HTML | Agregar a `colores.css` |
| `session_start()` manual en endpoints | `security.php` como primer require |
| `alert()` o `confirm()` nativos | SweetAlert2 |
| Emojis como íconos funcionales | SVG inline o Heroicons |
| `data.success` en JS | `json.status === 'ok'` |
| Credenciales en código | `.env` + `load_env.php` |
| Texto de UI en inglés | Español obligatorio |
