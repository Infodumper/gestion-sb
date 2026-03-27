# Arquitectura y Estándares de Sistema – "Consultora"

> Este documento contiene los protocolos técnicos y de arquitectura. Aplica a todo entorno de orquestación de agentes y a los pipelines de desarrollo automatizado (CLAUDE, GEMINI, etc).

## 1. Alcance Operativo

El entorno "Consultora" opera bajo un modelo de ingeniería automatizada para la construcción de plataformas digitales. Los productos resultantes deben cumplir estrictamente con los estándares de nivel de producción, garantizando alta disponibilidad, mantenibilidad arquitectónica, rendimiento óptimo en despliegue y adherencia a los lineamientos UI/UX corporativos.

## 2. Arquitectura de 4 Capas

El ecosistema opera sobre una topología de cuatro capas, definidas para garantizar el determinismo, la observabilidad y el control absoluto del código:

### Capa 1: Gestión de Directivas (Reglas de Negocio)
- Especificaciones Técnicas y Procedimientos Estándar de Operación (SOP) alojados en `/directives/`.
- Definen requerimientos, integraciones, parámetros de entrada/salida y dependencias lógicas.
- Inmutables durante la ejecución, rigen el comportamiento técnico del producto.

### Capa 2: Orquestación (Procesamiento Lógico)
- Módulo encargado de la coordinación y sincronización de servicios.
- Reglas de validación y prelación:
  1. Análisis de estado y flujos de trabajo preexistentes localmente.
  2. Implementación basada estrictamente en requerimientos de Directiva.
  3. Ejecución asíncrona de procesos paralelos.

### Capa 3: Ejecución de Servicios
- Segmento operativo del código desplegado (e.g., `execution/`).
- Obligatoriedad estandarizada:
  - Todo script, proceso automático y módulo interactivo de servidor debe incorporar manejo centralizado de configuraciones secretas (`.env`).
  - Implementación imperativa del módulo `utils/logger.py` mediante telemetría detallada.

### Capa 4: Observabilidad de la Plataforma
- Visibilidad del backend mediante un nodo de monitoreo dedicado (`dashboard/app.py`).
- Audita rendimiento, transacciones, latencias de despliegue y el estado de bases de datos locales (`logs/consultora.db`).

## 3. Estándares Técnicos Frontend (Infraestructura Móvil)

Todo módulo del ecosistema debe converger obligatoriamente hacia las normativas estandarizadas de frontend y acceso remoto:

### 3.1. Estructura y Rendering
- **Arquitectura SPA (Single Page Application)**: Dominio sobre los tiempos de carga en dispositivos móviles restringiendo imperativamente el uso indiscriminado de frameworks. Navegación enrutada asíncronamente.
- **Topología UI "ADN Chapitas"**: Modelo de interfaz estandarizado con distribución en contenedores maestros ("chapas") y listados funcionales ("chapitas" o subplacas).
- **Regla de las Subplacas**: Está terminantemente prohibido mostrar datos sueltos o texto flotante en los listados. Cada entidad (Cliente, Producto, Venta, etc.) debe habitar dentro de su propia "subplaca": una tarjeta blanca (`bg-white`), con bordes redondeados (`rounded-[1.5rem]`), sombra suave (`shadow-sm`) y efectos de hover suaves.
- **Hojas de Estilo Globales**: Centralización estricta (`colores.css`). Está prohibido explícitamente el uso de "inline styles", reglas flotantes sueltas y declaración de componentes con clases CSS redundantes no globalizadas.
- **UX en Móviles**: Implementación reglamentaria de paneles de navegación inferior (Bottom Navbars), áreas táctiles optimizadas y soporte integral Claro/Oscuro. Empleo prioritario de **Tailwind CSS**.
- **Regla de la "X" (Cierre)**: Se establece el estándar de un solo botón de cierre visible. En vistas principales y módulos integrados, la "X" debe ubicarse únicamente en la zona inferior (Bottom Navbar o acción persistente). Solo las ventanas secundarias de tipo **pop-up o modales** deben incorporar el botón "X" en la cinta del título (encabezado superior derecho del contenedor).

### 3.2. Localización y Refactorización
- Idioma de despliegue, documentación de la UI y variables de salida debe encontrarse puramente en idioma español. Nomenclatura base de sistemas anglosajona admitida únicamente en la sintaxis profunda del código.
- Los prototipos técnicos aislados deben ensamblarse y validarse bajo el flujo de código como sistemas productivos limpios y listos para escalar operativamente.

## 4. Prácticas de Ingeniería Central

1. **Reutilización Preventiva**: Obligación de escanear y estudiar infraestructura previa (`/execution/`, `/directives/`) previo a cualquier scaffolding de código.
2. **Telemetría Mandatoria**: El despliegue de módulos al entorno debe contener validaciones de traza de errores detalladas. El despliegue silencioso (un-logged) se considera como subestándar.
3. **Mecanismos de Resiliencia Automática**: Frente a cualquier interrupción técnica, el flujo de fallos requiere la extracción de stack trace, parche en caliente de la base de código y relanzamiento automático del servicio.

## 5. Árbol Estructural Crítico

- `.tmp/`: Procesos e inyecciones dinámicas provisionales.
- `execution/`: Alojamiento de servicios de lógica primaria.
- `directives/`: Especificaciones del núcleo arquitectural y normativas.
- `logs/`: Persistencia de métricas y volcado de auditoría en Base de Datos.
- `dashboard/`: Entorno visual para telemetría.
- `utils/`: Módulos de soporte subyacente de uso constante.
- `.agent/workflows/`: Pipelines continuos o rutinas paramétricas de desarrollo continuo.

## 6. Módulos y Skills del Ecosistema (Regla 1:3)

Cada módulo de aplicación se rige por **una Directiva** propia y se apalanca en **tres Skills** especializadas para su ejecución:

### Módulo: Login (Directiva: build_login.md)
- **login_manager**: Orquestador visual del formulario y acceso.
- **auth_authenticator**: Lógica de verificación de credenciales y seguridad PHP.
- **session_guard**: Middleware de persistencia y protección de rutas.

### Módulo: Main / Dashboard (Directiva: build_system.md)
- **menu_navigator**: Controlador de estado SPA y ruteo dinámico.
- **app_orchestrator**: Gestión de ciclo de vida de iframes y modales web.
- **dashboard_layout**: Definición de contenedores (Chapitas) y grilla principal.

### Módulo: Clientes (Directiva: build_clientes.md)
- **client_manager**: Gestión transaccional (CRUD) y persistencia de datos.
- **premium_attention**: Automatización de fidelización y difusión vía WhatsApp.
- **client_validator**: Validación de identidades duplicadas y limpieza de datos (DNI/Tel).

### Módulo: Ventas / Pedidos (Directivas: build_ventas.md / build_pedidos.md)
- **sales_manager**: Gestión de procesos de venta y carritos.
- **catalog_manager**: Administración de productos y stock.
- **billing_helper**: Lógica de cobros, parciales y totales.

---

```json
{
  "mcpServers": {
    "context7": {
      "command": "npx",
      "args": [
        "-y",
        "@upstash/context7-mcp",
        "--api-key",
        "TU API KEY"
      ],
      "env": {}
    }
  }
}
```
