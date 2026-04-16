---
name: menu_navigator
description: Controlador de estado SPA, ruteo dinámico y manejo de contenedores de módulos (Bottom Navbar).
directiva: directives/build_system.md
---

# Skill: menu_navigator

## 1. Rol y Responsabilidad

Eres el agente **menu_navigator**. Gobiernan tu comportamiento tres principios:

1. **Sin recargas de página**: Toda navegación se hace inyectando contenido en el `<main>` central.
2. **Bottom Navbar obligatorio**: Toda aplicación tiene menú fijo en la parte inferior de la pantalla.
3. **Una sola "X" visible**: Jamás aparece más de un botón de cierre al mismo tiempo.

Leer directiva antes de ejecutar: `directives/build_system.md`.

---

## 2. Patrones de Código Obligatorios

### A. Estructura HTML Base del SPA

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Gestion SB</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/styles/colores.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 font-poppins antialiased">

  <!-- Zona de contenido principal — aquí se inyectan los módulos -->
  <main id="contenedor-principal" class="min-h-screen pb-24 transition-opacity duration-200">
    <!-- Contenido inicial (Dashboard) -->
  </main>

  <!-- Bottom Navigation Bar (fijo, siempre visible) -->
  <nav id="bottom-nav" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 shadow-lg z-50">
    <div class="flex items-center justify-around h-16 max-w-lg mx-auto">

      <button onclick="navegarA('dashboard')" id="nav-dashboard" class="nav-item active" aria-label="Inicio">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="text-xs mt-1">Inicio</span>
      </button>

      <button onclick="navegarA('clientes')" id="nav-clientes" class="nav-item" aria-label="Clientes">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="text-xs mt-1">Clientes</span>
      </button>

      <button onclick="navegarA('ventas')" id="nav-ventas" class="nav-item" aria-label="Ventas">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="text-xs mt-1">Ventas</span>
      </button>

      <button onclick="navegarA('atencion')" id="nav-atencion" class="nav-item" aria-label="Atención">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span class="text-xs mt-1">Atención</span>
      </button>

    </div>
  </nav>

</body>
</html>
```

### B. Router SPA en JavaScript

```javascript
// ============================================================
// Router SPA — menu_navigator
// Carga contenido dinámicamente sin recargar la página
// ============================================================

const RUTAS = {
  dashboard: '/admin/apps/dashboard/index.php',
  clientes:  '/admin/apps/clientes/ver_clientes.php',
  ventas:    '/admin/apps/ventas/ver_ventas.php',
  pedidos:   '/admin/apps/pedidos/ver_pedidos.php',
  atencion:  '/admin/apps/clientes/atencion_cliente.php',
};

let moduloActual = null;

async function navegarA(modulo) {
  if (modulo === moduloActual) return; // Evita recarga innecesaria

  const contenedor = document.getElementById('contenedor-principal');
  const url = RUTAS[modulo];

  if (!url) {
    console.warn(`Ruta desconocida: ${modulo}`);
    return;
  }

  // Feedback visual de carga
  contenedor.style.opacity = '0.4';

  try {
    const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const html = await res.text();

    contenedor.innerHTML = html;
    contenedor.style.opacity = '1';
    moduloActual = modulo;

    // Ejecutar scripts del módulo inyectado
    contenedor.querySelectorAll('script').forEach(viejo => {
      const nuevo = document.createElement('script');
      nuevo.textContent = viejo.textContent;
      viejo.replaceWith(nuevo);
    });

    actualizarNavActivo(modulo);

  } catch (err) {
    contenedor.style.opacity = '1';
    contenedor.innerHTML = `
      <div class="flex flex-col items-center justify-center py-16 text-gray-400">
        <svg class="w-12 h-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm">Error al cargar el módulo</p>
        <button onclick="navegarA('${modulo}')" class="mt-3 text-xs text-primary underline">Reintentar</button>
      </div>
    `;
    console.error('Error al navegar:', err);
  }
}

function actualizarNavActivo(modulo) {
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  const navActivo = document.getElementById(`nav-${modulo}`);
  if (navActivo) navActivo.classList.add('active');
}
```

### C. CSS para el Bottom Navbar (`colores.css`)

```css
/* Bottom Navbar — estados de ítem */
.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  padding: 0.5rem;
  color: var(--color-muted, #9ca3af);
  border: none;
  background: none;
  cursor: pointer;
  transition: color 0.2s ease;
  min-height: 44px; /* Área táctil mínima */
}

.nav-item:active {
  transform: scale(0.95);
}

.nav-item.active {
  color: var(--color-primary, #7c3aed);
}

.nav-item svg {
  transition: transform 0.2s ease;
}

.nav-item.active svg {
  transform: scale(1.1);
}
```

### D. Apertura y Cierre de Módulos Secundarios

```javascript
// Módulo secundario — la "X" va en la CABECERA si es un overlay/modal
function abrirModal(titulo, urlContenido) {
  const overlay = document.createElement('div');
  overlay.id = 'overlay-modal';
  overlay.className = 'fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] flex items-end sm:items-center justify-center p-4';
  overlay.innerHTML = `
    <div class="bg-white rounded-t-[2rem] sm:rounded-[2rem] w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
      <!-- Header del modal con X en la derecha -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 sticky top-0 bg-white rounded-t-[2rem]">
        <h2 class="text-lg font-bold text-gray-800">${titulo}</h2>
        <button onclick="cerrarModal()" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors" aria-label="Cerrar">
          <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <div id="modal-body" class="p-5"><!-- Contenido cargado --></div>
    </div>
  `;
  document.body.appendChild(overlay);
  // Cargar contenido del modal vía fetch si se proporciona URL
  if (urlContenido) {
    fetch(urlContenido).then(r => r.text()).then(html => {
      document.getElementById('modal-body').innerHTML = html;
    });
  }
}

function cerrarModal() {
  document.getElementById('overlay-modal')?.remove();
}
```

---

## 3. Reglas de Enrutamiento

| Módulo | Ruta | Método de carga |
|---|---|---|
| `dashboard` | `/admin/apps/dashboard/index.php` | AJAX fetch |
| `clientes` | `/admin/apps/clientes/ver_clientes.php` | AJAX fetch |
| `ventas` | `/admin/apps/ventas/ver_ventas.php` | AJAX fetch |
| `atencion` | `/admin/apps/clientes/atencion_cliente.php` | AJAX fetch |
| Modal/Overlay | URL dinámica | fetch → `#modal-body` |

---

## 4. Checklist Antes de Entregar

- [ ] ¿La navegación usa `fetch()` y nunca `window.location.href` para cambiar módulos?
- [ ] ¿El Bottom Navbar tiene `position: fixed; bottom: 0` y `z-index` correcto?
- [ ] ¿El ítem activo del navbar se actualiza visualmente con la clase `active`?
- [ ] ¿Los modales secundarios tienen la "X" en el header superior derecho (no en el Bottom Nav)?
- [ ] ¿Las vistas principales cierran desde el Bottom Navbar (no tienen X flotante en el header)?
- [ ] ¿El contenedor principal tiene `transition-opacity` para feedback de carga suave?
- [ ] ¿Los nav items tienen área táctil mínima de 44px (`min-height: 44px`)?
