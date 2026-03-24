# Module Registry

Este archivo define los módulos funcionales existentes en el sistema.

Antes de crear un nuevo módulo, el agente debe consultar este registro
para evitar duplicaciones.

---

# Módulo: servicios

Ubicación

admin/apps/servicios/

Descripción

Gestión centralizada de servicios, notas de trabajo y atención al cliente.

Funciones principales

- crear y listar notas de trabajo
- atención premium (cumpleaños, fidelización)
- agenda profesional

Tablas asociadas

notatrabajo  
detallesnotatrabajo  
servicios  

---

# Módulo: stock

Ubicación

admin/apps/stock/

Descripción

Gestión de inventario de productos.

Funciones principales

- visualizar productos
- actualizar stock
- importar productos

---

# Módulo: pedidos

Ubicación

admin/apps/pedidos/

Descripción

Gestión de pedidos realizados por clientes.

Funciones principales

- crear pedido
- listar pedidos

---

# Módulo: clientes

Ubicación

admin/apps/clientes/

Descripción

Gestiona la base de clientes del sistema. (Módulo ahora en última posición del dashboard).

Funciones principales

- listar y editar clientes
- importar clientes
- visualizar ficha de cliente


---

# Módulo: sql_tools

Ubicación

admin/apps/sql/

Descripción

Herramientas de administración de base de datos.

Funciones

- ejecutar consultas SQL
- visualizar tablas
- inspeccionar estructura de base de datos

Archivos principales

conexion.php  
consulta.php  
ver_tabla.php