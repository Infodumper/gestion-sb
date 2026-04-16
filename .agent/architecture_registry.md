# Architecture Registry — Gestion SB v2.0

> Mapa de componentes del sistema. Leer antes de crear o modificar archivos.
> Fuente canónica: `AGENTS.md` § 3 y § 6.

---

## Topología de 4 Capas

```
┌─────────────────────────────────────────────────────────┐
│  CAPA 1: DIRECTIVAS (/directives/)                      │
│  Reglas de negocio inmutables. Definen el QUÉ.          │
├─────────────────────────────────────────────────────────┤
│  CAPA 2: ORQUESTACIÓN (.agent/)                         │
│  Workflows, skills, conventions. Definen el CÓMO.       │
├─────────────────────────────────────────────────────────┤
│  CAPA 3: EJECUCIÓN (admin/ + execution/)                │
│  Código productivo PHP/JS. Lo que el usuario ve.        │
├─────────────────────────────────────────────────────────┤
│  CAPA 4: OBSERVABILIDAD (dashboard/ + logs/)            │
│  Monitoreo, auditoría, trazas.                          │
└─────────────────────────────────────────────────────────┘
```

---

## Árbol de Archivos Clave

```
gestion_sb/
│
├── AGENTS.md                    ← Protocolo de inicio para agentes (LEER PRIMERO)
├── GEMINI.md                    ← Quick-reference para Gemini CLI
├── CLAUDE.md                    ← Quick-reference para Claude
├── .env                         ← Credenciales (NUNCA en Git)
│
├── .agent/                      ← Sistema de orquestación de agentes
│   ├── workflow_registry.md     ← Índice de workflows y protocolo de selección
│   ├── conventions.md           ← Naming, código PHP/JS/CSS, logging
│   ├── data_contracts.md        ← DDL de tablas y contrato AJAX
│   ├── architecture_registry.md ← Este archivo
│   ├── skills/                  ← Skills especializados por dominio
│   │   ├── client_manager/SKILL.md
│   │   ├── sales_manager/SKILL.md
│   │   ├── menu_navigator/SKILL.md
│   │   ├── login_manager/SKILL.md
│   │   ├── premium_attention/SKILL.md
│   │   ├── dashboard_layout/SKILL.md
│   │   └── ...
│   └── workflows/               ← Pipelines de desarrollo
│       ├── create_module.md     ← /create_module
│       ├── fix_bug.md           ← /fix_bug
│       ├── debug_endpoint.md    ← /debug_endpoint
│       └── audit_module.md      ← /audit_module
│
├── admin/                       ← Panel administrativo (SPA Mobile-First)
│   ├── login.php                ← Punto de entrada
│   ├── index.php                ← Shell SPA con Bottom Navbar
│   └── apps/                   ← Módulos de negocio
│       ├── clientes/
│       │   ├── ver_clientes.php
│       │   ├── atencion_cliente.php
│       │   ├── ajax_save_client.php
│       │   ├── ajax_get_client_card.php
│       │   ├── ajax_buscar_clientes.php
│       │   ├── ajax_mark_contacted.php
│       │   └── partials/
│       │       ├── modal_nuevo_cliente.php
│       │       └── modal_editar_cliente.php
│       ├── ventas/
│       └── pedidos/
│
├── directives/                  ← Especificaciones de negocio por módulo
│   ├── build_system.md          ← Arquitectura base
│   ├── build_clientes.md
│   ├── build_ventas.md
│   ├── build_atencion.md
│   └── build_login.md
│
├── includes/                    ← Lógica compartida PHP
│   ├── security.php             ← Middleware de sesión + helpers (json_response, s, log_event)
│   ├── db.php                   ← Conexión PDO (lee .env)
│   ├── utils.php                ← fmt_money, clean_phone_wa, render_premium_header, get_gradient_avatar
│   └── load_env.php             ← Carga variables del .env
│
├── utils/                       ← Módulos de soporte
│   └── logger.php               ← log_event() — logger central PHP
│
├── styles/                      ← CSS del sistema
│   └── colores.css              ← Design System global (tokens, componentes, dark mode)
│
├── logs/                        ← Auditoría SQLite
│   └── gestion_sb.db
│
└── dashboard/                   ← Observabilidad Python/Flask
    └── app.py
```

---

## Mapa de Módulos

| Módulo | Directiva | Skills (Regla 1:3) | Ruta Principal |
|---|---|---|---|
| Login | `build_login.md` | `login_manager`, `auth_authenticator`, `session_guard` | `admin/login.php` |
| Dashboard | `build_system.md` | `menu_navigator`, `app_orchestrator`, `dashboard_layout` | `admin/index.php` |
| Clientes | `build_clientes.md` | `client_manager`, `premium_attention`, `client_profiler` | `admin/apps/clientes/ver_clientes.php` |
| Atención CRM | `build_atencion.md` | `premium_attention`, `client_profiler`, `client_manager` | `admin/apps/clientes/atencion_cliente.php` |
| Ventas | `build_ventas.md` | `sales_manager`, `catalog_manager`, `billing_helper` | `admin/apps/ventas/ver_ventas.php` |
| Pedidos | `build_ventas.md` | `sales_manager`, `catalog_manager`, `billing_helper` | `admin/apps/pedidos/ver_pedidos.php` |

---

## Flujo de Datos (Request → Response)

```
Browser (JS fetch)
    │
    ▼
admin/apps/<modulo>/ajax_<verbo>_<entidad>.php
    │
    ├─ includes/security.php   → check_auth() → session válida?
    │                                           NO → json 401
    ├─ includes/db.php         → $pdo ready
    ├─ utils/logger.php        → log_event() disponible
    │
    ├─ Validar input
    ├─ Query PDO (prepared)
    ├─ log_event() si es write
    │
    └─ json_encode({status, data, message})
    │
    ▼
Browser (json.status === 'ok' ? render : Swal.error)
```

---

## Dependencias de Archivos

| Archivo | Depende de |
|---|---|
| Cualquier endpoint `ajax_*.php` | `security.php` → `logger.php`, `db.php` → `load_env.php` |
| `security.php` | `utils/logger.php` (vía require) |
| `db.php` | `includes/load_env.php` |
| Vistas `ver_*.php` | `security.php`, `db.php`, `utils.php`, `styles/colores.css` |
| `logger.php` | `$pdo` global (opcional — fallback a `error_log`) |