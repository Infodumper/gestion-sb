---
name: dashboard_layout
description: Definición de contenedores maestros, grillas, Design System de tokens CSS y el sistema visual de "Placas" para el escritorio central.
directiva: directives/build_system.md
---

# Skill: dashboard_layout

## 1. Rol y Responsabilidad

Eres el agente **dashboard_layout**. Eres el guardián visual del sistema. Tu mandato es garantizar que cada pantalla cumpla el Design System corporativo: colores, tipografía, espaciados y el sistema de Placas. Nada de estilos inline y nada de clases Tailwind ad-hoc sueltas.

Leer directiva antes de ejecutar: `directives/build_system.md`.

---

## 2. Design System — Variables CSS Globales (`styles/colores.css`)

Este es el archivo maestro. **Todo color, tipografía y espaciado debe provenir de aquí**.

```css
/* ============================================================
   Gestion SB — Design System Global
   Archivo: styles/colores.css
   NOTA: No modificar sin revisar el impacto en todos los módulos
   ============================================================ */

/* ── Google Fonts ── */
@import url('https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap');

/* ── Reset básico ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Tokens de Color ── */
:root {
  /* Primarios (Marca Stefy Barroso) */
  --color-primary:        #7c3aed;  /* Violeta principal */
  --color-primary-light:  #ede9fe;  /* Violeta suave (fondos) */
  --color-primary-dark:   #5b21b6;  /* Violeta oscuro (hover) */

  /* Secundarios */
  --color-secondary:      #f59e0b;  /* Ámbar acento */
  --color-secondary-light: #fef3c7;

  /* Estados */
  --color-success:        #10b981;  /* Éxito / WhatsApp */
  --color-warning:        #f59e0b;  /* Advertencia */
  --color-error:          #ef4444;  /* Error */
  --color-info:           #3b82f6;  /* Información */

  /* Neutros */
  --color-bg:             #f9fafb;  /* Fondo general (gray-50) */
  --color-surface:        #ffffff;  /* Fondo de Subplacas */
  --color-border:         #e5e7eb;  /* Bordes suaves (gray-200) */
  --color-muted:          #9ca3af;  /* Texto secundario (gray-400) */
  --color-text:           #1f2937;  /* Texto principal (gray-800) */
  --color-text-light:     #6b7280;  /* Texto de apoyo (gray-500) */

  /* Tipografía */
  --font-brand:  'Libre Baskerville', Georgia, serif;
  --font-body:   'Poppins', system-ui, sans-serif;

  /* Radios */
  --radius-sm:   0.75rem;   /* 12px — chips, badges */
  --radius-md:   1.5rem;    /* 24px — Subplacas estándar */
  --radius-lg:   1.8rem;    /* 28.8px — Subplacas ADN */
  --radius-xl:   2rem;      /* 32px — Modales */

  /* Sombras */
  --shadow-sm:   0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
  --shadow-md:   0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.05);
  --shadow-lg:   0 10px 30px rgba(0,0,0,0.12);

  /* Espaciados de contenedor */
  --container-padding: 1rem;        /* Padding interno de la placa */
  --gap-subplacas:     0.75rem;     /* Separación entre Subplacas */
}

/* ── Modo Oscuro (sin requerir JS) ── */
@media (prefers-color-scheme: dark) {
  :root {
    --color-bg:       #111827;
    --color-surface:  #1f2937;
    --color-border:   #374151;
    --color-muted:    #6b7280;
    --color-text:     #f9fafb;
    --color-text-light: #d1d5db;
  }
}

/* ── Reset tipografía global ── */
body {
  font-family: var(--font-body);
  background-color: var(--color-bg);
  color: var(--color-text);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

h1, h2, h3 { font-family: var(--font-brand); }

/* ── Sistema de Placas ── */
.placa-maestra {
  background: var(--color-bg);
  min-height: 100vh;
  padding: var(--container-padding);
  padding-bottom: 6rem; /* Espacio para Bottom Navbar */
}

.subplaca {
  background: var(--color-surface);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  padding: 1rem;
  margin-bottom: var(--gap-subplacas);
  transition: box-shadow 0.2s ease;
}

.subplaca:hover {
  box-shadow: var(--shadow-md);
}

.subplaca-adn {
  background: var(--color-surface);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  padding: 1.25rem;
  margin-bottom: var(--gap-subplacas);
}

/* ── Botones ── */
.btn-primary {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--radius-sm);
  padding: 0.625rem 1.25rem;
  font-family: var(--font-body);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.1s ease;
  min-height: 44px;
}

.btn-primary:hover  { background: var(--color-primary-dark); }
.btn-primary:active { transform: scale(0.97); }

.btn-icon {
  width: 2.25rem;
  height: 2.25rem;
  border-radius: 9999px;
  border: none;
  background: var(--color-primary-light);
  color: var(--color-primary);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-icon:hover { background: #ddd6fe; }

/* ── Bottom Navbar (ver skill: menu_navigator) ── */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 4rem;
  background: var(--color-surface);
  border-top: 1px solid var(--color-border);
  box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
  z-index: 50;
}

/* ── Badges de Estado ── */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.125rem 0.625rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-pendiente  { background: #fef3c7; color: #d97706; }
.badge-pagado     { background: #d1fae5; color: #059669; }
.badge-entregado  { background: #dbeafe; color: #2563eb; }
.badge-cancelado  { background: #fee2e2; color: #dc2626; }
.badge-activo     { background: #d1fae5; color: #059669; }
.badge-inactivo   { background: #f3f4f6; color: #6b7280; }

/* ── Utilidades ── */
.font-brand { font-family: var(--font-brand); }
.text-primary { color: var(--color-primary); }
.bg-primary  { background-color: var(--color-primary); }
.truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
```

### B. Estructura de Dashboard HTML

```html
<!-- Dashboard principal — estructura base -->
<div class="placa-maestra max-w-lg mx-auto">

  <!-- Header de módulo -->
  <header class="flex items-center justify-between mb-6 pt-2">
    <div>
      <h1 class="text-2xl font-brand text-gray-800">Inicio</h1>
      <p class="text-sm text-gray-500">Buenos días, Stefy 👋</p>
    </div>
    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
      SB
    </div>
  </header>

  <!-- Placas de métricas (grilla 2 columnas) -->
  <div class="grid grid-cols-2 gap-3 mb-6">

    <div class="subplaca text-center">
      <p class="text-3xl font-bold text-primary" id="metric-clientes">—</p>
      <p class="text-xs text-gray-500 mt-1">Clientes activos</p>
    </div>

    <div class="subplaca text-center">
      <p class="text-3xl font-bold text-emerald-600" id="metric-ventas">—</p>
      <p class="text-xs text-gray-500 mt-1">Ventas este mes</p>
    </div>

    <div class="subplaca text-center">
      <p class="text-3xl font-bold text-amber-600" id="metric-pedidos">—</p>
      <p class="text-xs text-gray-500 mt-1">Pedidos pendientes</p>
    </div>

    <div class="subplaca text-center">
      <p class="text-3xl font-bold text-violet-600" id="metric-cumples">—</p>
      <p class="text-xs text-gray-500 mt-1">Cumples este mes</p>
    </div>

  </div>

  <!-- Accesos rápidos -->
  <h2 class="text-base font-semibold text-gray-700 mb-3">Acceso rápido</h2>
  <div class="space-y-2">
    <button onclick="navegarA('clientes')" class="subplaca w-full flex items-center gap-3 text-left hover:shadow-md cursor-pointer transition-shadow">
      <div class="w-10 h-10 rounded-xl bg-primary-light flex items-center justify-center">
        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      <div>
        <p class="font-medium text-gray-800 text-sm">Clientes</p>
        <p class="text-xs text-gray-500">Gestionar base de clientes</p>
      </div>
    </button>
  </div>

</div>
```

---

## 3. Reglas del Design System

| Regla | Detalle |
|---|---|
| **Sin inline styles** | Todo color, espaciado y tamaño proviene de `colores.css` o clases Tailwind |
| **Subplaca mínima** | `background: surface`, `border-radius: var(--radius-md)`, `box-shadow: var(--shadow-sm)` |
| **Tipografía de títulos** | `font-family: var(--font-brand)` — Libre Baskerville |
| **Tipografía de cuerpo** | `font-family: var(--font-body)` — Poppins |
| **Área táctil mínima** | Todo botón interactivo tiene `min-height: 44px` |
| **Modo oscuro** | El sistema soporta `prefers-color-scheme: dark` vía variables CSS |

---

## 4. Checklist Antes de Entregar

- [ ] ¿Todos los colores usan variables CSS de `colores.css` (no valores hex literales en el HTML)?
- [ ] ¿Las Subplacas usan las clases `.subplaca` o `.subplaca-adn` (no clases Tailwind ad-hoc)?
- [ ] ¿Los títulos usan `font-family: var(--font-brand)` (Libre Baskerville)?
- [ ] ¿Los botones tienen `min-height: 44px` para área táctil correcta?
- [ ] ¿El dashboad es responsive a 390px de ancho?
- [ ] ¿El modo oscuro funciona con `prefers-color-scheme: dark`?
- [ ] ¿Los badges de estado usan las clases `.badge-*` definidas en `colores.css`?
