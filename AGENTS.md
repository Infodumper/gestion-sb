# Arquitectura y Estándares de Sistema — "Gestion SB"

> **Versión**: 2.0 · **Estado**: Producción · **Alcance**: Todos los agentes de codificación (Gemini, Claude, Cursor, etc.)

Este documento es la fuente de verdad arquitectónica. Todo agente o pipeline que intervenga en este repositorio **debe leerlo en su totalidad antes de escribir una sola línea de código**.

---

## 0. Protocolo de Inicio (Obligatorio para cada sesión de agente)

Antes de cualquier acción, el agente debe ejecutar estos pasos en orden:

1. **Leer `RULES.md`** — reglas absolutas e inviolables del workspace.
2. **Leer este documento** (`AGENTS.md`) completo.
3. **Consultar `.agent/workflow_registry.md`** — seleccionar el proceso correcto para la tarea.
4. **Escanear la directiva del módulo solicitado** en `/directives/`.
5. **Leer el SKILL.md del skill correspondiente** en `.agent/skills/`.
6. **Inspeccionar código existente** en `admin/apps/<módulo>/` antes de crear archivos nuevos.
7. **Solo entonces**: proceder a implementar.

El agente que salte alguno de estos pasos está en violación del protocolo.

---

## 1. Alcance Operativo

**Gestion SB** es el panel de administración interno de negocio para **Stefy Barroso**. Es una SPA Mobile-First construida en PHP 8 + Tailwind CSS que gestiona Clientes, Ventas, Pedidos y Atención Premium (CRM).

**Criterios de aceptación de cualquier entrega:**
- [ ] Funciona correctamente en Chrome Mobile (viewport 390px)
- [ ] No hay estilos `inline` en el HTML (todo va a `styles/colores.css` o clases Tailwind)
- [ ] Todo endpoint AJAX retorna JSON estructurado: `{"status": "ok|error", "data": ..., "message": "..."}`
- [ ] Toda acción de write (INSERT/UPDATE/DELETE) llama a `log_event()`
- [ ] El agente ejecutó su Checklist antes de entregar

---

## 2. Stack Tecnológico

| Capa | Tecnología | Observaciones |
|---|---|---|
| Backend | PHP 8.1+ | PDO obligatorio, sin `mysqli_*` directo |
| Base de Datos | MySQL / MariaDB | Credenciales solo en `.env` |
| Frontend | Tailwind CSS (CDN) + Vanilla JS | Sin frameworks JS pesados (React, Vue) |
| Diálogos | SweetAlert2 | Único sistema de alertas permitido |
| Fuentes | Libre Baskerville (títulos) + Poppins (cuerpo) | Cargadas desde Google Fonts |
| Íconos | SVG inline o Heroicons | Prohibido usar emojis como íconos funcionales |
| Logging | `utils/logger.php → log_event()` | Obligatorio en toda modificación de datos |

---

## 3. Arquitectura de 4 Capas

El ecosistema opera sobre una topología de cuatro capas, definidas para garantizar determinismo, observabilidad y control total del código.

### Capa 1 · Directivas (Reglas de Negocio)
- Especificaciones inmutables alojadas en `/directives/`.
- Definen requerimientos funcionales, esquemas de BD, flujos de negocio y relaciones entre módulos.
- **El agente no puede contradecir o ignorar lo estipulado en una Directiva.**

### Capa 2 · Orquestación (Procesamiento Lógico)
- Coordinación y sincronización de servicios.
- Orden de prelación obligatorio:
  1. Verificar estado del código existente localmente.
  2. Implementar estrictamente conforme a la Directiva del módulo.
  3. Paralelizar procesos cuando sea posible (múltiples inserts en un solo transaction block).

### Capa 3 · Ejecución de Servicios
- Todo el código productivo vive en `execution/` o `admin/apps/<módulo>/`.
- Estándares no negociables:
  - Variables de entorno: exclusivamente vía `.env` + `vlucas/phpdotenv` o equivalente.
  - Logging: `utils/logger.php` en cada write.
  - Seguridad: `includes/security.php` en cada vista privada.

### Capa 4 · Observabilidad
- Dashboard de monitoreo: `dashboard/app.py`.
- Base de datos de auditoría: `logs/gestion_sb.db`.
- Toda transacción crítica (login, venta, edición de cliente) debe dejar rastro en la DB de logs.

---

## 4. Estándares Frontend (Mobile-First)

### 4.1 Arquitectura Visual — Sistema de Placas

El sistema de UI está basado en **Placas Maestras** y **Subplacas**.

| Concepto | Descripción | Clases Tailwind obligatorias |
|---|---|---|
| **Placa Maestra** | Contenedor principal de la vista entera | `bg-gray-50 min-h-screen p-4` |
| **Subplaca** | Tarjeta individual de cada entidad (cliente, venta, etc.) | `bg-white rounded-[1.5rem] shadow-sm p-4 mb-3` |
| **Subplaca ADN** | Variante con bordes más pronunciados | `bg-white rounded-[1.8rem] shadow-md p-5 mb-4` |

**Regla de Oro**: Está absolutamente prohibido mostrar datos sueltos o texto flotante en listados. Toda entidad (Cliente, Producto, Venta, Turno) vive dentro de su Subplaca.

### 4.2 Navegación SPA

- **Bottom Navbar**: Toda vista principal tiene un menú fijo en la parte inferior (`position: fixed; bottom: 0`).
- **Sin recargas de página**: La navegación entre módulos se hace inyectando contenido vía Fetch/AJAX en el `<main id="contenedor-principal">`.
- **Regla de la "X" (Cierre)**:
  - Vistas principalesy módulos integrados → El cierre va en el **Bottom Navbar** (nunca flotante arriba).
  - Modales / pop-ups secundarios → Botón "X" en la esquina superior derecha del overlay.

### 4.3 Hoja de Estilos Global

- Archivo maestro: `styles/colores.css`.
- Variables CSS en `:root` para todos los colores de marca.
- **Prohibido**: `style="..."` inline, clases CSS sueltas en `<style>` dentro del HTML.

### 4.4 Feedback al Usuario

- **SweetAlert2** para: éxito de guardado, confirmaciones de borrado, errores de validación.
- **Toasts** (SweetAlert2 `toast: true`) para: confirmaciones rápidas no bloqueantes.
- **Formato de error**: Si la operación falla en el backend, la respuesta JSON `{"status":"error","message":"..."}` debe mostrarse al usuario con `Swal.fire({icon: 'error', ...})`.

### 4.5 Localización

- **Idioma de UI**: Español exclusivamente.
- **Código fuente**: Variables y funciones en inglés (anglosajón normalizado).
- Ejemplo correcto: `function saveClient()` + `"Guardando cliente..."` en el texto visible.

---

## 5. Estándares Backend

### 5.1 Endpoints AJAX

Todo archivo de endpoint AJAX debe seguir este contrato de respuesta:

```php
<?php
// includes/security.php verifica sesión activa
require_once __DIR__ . '/../../includes/security.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // ... lógica ...
    echo json_encode(['status' => 'ok', 'data' => $resultado, 'message' => 'Operación exitosa']);
} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor']);
}
exit;
```

### 5.2 PDO — Uso Obligatorio

```php
// ✅ CORRECTO
$stmt = $pdo->prepare("SELECT * FROM Clientes WHERE Telefono = :tel");
$stmt->execute([':tel' => trim($telefono)]);

// ❌ PROHIBIDO
$result = mysqli_query($conn, "SELECT * FROM Clientes WHERE Telefono = '$telefono'");
```

### 5.3 Transacciones para Operaciones Compuestas

```php
$pdo->beginTransaction();
try {
    // múltiples writes atómicos
    $pdo->commit();
    log_event('INFO', 'Transacción completada', __FILE__);
} catch (Exception $e) {
    $pdo->rollBack();
    log_event('ERROR', $e->getMessage(), __FILE__);
    throw $e;
}
```

### 5.4 Naming Conventions

| Elemento | Convención | Ejemplo |
|---|---|---|
| Archivos AJAX | `ajax_<verbo>_<entidad>.php` | `ajax_save_client.php` |
| Archivos de vista | `<entidad>_<acción>.php` | `ver_clientes.php` |
| Tablas de BD | PascalCase | `Clientes`, `ItemsPedido` |
| PKs de BD | `Id<Entidad>` | `IdCliente`, `IdPedido` |
| Variables PHP | camelCase | `$idCliente`, `$nombreCompleto` |
| Funciones JS | camelCase | `loadClientCard()`, `submitForm()` |

---

## 6. Árbol Estructural

```
gestion_sb/
├── .agent/              # Sistema de orquestación de agentes
│   ├── skills/          # SKILLs especializados (uno por skill)
│   └── workflows/       # Pipelines de tareas repetibles
├── admin/               # Panel de administración (vistas y lógica)
│   ├── apps/            # Módulos de negocio
│   │   ├── clientes/    # Gestión de clientes
│   │   ├── ventas/      # Gestión de ventas
│   │   └── pedidos/     # Gestión de pedidos
│   └── login.php        # Punto de entrada de autenticación
├── dashboard/           # Observabilidad (Python/Flask)
├── directives/          # Especificaciones de negocio por módulo
├── execution/           # Scripts de servicio y automatización
├── includes/            # Lógica compartida (db.php, security.php)
├── logs/                # Persistencia de auditoría (SQLite)
├── styles/              # CSS global (colores.css)
├── utils/               # Módulos de soporte (logger.php)
└── .env                 # Credenciales (NUNCA en Git)
```

---

## 7. Mapa de Módulos y Skills (Regla 1 Directiva : 3 Skills)

| Módulo | Directiva | Skills Asociados | Ruta principal |
|---|---|---|---|
| **Login** | `build_login.md` | `login_manager`, `auth_authenticator`, `session_guard` | `admin/login.php` |
| **Dashboard** | `build_system.md` | `menu_navigator`, `app_orchestrator`, `dashboard_layout` | `admin/index.php` |
| **Clientes** | `build_clientes.md` | `client_manager`, `premium_attention`, `client_profiler` | `admin/apps/clientes/ver_clientes.php` |
| **Atención** | `build_atencion.md` | `premium_attention`, `client_profiler`, `client_manager` | `admin/apps/clientes/atencion_cliente.php` |
| **Ventas** | `build_ventas.md` | `sales_manager`, `catalog_manager`, `billing_helper` | `admin/apps/ventas/ver_ventas.php` |
| **Pedidos** | `build_pedidos.md` | `sales_manager`, `catalog_manager`, `billing_helper` | `admin/apps/pedidos/ver_pedidos.php` |

---

## 8. Protocolo de Manejo de Errores

Ante cualquier falla técnica, el flujo obligatorio es:

1. **Capturar**: `catch (Exception $e)` — nunca silenciar errores.
2. **Registrar**: `log_event('ERROR', $e->getMessage(), __FILE__)`.
3. **Responder**: Retornar JSON de error estructurado con código HTTP apropiado.
4. **Revertir**: Si había una transacción activa, ejecutar `$pdo->rollBack()`.
5. **Notificar al usuario**: SweetAlert2 con ícono de error y mensaje legible en español.

```
Error PHP → catch → log_event() → rollBack() → JSON error → Swal.fire(error)
```

---

## 9. Prácticas de Ingeniería Central

| Práctica | Descripción |
|---|---|
| **Reutilización Preventiva** | Leer código existente en `execution/` y `admin/apps/` ANTES de crear cualquier archivo nuevo |
| **Telemetría Obligatoria** | `log_event()` en todo INSERT/UPDATE/DELETE. Deployment silencioso = subestándar |
| **Zero Inline Styles** | Ningún `style="..."` en HTML. Todo a `colores.css` o clases Tailwind |
| **PDO Siempre** | Sentencias preparadas en absolutamente todos los queries |
| **JSON Contracts** | Todos los endpoints AJAX retornan `{status, data, message}` |
| **Español en UI** | Toda cadena visible por el usuario final está en español |
| **SVG sobre Emoji** | Los íconos funcionales (botones, estados) son SVG, nunca emojis |

---

*Última revisión: Abril 2026 · Gestion SB v2.0*
