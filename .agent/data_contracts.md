# Data Contracts

Este archivo define los contratos de datos del sistema.

Los agentes deben respetar estas estructuras
al crear consultas SQL, endpoints o formularios.

No modificar estos contratos sin aprobación.

---

# Contrato: Cliente

Tabla

clientes

Campos

IdCliente      INT PRIMARY KEY AUTO_INCREMENT  
Nro            INT  
Cliente        VARCHAR(120)  
Lider          VARCHAR(120)

Campos obligatorios

Nro  
Cliente

Campos opcionales

Lider

Descripción

Representa un cliente registrado en el sistema.

---

# Contrato: Datos_Completos

Tabla

datos_completos

Campos

IdDato     INT PRIMARY KEY AUTO_INCREMENT  
Cliente    VARCHAR(120)  
FecNac     DATE  
Telefono   VARCHAR(30)  
Direccion  VARCHAR(200)

Relación

Cliente → clientes.Cliente

Descripción

Datos adicionales del cliente.

---

# Contrato: Pedido

Tabla

pedidos

Campos

IdPedido     INT PRIMARY KEY AUTO_INCREMENT  
IdCliente    INT  
Fecha        DATE  
Campaña      VARCHAR(10)  
Total        DECIMAL(10,2)

Campos obligatorios

IdCliente  
Fecha

Relaciones

IdCliente → clientes.IdCliente

---

# Contrato: DetallePedido

Tabla

detalle_pedidos

Campos

IdDetalle    INT PRIMARY KEY AUTO_INCREMENT  
IdPedido     INT  
IdProducto   INT  
Cantidad     INT  
Precio       DECIMAL(10,2)

Relaciones

IdPedido → pedidos.IdPedido  
IdProducto → productos.IdProducto

---

# Contrato: Producto

Tabla

productos

Campos

IdProducto     INT PRIMARY KEY AUTO_INCREMENT  
Nombre         VARCHAR(120)  
Precio         DECIMAL(10,2)  
Stock          INT

Campos obligatorios

Nombre  
Precio

Descripción

Producto disponible para venta.