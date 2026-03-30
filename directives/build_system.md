# Directiva: Arquitectura del Sistema (Gestion SB)
> **Skills Asociados:** `menu_navigator`, `app_orchestrator`, `dashboard_layout`

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**

Establecer los estándares de arquitectura, diseño y navegación para el sistema de gestión de Stefy Barroso. El sistema debe ser altamente eficiente para uso móvil y con una estética premium coherente.

## Capa 2: Orquestación (Patrones de Diseño)

### 1. Navegación "App-Like" (NORMA OBLIGATORIA ESTRUCTURA)

* **SPA y Mobile-First**: Mantener una estructura SPA simple, sin frameworks pesados, priorizando el uso y visualización desde celulares.
* **Arquitectura de Placas Independientes de Información**: Contenedores de tipo "Placa Maestra" y "Subplacas" apiladas. Todo el CSS debe estar globalizado; nada de estilos flotantes.
* **Menú y Navigations**: Bottom nav bars o menú principal táctil, controlando vistas desde JS sin refrescar si no es necesario.
* **Pop-ups / Iframes / Vistas Dinámicas**: Mantener el contexto persistente cargando la info en el Dashboard central.
* **Regla de la "X" (Cierre)**: Únicamente un botón de cierre. En vistas principales se ubica en la parte inferior. Solo pop-ups/modales llevan el botón en el encabezado superior derecho.
* **Regla de las Subplacas**: Todo listado de entidades (personas, productos, registros) debe implementarse mediante tarjetas individuales blancas (`bg-white`), con bordes redondeados (`rounded-[1.5rem]`), sombra suave (`shadow-sm`) y separadas entre sí. Prohibido mostrar datos sueltos o texto flotante en listados.
### 2. Estrechamiento de Diseño (Design System)

* **Centralización**: Todo el estilo visual vive en `/styles/main.css`.
* **Variables CSS**: Uso obligatorio de `:root` para colores de marca (Púrpura Noa Mora, Naranja Stefy).
* **Tipografía**:
* `Libre Baskerville`: Títulos de marca y énfasis.
* `Poppins`: Cuerpo de texto, formularios y datos.
## Capa 3: Ejecución (Estándares Técnicos)

### Estructura de Carpetas

* `admin/`: Panel de gestión.
* `admin/apps/`: Módulos de negocio (clientes, ventas, stock).
* `admin/apps/[modulo]/partials/`: Fragmentos de código (modales, tablas) reutilizables.
* `includes/`: Lógica compartida (conexión DB, carga de .env).
* `styles/`: Archivos CSS globales.
### Tecnologías Obligatorias

* **Backend**: PHP 8+ (PDO para base de datos).
* **Frontend**: Tailwind CSS (via CDN para prototipado rápido) + Vanilla JS.
* **Interacción**: SweetAlert2 para todos los diálogos de confirmación y error.
## Capa 4: Observabilidad

* **Logging**: Integrado en cada acción de cambio (INSERT/UPDATE/DELETE).
* **Feedback**: El usuario siempre debe recibir una confirmación visual clara tras cualquier operación.