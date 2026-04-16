---
description: Workflow para diagnosticar y corregir bugs en cualquier módulo de Gestion SB
---

# Workflow: Corregir Bug `/fix_bug`

> Usar cuando hay un error reportado (PHP, JS, SQL, UI). La prioridad es **no romper nada más**.

---

## Paso 0: Capturar el Síntoma

Antes de tocar código, responder:

| Pregunta | Respuesta esperada |
|---|---|
| ¿Cuál es el mensaje de error exacto? | Stack trace, consola JS, o descripción del usuario |
| ¿En qué archivo/endpoint ocurre? | Ej: `ajax_save_client.php`, `ver_clientes.php` |
| ¿Es reproducible? | Siempre / A veces / Solo en móvil |
| ¿Qué acción lo desencadena? | Ej: "al hacer click en Guardar" |

---

## Paso 1: Leer el Código Existente ANTES de Editar

```powershell
# Ver el archivo afectado completo
Get-Content admin/apps/<modulo>/<archivo>.php
```

**Regla de oro:** No hacer cambios sin leer el contexto completo. Un bug raramente vive solo
en una línea.

---

## Paso 2: Identificar la Causa Raíz

Clasificar el tipo de bug:

| Tipo | Síntomas típicos | Dónde buscar |
|---|---|---|
| **Auth** | 401, redirect a login inesperado | `includes/security.php`, `check_auth()` |
| **SQL** | Error PDO, datos no se guardan | Endpoint `ajax_*.php`, query SQL |
| **JSON** | "SyntaxError: Unexpected token" en JS | Endpoint AJAX, header `Content-Type` |
| **Contrato** | JS lee `data.success` pero endpoint retorna `data.status` | Endpoint + fetch JS |
| **UI/CSS** | Estilos rotos, overflow, layout | `colores.css`, clase en el HTML |
| **Session** | Logout inesperado, datos de sesión perdidos | `security.php`, `session_start()` |

---

## Paso 3: Aplicar el Fix Mínimo

**Principio de cirugía mínima**: Cambiar exactamente lo que está roto, no refactorizar.

```php
// ❌ MAL: Refactorizar el archivo entero por un bug de una línea
// ✅ BIEN: Corregir solo la línea/bloque afectado

// Ejemplo: contrato roto
// ANTES:
echo json_encode(['success' => true, 'message' => '...']);
// DESPUÉS:
echo json_encode(['status' => 'ok', 'message' => '...']);
```

---

## Paso 4: Verificar que el Fix no Rompe Nada

Checklist post-fix:

- [ ] El endpoint todavía retorna `{status, data, message}`
- [ ] `security.php` sigue siendo la primera línea del archivo
- [ ] No se agregaron `style=""` inline
- [ ] El `log_event()` sigue presente en operaciones de escritura
- [ ] El JS que consume el endpoint usa `json.status === 'ok'`

---

## Paso 5: Registrar el Fix en Memoria

Siempre guardar lo que se aprendió:

```
mem_save(
  title: "Fix: [descripción breve]",
  type: "bugfix",
  content: "**What**: ... **Why**: ... **Where**: ... **Learned**: ..."
)
```

---

## Paso 6: Documentar en CHANGELOG.md

```markdown
## [FECHA] — Bugfix: <Módulo>
### Corregido
- [Descripción del bug]
- **Causa**: [Causa raíz]
- **Fix**: [Qué se cambió]
- **Archivo**: `path/to/file.php`
```

---

## Errores Frecuentes del Sistema (Referencia Rápida)

| Error | Causa | Fix |
|---|---|---|
| `"SyntaxError: Unexpected token '<'"` | PHP imprimió HTML (error/warning) antes del JSON | Revisar warnings de PHP, agregar `error_reporting(0)` al inicio del endpoint en producción |
| `"Sesión expirada"` al cargar | `session_start()` duplicado o no ejecutado | Eliminar `session_start()` manual — `security.php` lo hace automáticamente |
| Fetch devuelve 401 | `check_auth()` falló — sesión no iniciada | Verificar que `security.php` se incluye como primer require |
| `data.success` undefined en JS | Endpoint usa `{status}` pero JS espera `{success}` | Actualizar JS a `json.status === 'ok'` |
| Subplaca no aparece en lista | AJAX busca `json.data` pero endpoint retorna array raíz | Envolver respuesta: `['status'=>'ok','data'=>$arr]` |
| DNI/Teléfono duplicado en INSERT | Falta `unique constraint` en la BD + no se valida en PHP | Agregar check duplicado antes del INSERT con `SELECT ... LIMIT 1` |
