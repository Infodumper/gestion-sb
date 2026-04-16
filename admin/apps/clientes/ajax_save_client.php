<?php
/**
 * Endpoint: Guardar / Actualizar Cliente
 * Contrato: POST JSON → { status: 'ok|error', data?: {...}, message: string }
 */

require_once '../../../includes/security.php';
require_once '../../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response('error', null, 'Método no permitido', 405);
}

// ── Leer payload (soporta tanto form-data como JSON body) ──────────────────────
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_contains($contentType, 'application/json')) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $input = $_POST;
}

$idCliente = intval($input['id_cliente'] ?? $input['id'] ?? 0);
$nombre    = ucwords(strtolower(trim($input['nombre'] ?? '')));
$apellido  = ucwords(strtolower(trim($input['apellido'] ?? '')));
$dni       = preg_replace('/[\s\-\.]/', '', trim($input['dni'] ?? ''));
$telefono  = preg_replace('/\D/', '', trim($input['telefono'] ?? ''));
$dia       = $input['dia_nac'] ?? '';
$mes       = $input['mes_nac'] ?? '';
$promo     = isset($input['Promociones']) ? 1 : 0;
$estado    = isset($input['estado']) ? intval($input['estado']) : 1;

// ── Validaciones básicas ───────────────────────────────────────────────────────
if (empty($nombre)) {
    json_response('error', null, 'El nombre es obligatorio');
}

if (!empty($telefono) && strlen($telefono) < 7) {
    json_response('error', null, 'El teléfono debe tener al menos 7 dígitos');
}

// Normalizar DNI y teléfono a NULL si vacíos
$dniVal  = empty($dni)      ? null : $dni;
$telVal  = empty($telefono) ? null : $telefono;

// Construir FechaNac
$fechaNac = null;
if ($dia && $mes && checkdate((int)$mes, (int)$dia, 2000)) {
    $fechaNac = sprintf('2000-%02d-%02d', (int)$mes, (int)$dia);
}

$nombreCompleto = trim("$nombre $apellido");

try {
    // ── Verificar duplicado de teléfono ────────────────────────────────────────
    if ($telVal) {
        $chkTel = $pdo->prepare(
            "SELECT IdCliente FROM clientes WHERE Telefono = ? AND IdCliente != ? LIMIT 1"
        );
        $chkTel->execute([$telVal, $idCliente]);
        if ($chkTel->fetch()) {
            json_response('error', null, 'Ese número de teléfono ya está registrado en otro cliente');
        }
    }

    // ── Verificar duplicado de DNI ─────────────────────────────────────────────
    if ($dniVal) {
        $chkDni = $pdo->prepare(
            "SELECT IdCliente FROM clientes WHERE Dni = ? AND IdCliente != ? LIMIT 1"
        );
        $chkDni->execute([$dniVal, $idCliente]);
        if ($chkDni->fetch()) {
            json_response('error', null, 'Ese DNI ya está registrado en otro cliente');
        }
    }

    if ($idCliente > 0) {
        // ── UPDATE ─────────────────────────────────────────────────────────────
        $stmt = $pdo->prepare("
            UPDATE clientes
            SET Nombre = ?, Apellido = ?, Dni = ?, Telefono = ?, FechaNac = ?, Promociones = ?, Estado = ?
            WHERE IdCliente = ?
        ");
        $stmt->execute([$nombre, $apellido, $dniVal, $telVal, $fechaNac, $promo, $estado, $idCliente]);

        log_event('UPDATE', "Cliente ID=$idCliente ($nombreCompleto) actualizado", __FILE__);
        json_response('ok', ['id' => $idCliente], 'Cliente actualizado correctamente');

    } else {
        // ── INSERT ─────────────────────────────────────────────────────────────
        $stmt = $pdo->prepare("
            INSERT INTO clientes (Nombre, Apellido, Dni, Telefono, FechaNac, Promociones, Estado)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$nombre, $apellido, $dniVal, $telVal, $fechaNac, $promo]);
        $newId = $pdo->lastInsertId();

        log_event('INSERT', "Nuevo cliente ID=$newId ($nombreCompleto) creado por uid=" . $_SESSION['userid'], __FILE__);
        json_response('ok', ['id' => $newId], 'Cliente registrado correctamente');
    }

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    json_response('error', null, 'Error interno del servidor', 500);
}
