# Gestion SB — Configuración para Gemini

> **IMPORTANTE**: Este archivo es el punto de entrada para Gemini CLI y entornos Gemini.
> La fuente de verdad arquitectónica completa está en **`AGENTS.md`** en la raíz del repositorio.
> **Leer `AGENTS.md` completo antes de cualquier acción.**

---

## Resumen Ejecutivo (Quick Reference)

- **Sistema**: Panel de administración Mobile-First para Stefy Barroso
- **Stack**: PHP 8.1 + PDO · Tailwind CSS · Vanilla JS · SweetAlert2
- **UI Pattern**: Sistema de Placas (Subplacas `rounded-[1.5rem] shadow-sm`)
- **Navegación**: SPA con Bottom Navbar — sin recargas de página
- **Autenticación**: PHP Sessions + `includes/security.php` en cada vista privada
- **Logging**: `log_event()` en todo INSERT/UPDATE/DELETE
- **Idioma de UI**: Español

## Protocolo Obligatorio

1. Leer `AGENTS.md` (arquitectura completa)
2. Leer la Directiva del módulo solicitado (`/directives/build_<modulo>.md`)
3. Leer el SKILL relevante (`.agent/skills/<skill>/SKILL.md`)
4. Escanear código existente antes de crear archivos nuevos
5. Ejecutar el Checklist del Skill antes de entregar

## Mapa Rápido de Módulos

| Módulo | Directiva | Ruta |
|---|---|---|
| Login | `build_login.md` | `admin/login.php` |
| Dashboard | `build_system.md` | `admin/index.php` |
| Clientes | `build_clientes.md` | `admin/apps/clientes/ver_clientes.php` |
| Atención CRM | `build_atencion.md` | `admin/apps/clientes/atencion_cliente.php` |
| Ventas/Pedidos | `build_ventas.md` | `admin/apps/ventas/ver_ventas.php` |

## Reglas No Negociables

- ❌ Sin `style="..."` inline en HTML
- ❌ Sin `mysqli_*` — solo PDO con Prepared Statements
- ❌ Sin frameworks JS pesados (React, Vue, etc.)
- ❌ Sin texto flotante en listados — solo Subplacas
- ✅ `log_event()` en toda escritura en BD
- ✅ SweetAlert2 para todos los diálogos
- ✅ UI en español, código en inglés

---

*Referencia completa: ver [`AGENTS.md`](./AGENTS.md)*
