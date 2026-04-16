---
description: Auditoría de un módulo existente para verificar que cumple con los estándares de AGENTS.md v2.0
---

# Workflow: Auditar Módulo `/audit_module`

> Usar cuando se hereda código de un módulo existente, o antes de hacer una PR/deploy.
> Produce un reporte claro de qué cumple y qué debe corregirse.

---

## Paso 0: Identificar el Módulo a Auditar

```
Módulo: <nombre>
Ruta principal: admin/apps/<modulo>/ver_<modulo>.php
Directiva: directives/build_<modulo>.md
```

---

## Paso 1: Checklist de Seguridad

Leer el archivo principal de la vista (`ver_<modulo>.php`) y verificar:

| Ítem | Cómo verificar | ¿OK? |
|---|---|---|
| `security.php` es el primer `require_once` | Línea 1-3 del archivo | ☐ |
| No hay bypass de auth (`REMOTE_ADDR`, `$_GET['debug']`) | grep "REMOTE_ADDR" y "debug" en el archivo | ☐ |
| Sesión verificada antes de cualquier query | `check_auth()` en security.php | ☐ |
| No hay datos sensibles en HTML renderizado | Revisar output de `print_r`, `var_dump` | ☐ |

---

## Paso 2: Checklist de Contrato AJAX

Para cada archivo `ajax_*.php` del módulo:

| Ítem | Criterio | ¿OK? |
|---|---|---|
| Header `Content-Type: application/json` | Primera línea después de includes | ☐ |
| Retorna `{status: 'ok'|'error', data, message}` | Revisar echo json_encode() | ☐ |
| Usa `PDO` + prepared statements | Sin `mysqli_*`, sin concatenación en SQL | ☐ |
| `log_event()` en todo INSERT/UPDATE/DELETE | Después de cada operación de escritura | ☐ |
| Try/catch con http_response_code en error | 500 en catch, 400 en validación | ☐ |
| NO tiene `session_start()` manual | security.php lo maneja | ☐ |

---

## Paso 3: Checklist de Frontend

Revisar todo el HTML del módulo:

| Ítem | Criterio | ¿OK? |
|---|---|---|
| Cero `style=""` inline en HTML | grep 'style=' en el archivo | ☐ |
| Cero `<style>` blocks dentro del HTML | grep '<style' | ☐ |
| Usa clases de `colores.css` (`.subplaca`, `.btn-primary`) | Ver CSS classes usadas | ☐ |
| Sin emojis como íconos funcionales | Reemplazar con SVG inline o Heroicons | ☐ |
| Sin `alert()` o `console.log()` en producción | grep 'console.log' | ☐ |
| SweetAlert2 para todos los diálogos | grep 'Swal.fire' | ☐ |
| Fetch JS lee `json.status === 'ok'` | (no `json.success`) | ☐ |

---

## Paso 4: Checklist Mobile-First

Abrir Chrome DevTools en viewport 390px:

| Ítem | Criterio | ¿OK? |
|---|---|---|
| Bottom Navbar visible y funcional | No se corta ni superpone | ☐ |
| Botones táctiles ≥ 44px de alto | CSS `min-height: 44px` | ☐ |
| Texto legible sin zoom | `font-size` ≥ 14px | ☐ |
| Subplacas no hacen overflow horizontal | `overflow-x: hidden` en body | ☐ |
| Modal/overlay cierra con Escape | eventListener en `keydown` | ☐ |

---

## Paso 5: Checklist de Base de Datos

Revisar el DDL de la tabla del módulo vs la Directiva:

| Ítem | Criterio | ¿OK? |
|---|---|---|
| PK nombrado `Id<Entidad>` | Ej: `IdCliente`, no `id` o `cliente_id` | ☐ |
| Existen índices en columnas de búsqueda frecuente | FK, columnas de filtro | ☐ |
| Campos opcionales tienen DEFAULT o NULL | No rompen INSERT simple | ☐ |
| FKs definidas con ON DELETE correcta | CASCADE o SET NULL según negocio | ☐ |

---

## Paso 6: Generar Reporte de Auditoría

Producir un resumen como este:

```markdown
## Auditoría: Módulo <Nombre> — <Fecha>

### ✅ Cumple
- security.php como primera línea
- Contrato JSON {status, data, message}
- PDO + Prepared Statements

### ⚠️ A Corregir (Prioridad Media)
- [ ] Botón "Exportar" usa emoji 📊 → reemplazar con SVG
- [ ] ajax_get_X.php no tiene log_event en SELECT (opcional pero recomendado)

### ❌ Crítico (Debe corregirse antes de producción)
- [ ] ajax_save_X.php retorna {success: true} — viola contrato
- [ ] ver_X.php tiene bypass de auth con REMOTE_ADDR
```

---

## Comandos útiles para la auditoría

```powershell
# Buscar style="" inline en todo el módulo
Select-String -Path "admin/apps/<modulo>/*.php" -Pattern 'style="'

# Buscar session_start() duplicados
Select-String -Path "admin/apps/<modulo>/*.php" -Pattern 'session_start'

# Buscar {success: en endpoints
Select-String -Path "admin/apps/<modulo>/ajax_*.php" -Pattern "'success'"

# Buscar REMOTE_ADDR bypass
Select-String -Path "admin/apps/<modulo>/*.php" -Pattern 'REMOTE_ADDR'

# Buscar console.log en JS
Select-String -Path "admin/apps/<modulo>/js/*.js" -Pattern 'console\.log'
```
