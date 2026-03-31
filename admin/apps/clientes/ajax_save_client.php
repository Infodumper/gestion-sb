<?php
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
    exit;
}

require_once '../../../includes/db.php';
require_once '../../../includes/security.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no válido.');
    }

    $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni      = trim($_POST['dni'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $dia      = $_POST['dia_nac'] ?? '';
    $mes      = $_POST['mes_nac'] ?? '';
    $promo    = isset($_POST['Promociones']) ? 1 : 0;
    $estado   = isset($_POST['estado']) ? intval($_POST['estado']) : 1;

    if (empty($nombre)) {
        $nombre = 'Desconocido';
    }
    if (empty($apellido)) {
        $apellido = '';
    }
    // Sanitizar teléfono si no está vacío
    $tel_val = NULL;
    if (!empty($telefono)) {
        $tel_val = preg_replace('/[^0-9]/', '', $telefono);
    }

    $fechaNac = NULL;
    if ($dia && $mes) {
        if (checkdate((int)$mes, (int)$dia, 2000)) {
            $fechaNac = sprintf('2000-%02d-%02d', $mes, $dia);
        }
    }

    // Manejar DNI como NULL si está vacío para evitar conflictos de Unique
    $dni_val = empty($dni) ? NULL : $dni;

    if ($id_cliente > 0) {
        // --- UPDATE ---
        // Verificar Teléfono duplicado SOLO si se ingresó uno
        if ($tel_val) {
            $stmtChk = $pdo->prepare("SELECT IdCliente FROM clientes WHERE Telefono = ? AND IdCliente != ? LIMIT 1");
            $stmtChk->execute([$tel_val, $id_cliente]);
            if ($stmtChk->fetch()) {
                throw new Exception("El número de teléfono ya está registrado en otro cliente.");
            }
        }

        // Verificar DNI duplicado SOLO si se ingresó uno
        if ($dni_val) {
            $stmtChk = $pdo->prepare("SELECT IdCliente FROM clientes WHERE Dni = ? AND IdCliente != ? LIMIT 1");
            $stmtChk->execute([$dni_val, $id_cliente]);
            if ($stmtChk->fetch()) {
                throw new Exception("El DNI ya está registrado en otro cliente.");
            }
        }

        $sql = "UPDATE clientes SET Nombre=?, Apellido=?, Dni=?, Telefono=?, FechaNac=?, Promociones=?, Estado=? WHERE IdCliente=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $dni_val, $tel_val, $fechaNac, $promo, $estado, $id_cliente]);
        
        log_event($pdo, 'CLIENTE_ACTUALIZADO', "ID: $id_cliente - $nombre $apellido");
        echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente.']);
    } else {
        // --- INSERT ---
        // Verificar Teléfono duplicado SOLO si se ingresó uno
        if ($tel_val) {
            $stmtChk = $pdo->prepare("SELECT IdCliente FROM clientes WHERE Telefono = ? LIMIT 1");
            $stmtChk->execute([$tel_val]);
            if ($stmtChk->fetch()) {
                throw new Exception("El número de teléfono ya está registrado.");
            }
        }

        // Verificar DNI duplicado SOLO si se ingresó uno
        if ($dni_val) {
            $stmtChk = $pdo->prepare("SELECT IdCliente FROM clientes WHERE Dni = ? LIMIT 1");
            $stmtChk->execute([$dni_val]);
            if ($stmtChk->fetch()) {
                throw new Exception("El DNI ya está registrado.");
            }
        }

        $sql = "INSERT INTO clientes (Nombre, Apellido, Dni, Telefono, FechaNac, Promociones, Estado) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $dni_val, $tel_val, $fechaNac, $promo]);

        $newId = $pdo->lastInsertId();
        log_event($pdo, 'CLIENTE_CREADO', "ID: $newId - $nombre $apellido");
        echo json_encode(['success' => true, 'message' => 'Cliente guardado correctamente.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
