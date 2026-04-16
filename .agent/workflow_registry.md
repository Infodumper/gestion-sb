# Workflow Registry — Gestion SB

Este archivo es el índice de todos los workflows disponibles para agentes.
**Leer antes de implementar cualquier tarea.**

---

## Protocolo de Selección

```
¿Qué está pidiendo el usuario?
│
├─ Crear algo nuevo desde cero → /create_module
├─ Algo no funciona → /fix_bug
├─ Un endpoint no responde bien → /debug_endpoint
└─ Validar código existente antes de deploy → /audit_module
```

---

## Workflows Disponibles

| Comando | Archivo | Cuándo usarlo |
|---|---|---|
| `/create_module` | `workflows/create_module.md` | Crear un nuevo módulo de negocio completo (Vista + Endpoints + Directiva) |
| `/fix_bug` | `workflows/fix_bug.md` | Diagnosticar y corregir un error en código existente |
| `/debug_endpoint` | `workflows/debug_endpoint.md` | Depurar un endpoint AJAX que falla o retorna mal JSON |
| `/audit_module` | `workflows/audit_module.md` | Auditar un módulo contra los estándares de AGENTS.md |

---

## Flujo de Decisión General (8 Pasos)

1. **Leer `AGENTS.md`** completo si no lo hiciste en esta sesión
2. **Identificar la intención** del usuario (crear / corregir / auditar / consultar)
3. **Seleccionar el workflow** de la tabla de arriba
4. **Leer la Directiva del módulo** en `/directives/build_<modulo>.md`
5. **Leer el SKILL relevante** en `.agent/skills/<skill>/SKILL.md`
6. **Escanear el código existente** antes de crear archivos nuevos
7. **Implementar** siguiendo el workflow elegido paso a paso
8. **Ejecutar el Checklist del workflow** antes de reportar "listo"

---

## Regla de No-Improvisación

Si la tarea no encaja en ningún workflow existente:
1. Preguntar al usuario antes de proceder
2. Aplicar los principios de `conventions.md` + `AGENTS.md`
3. Documentar el nuevo patrón con `mem_save()` para sesiones futuras
