-- Database Creation
CREATE DATABASE IF NOT EXISTS consultora_belleza;
USE consultora_belleza;

-- 1. Table: DbLogin
CREATE TABLE IF NOT EXISTS DbLogin (
    IdUsuario INT AUTO_INCREMENT PRIMARY KEY,
    Usuario VARCHAR(50) UNIQUE NOT NULL,
    Clave VARCHAR(255) NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    Rol VARCHAR(20) NOT NULL DEFAULT 'admin',
    Estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table: Servicios
CREATE TABLE IF NOT EXISTS Servicios (
    IdServicio INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    Duracion INT COMMENT 'Duración en minutos',
    Precio DECIMAL(10, 2) NOT NULL,
    Estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: Turnos
CREATE TABLE IF NOT EXISTS Turnos (
    IdTurno INT AUTO_INCREMENT PRIMARY KEY,
    IdCliente INT NOT NULL,
    IdServicio INT NOT NULL,
    FechaTurno DATETIME NOT NULL,
    Estado TINYINT DEFAULT 1 COMMENT '1: Pendiente, 2: Completado, 0: Cancelado',
    Notas TEXT,
    CONSTRAINT fk_turno_cliente FOREIGN KEY (IdCliente) REFERENCES Clientes(IdCliente) ON DELETE CASCADE,
    CONSTRAINT fk_turno_servicio FOREIGN KEY (IdServicio) REFERENCES Servicios(IdServicio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: Clientes
CREATE TABLE IF NOT EXISTS Clientes (
    IdCliente INT AUTO_INCREMENT PRIMARY KEY,
    NroCliente INT NULL COMMENT 'Opcional para catálogos',
    Nombre VARCHAR(100) DEFAULT 'Desconocido',
    Apellido VARCHAR(100) DEFAULT 'Desconocido',
    Dni VARCHAR(20) UNIQUE NULL,
    Telefono VARCHAR(20) UNIQUE NOT NULL,
    FechaNac DATE NULL,
    Promociones TINYINT(1) DEFAULT 1,
    Estado TINYINT(1) DEFAULT 1 COMMENT '1: Activo, 0: Baja, 2: Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: ContactosWhatsapp
CREATE TABLE IF NOT EXISTS ContactosWhatsapp (
    IdContacto INT AUTO_INCREMENT PRIMARY KEY,
    IdCliente INT NOT NULL,
    Tipo ENUM('cumple', 'habitual', 'mensual') NOT NULL,
    FechaContacto DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cliente_contacto FOREIGN KEY (IdCliente) REFERENCES Clientes(IdCliente) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: Productos
CREATE TABLE IF NOT EXISTS Productos (
    IdProducto INT AUTO_INCREMENT PRIMARY KEY,
    Codigo VARCHAR(50) UNIQUE NOT NULL,
    Nombre VARCHAR(255) NOT NULL,
    Descripcion TEXT,
    Precio DECIMAL(10, 2) NOT NULL,
    Stock INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table: Pedidos
CREATE TABLE IF NOT EXISTS Pedidos (
    IdPedido INT AUTO_INCREMENT PRIMARY KEY,
    IdCliente INT NOT NULL,
    IdUsuario INT NOT NULL COMMENT 'Usuario que registró el pedido',
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Total DECIMAL(10, 2) DEFAULT 0.00,
    Estado TINYINT DEFAULT 1 COMMENT '1: Pendiente, 2: Pagado, 3: Entregado',
    CONSTRAINT fk_cliente_pedido FOREIGN KEY (IdCliente) REFERENCES Clientes(IdCliente) ON DELETE CASCADE,
    CONSTRAINT fk_usuario_pedido FOREIGN KEY (IdUsuario) REFERENCES DbLogin(IdUsuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table: ItemsPedido
CREATE TABLE IF NOT EXISTS ItemsPedido (
    IdItem INT AUTO_INCREMENT PRIMARY KEY,
    IdPedido INT NOT NULL,
    IdProducto INT NOT NULL,
    Cantidad INT NOT NULL DEFAULT 1,
    PrecioUnitario DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_pedido FOREIGN KEY (IdPedido) REFERENCES Pedidos(IdPedido) ON DELETE CASCADE,
    CONSTRAINT fk_producto FOREIGN KEY (IdProducto) REFERENCES Productos(IdProducto) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Data
-- Password: ID9800
INSERT INTO DbLogin (Usuario, Clave, Nombre, Rol, Estado) 
VALUES ('infodumper.au@gmail.com', '$2y$10$8.X0gHl5zO9fF.KzHjA9xe.r0bO8fPqMvR3p5R4V4R4V4R4V4R4V4', 'Infodumper', 'admin', 1)
ON DUPLICATE KEY UPDATE Usuario=Usuario;
