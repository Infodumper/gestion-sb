# Skills Registry

Este archivo define las capacidades disponibles para los agentes dentro del proyecto. Los agentes deben consultar este registro antes de ejecutar cualquier acción.

---

# Skill: create_module
**Descripción**: Crear un nuevo módulo dentro del panel administrativo.  
**Cuándo usar**: Cuando se solicite una nueva funcionalidad del sistema.  
**Entrada**: nombre del módulo.  
**Salida**: carpeta en `admin/apps/<modulo>/`.  
**Directiva asociada**: `directives/create_module.md`

---

# Skill: manage_clients
**Descripción**: Gestión integral de la base de clientes.  
**Funciones**:
- Listado premium con búsqueda y ordenación.
- Alta y Edición mediante modales/partials.
- Visualización de "Ficha de Cliente" con historial de ventas.
- Importación de datos.  
**Directiva asociada**: `directives/build_clientes.md`  
**Ubicación**: `admin/apps/clientes/`

---

# Skill: manage_atencion
**Descripción**: Fidelización y atención premium al cliente.  
**Funciones**:
- Seguimiento de cumpleaños del mes.
- Identificación de clientes frecuentes (VIP).
- Gestión de listas de difusión móviles.
- Integración directa con WhatsApp.  
**Directiva asociada**: `directives/build_atencion.md`  
**Ubicación**: `admin/apps/clientes/atencion_cliente.php`

---

# Skill: manage_sales
**Descripción**: Gestión del ciclo de venta y pedidos.  
**Funciones**:
- Toma de pedidos dinámica.
- Historial de ventas integrado.
- Sincronización con stock.  
**Directiva asociada**: `directives/build_ventas.md`  
**Ubicación**: `admin/apps/ventas/`

---

# Skill: manage_stock
**Descripción**: Gestión de inventario de productos.  
**Cuándo usar**: Consultar stock, actualizar productos, importar productos.  
**Directiva asociada**: `directives/build_stock.md`  
**Ubicación**: `admin/apps/stock/`

---

# Skill: run_sql_query
**Descripción**: Ejecutar consultas SQL para diagnóstico.  
**Advertencia**: No ejecutar operaciones destructivas sin aprobación humana.  
**Directiva asociada**: `directives/build_sql_tools.md`  
**Ubicación**: `admin/apps/sql/`

---

# Skill: system_maintenance
**Descripción**: Tareas de mantenimiento (backups, diagnóstico, limpieza).  
**Directiva asociada**: `directives/build_system_tools.md`  
**Ubicación**: raíz del sistema