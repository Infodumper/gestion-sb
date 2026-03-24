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
    if (empty($telefono)) {
        throw new Exception('El Teléfono es obligatorio.');
    }

    // Sanitizar teléfono
    $telefono = preg_replace('/[^0-9]/', '', $telefono);

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
        // Verificar Teléfono duplicado (excluyendo el actual)
        $stmtChk = $pdo->prepare("SELECT IdCliente FROM clientes WHERE Telefono = ? AND IdCliente != ? LIMIT 1");
        $stmtChk->execute([$telefono, $id_cliente]);
        if ($stmtChk->fetch()) {
            throw new Exception("El número de teléfono ya está registrado en otro cliente.");
        }

        $sql = "UPDATE clientes SET Nombre=?, Apellido=?, Dni=?, Telefono=?, FechaNac=?, Promociones=?, Estado=? WHERE IdCliente=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $dni_val, $telefono, $fechaNac, $promo, $estado, $id_cliente]);
        
        log_event($pdo, 'CLIENTE_ACTUALIZADO', "ID: $id_cliente - $nombre $apellido");
        echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente.']);
    } else {
        // --- INSERT ---
        // Verificar Teléfono duplicado
        $stmtChk = $pdo->prepare("SELECT IdCliente FROM clientes WHERE Telefono = ? LIMIT 1");
        $stmtChk->execute([$telefono]);
        if ($stmtChk->fetch()) {
            throw new Exception("El número de teléfono ya está registrado.");
        }

        $sql = "INSERT INTO clientes (Nombre, Apellido, Dni, Telefono, FechaNac, Promociones, Estado) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $dni_val, $telefono, $fechaNac, $promo]);

        $newId = $pdo->lastInsertId();
        log_event($pdo, 'CLIENTE_CREADO', "ID: $newId - $nombre $apellido");
        echo json_encode(['success' => true, 'message' => 'Cliente guardado correctamente.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
