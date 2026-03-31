<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
require_once '../../../includes/utils.php';

// --- Lógica de Guardado ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $codigo = $_POST['codigo'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $id_proveedor = intval($_POST['id_proveedor']) ?: null;

        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE productos SET Codigo = ?, Nombre = ?, Descripcion = ?, Precio = ?, Stock = ?, IdProveedor = ? WHERE IdProducto = ?");
                $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock, $id_proveedor, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO productos (Codigo, Nombre, Descripcion, Precio, Stock, IdProveedor) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock, $id_proveedor]);
            }
            $success_msg = "Producto guardado correctamente.";
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $pdo->prepare("UPDATE productos SET Estado = 0 WHERE IdProducto = ?")->execute([$id]);
        $success_msg = "Producto eliminado.";
    }
}

// Obtener productos con nombre de proveedor
$productos = $pdo->query("SELECT p.*, prov.NombreComercial as Proveedor 
                          FROM productos p 
                          LEFT JOIN proveedores prov ON p.IdProveedor = prov.IdProveedor 
                          WHERE p.Estado = 1 
                          ORDER BY p.Nombre ASC")->fetchAll();

// Obtener lista de proveedores para el select
$proveedores = $pdo->query("SELECT IdProveedor, NombreComercial FROM proveedores WHERE Estado = 1 ORDER BY NombreComercial ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario | Stefy Barroso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css?v=4.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Cabecera Estilo Pop-up (Centralizada) -->
        <?php render_premium_header('Inventario', 'openModal()'); ?>

        <?php if(isset($success_msg)): ?>
            <script>Swal.fire('¡Éxito!', '<?= $success_msg ?>', 'success');</script>
        <?php endif; ?>

        <!-- Listado de Productos (Subplacas) -->
        <div class="space-y-3">
            <?php foreach($productos as $p): ?>
                <div class="subplaca-adn">
                    <div class="subplaca-acento bg-emerald-500"></div>
                    <div class="subplaca-cuerpo">
                        <div class="subplaca-info">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-black"><?= htmlspecialchars($p['Codigo'] ?: '-') ?></span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight"><?= htmlspecialchars($p['Proveedor'] ?: 'SIN PROVEEDOR') ?></span>
                            </div>
                            <h3 class="font-bold text-emerald-950 text-[1.15rem] leading-tight"><?= htmlspecialchars($p['Nombre']) ?></h3>
                            <div class="flex items-center gap-3 mt-1.5">
                                <div class="flex items-center gap-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock:</span>
                                    <span class="text-[12px] font-black <?= $p['Stock'] < 5 ? 'text-red-500' : 'text-emerald-600' ?>"><?= $p['Stock'] ?></span>
                                </div>
                                <span class="text-gray-200">|</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Precio:</span>
                                    <span class="text-[12px] font-black text-emerald-900">$ <?= number_format($p['Precio'], 2, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="subplaca-acciones !flex-row !items-center !gap-2">
                             <button onclick='editProducto(<?= json_encode($p) ?>)' class="w-8 h-8 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-sm hover:bg-emerald-950 hover:text-white transition-all shadow-sm">✏️</button>
                             <button onclick='deleteProducto(<?= $p["IdProducto"] ?>)' class="w-8 h-8 bg-red-50 text-red-400 rounded-full flex items-center justify-center text-sm hover:bg-red-500 hover:text-white transition-all shadow-sm">🗑️</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($productos)): ?>
                <div class="bg-white rounded-3xl p-10 text-center shadow-sm border border-emerald-50 text-gray-400 italic font-medium">
                    No hay productos registrados.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modales -->
    <?php include 'partials/modal_nuevo_producto.php'; ?>

    <!-- Form de eliminación oculta -->
    <form id="formDelete" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script>
        function editProducto(p) {
            document.getElementById('modalTitle').innerText = 'Editar Producto';
            document.getElementById('prod_id').value = p.IdProducto;
            document.getElementById('prod_codigo').value = p.Codigo || '';
            document.getElementById('prod_nombre').value = p.Nombre;
            document.getElementById('prod_descripcion').value = p.Descripcion;
            document.getElementById('prod_precio').value = p.Precio;
            document.getElementById('prod_stock').value = p.Stock;
            document.getElementById('prod_id_proveedor').value = p.IdProveedor || '';
            document.getElementById('modalProd').classList.remove('hidden');
        }

        function deleteProducto(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El producto quedará inactivo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_id').value = id;
                    document.getElementById('formDelete').submit();
                }
            });
        }
    </script>
</body>
</html>
