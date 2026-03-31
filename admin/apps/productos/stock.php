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

    <!-- Modal Formulario -->
    <div id="modalProd" class="hidden fixed inset-0 z-[100] bg-emerald-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 id="modalTitle" class="brand-title text-2xl text-emerald-900">Nuevo Producto</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form method="POST" class="p-8 space-y-4">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="prod_id" value="0">
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Código de Producto</label>
                    <input type="text" name="codigo" id="prod_codigo" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" placeholder="Ej: CRAv01">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nombre del Producto</label>
                    <input type="text" name="nombre" id="prod_nombre" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Descripción</label>
                    <textarea name="descripcion" id="prod_descripcion" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none h-20"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Precio</label>
                        <input type="number" step="0.01" name="precio" id="prod_precio" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Stock Inicial</label>
                        <input type="number" name="stock" id="prod_stock" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Proveedor</label>
                    <select name="id_proveedor" id="prod_id_proveedor" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all appearance-none">
                        <option value="">Seleccionar Proveedor...</option>
                        <?php foreach($proveedores as $prov): ?>
                            <option value="<?= $prov['IdProveedor'] ?>"><?= htmlspecialchars($prov['NombreComercial']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-bold shadow-lg hover:opacity-90 active:scale-95 transition-all uppercase tracking-widest">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form de eliminación oculta -->
    <form id="formDelete" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Nuevo Producto';
            document.getElementById('prod_id').value = '0';
            document.getElementById('prod_codigo').value = '';
            document.getElementById('prod_nombre').value = '';
            document.getElementById('prod_descripcion').value = '';
            document.getElementById('prod_precio').value = '';
            document.getElementById('prod_stock').value = '0';
            document.getElementById('prod_id_proveedor').value = '';
            document.getElementById('modalProd').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalProd').classList.add('hidden');
        }

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
