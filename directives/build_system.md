## Directiva: Arquitectura del Sistema (Gestion SB)
> **Skills Asociados:** `menu_navigator` · `app_orchestrator` · `dashboard_layout`
> **Versión:** 2.0 · **Estado:** Producción

---

## Capa 1: Objetivo y Alcance

**Misión:**
Establecer los estándares de arquitectura de información, navegación, design system y convenciones técnicas que unifican la experiencia visual y de desarrollo de todo el sistema.

Esta directiva es la **raíz** del árbol de directivas. Todos los demás módulos la heredan.

---

## Capa 2: Orquestación (Patrones de Diseño)

### 1. Arquitectura SPA Mobile-First (OBLIGATORIO)

| Principio | Implementación |
|---|---|
| **Sin recargas de página** | La navegación entre módulos usa `fetch()` hacia rutas PHP. Resultado inyectado en `<main id="contenedor-principal">` |
| **Mobile-First** | Diseño para viewport 390px. Escalar hacia arriba para tablet/desktop |
| **Bottom Navbar** | Menú fijo inferior (`position: fixed; bottom: 0`) con íconos SVG. Mín. 4 ítems |
| **Módulos como iframes/partials** | Cada módulo es un archivo PHP que puede cargarse standalone o inyectado como partial |

### 2. Sistema de Placas

| Componente | Clases / Variables | Uso |
|---|---|---|
| `Placa Maestra` | `.placa-maestra` | Contenedor de toda la vista |
| `Subplaca` | `.subplaca` + `rounded-[1.5rem]` | Cada entidad en un listado |
| `Subplaca ADN` | `.subplaca-adn` + `rounded-[1.8rem]` | Variante con más énfasis visual |
| `Modal Overlay` | `fixed inset-0 bg-black/40` | Ventanas flotantes secundarias |

### 3. Regla de la "X" (Cierre)

```
Vista principal (módulo inyectado)  →  Sin X visible. El Back va en el Bottom Navbar
Modal / Pop-up secondary            →  X en esquina superior derecha del contenedor del modal
Drawer / Bottom-sheet               →  Swipe abajo o botón en el header del drawer
```

---

## Capa 3: Estándares Técnicos

### Stack Tecnológico

| Tecnología | Uso |
|---|---|
| PHP 8.1+ | Backend, renderizado de HTML inicial |
| PDO | Única forma de interactuar con la BD |
| MySQL/MariaDB | Base de datos relacional |
| Tailwind CSS (CDN) | Clases utilitarias de UI |
| `styles/colores.css` | Design System global (colores, tipografías, radios, sombras) |
| Vanilla JS | Lógica de UI, fetch, DOM |
| SweetAlert2 | Sistema único de diálogos y toasts |
| Libre Baskerville | Tipografía de marca y títulos |
| Poppins | Tipografía de cuerpo |

### Estructura de Carpetas

```
admin/
├── login.php                   # Punto de entrada
├── index.php                   # Shell SPA central
└── apps/
    ├── clientes/
    │   ├── ver_clientes.php
    │   ├── atencion_cliente.php
    │   ├── partials/            # Modales, fragmentos reutilizables
    │   └── ajax_*.php           # Endpoints AJAX
    ├── ventas/
    │   └── ...
    └── pedidos/
        └── ...
includes/
├── db.php                      # Conexión PDO (lee .env)
└── security.php                # Middleware de sesión
styles/
└── colores.css                 # Design System global
utils/
└── logger.php                  # Función log_event()
```

### Contratos de Interfaz AJAX

Todo endpoint AJAX retorna:
```json
{
  "status":  "ok | error",
  "data":    { ... },
  "message": "Texto legible en español"
}
```

Código HTTP: `200` para OK, `400` para validación, `401` para no autenticado, `500` para error interno.

---

## Capa 4: Observabilidad

### Logging

```php
// utils/logger.php — función estandarizada
function log_event(string $nivel, string $mensaje, string $origen = ''): void {
    // Persiste en logs/gestion_sb.db (SQLite)
    // Niveles: INFO, UPDATE, INSERT, DELETE, AUTH_OK, AUTH_FAIL, ERROR
}
```

**Obligatorio llamar a `log_event()` en:**
- Todo INSERT/UPDATE/DELETE en BD
- Login exitoso (`AUTH_OK`) y fallido (`AUTH_FAIL`)
- Errores de excepción (`ERROR`)

### Dashboard de Monitoreo
- `dashboard/app.py`: Interfaz Python/Flask con métricas del sistema.
- Acceso a `logs/gestion_sb.db` para visualizar actividad.

---

## Capa 5: Design System (tokens)

Ver archivo maestro: `styles/colores.css`
Ver skill: `dashboard_layout`

### Paleta de Colores Principal

| Token | Valor | Uso |
|---|---|---|
| `--color-primary` | `#7c3aed` | Marca, botones principales, nav activo |
| `--color-secondary` | `#f59e0b` | Acentos, destacados |
| `--color-success` | `#10b981` | WhatsApp, estados OK |
| `--color-error` | `#ef4444` | Errores, bajas |
| `--color-bg` | `#f9fafb` | Fondo general |
| `--color-surface` | `#ffffff` | Subplacas |

### Tipografía

| Tipografía | Variable | Uso obligatorio |
|---|---|---|
| Libre Baskerville | `--font-brand` | `h1`, `h2`, nombres de módulo |
| Poppins | `--font-body` | Todo el cuerpo de texto |