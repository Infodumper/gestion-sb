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
        $nombre = $_POST['nombre'];
        $cuit = $_POST['cuit'];
        $contacto = $_POST['contacto'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];

        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE proveedores SET NombreComercial = ?, Cuit = ?, Contacto = ?, Telefono = ?, Email = ? WHERE IdProveedor = ?");
                $stmt->execute([$nombre, $cuit, $contacto, $telefono, $email, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO proveedores (NombreComercial, Cuit, Contacto, Telefono, Email) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $cuit, $contacto, $telefono, $email]);
            }
            $success_msg = "Proveedor guardado correctamente.";
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $pdo->prepare("UPDATE proveedores SET Estado = 0 WHERE IdProveedor = ?")->execute([$id]);
        $success_msg = "Proveedor eliminado.";
    }
}

$proveedores = $pdo->query("SELECT * FROM proveedores WHERE Estado = 1 ORDER BY NombreComercial ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores | Stefy Barroso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css?v=4.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Cabecera Estilo Pop-up (Centralizada) -->
        <?php render_premium_header('Proveedores', 'openModal()'); ?>

        <?php if(isset($success_msg)): ?>
            <script>Swal.fire('¡Éxito!', '<?= $success_msg ?>', 'success');</script>
        <?php endif; ?>

        <!-- Listado de Proveedores (Subplacas) -->
        <div class="space-y-3">
            <?php foreach($proveedores as $p): ?>
                <div class="subplaca-adn">
                    <div class="subplaca-acento bg-emerald-500"></div>
                    <div class="subplaca-cuerpo">
                        <div class="subplaca-info">
                            <h3 class="font-bold text-emerald-950 text-[1.15rem] leading-tight"><?= htmlspecialchars($p['NombreComercial']) ?></h3>
                            <p class="text-[11px] font-bold text-emerald-600 mt-0.5 tracking-wider"><?= htmlspecialchars($p['Telefono'] ?: 'SIN TELÉFONO') ?></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">👤 <?= htmlspecialchars($p['Contacto'] ?: 'SIN CONTACTO') ?></span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">|</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">ID: <?= htmlspecialchars($p['Cuit'] ?: '-') ?></span>
                            </div>
                        </div>
                        <div class="subplaca-acciones !flex-row !items-center !gap-2">
                             <button onclick='editProveedor(<?= json_encode($p) ?>)' class="w-8 h-8 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-sm hover:bg-emerald-950 hover:text-white transition-all shadow-sm">✏️</button>
                             <button onclick='deleteProveedor(<?= $p["IdProveedor"] ?>)' class="w-8 h-8 bg-red-50 text-red-400 rounded-full flex items-center justify-center text-sm hover:bg-red-500 hover:text-white transition-all shadow-sm">🗑️</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($proveedores)): ?>
                <div class="bg-white rounded-3xl p-10 text-center shadow-sm border border-emerald-50 text-gray-400 italic font-medium">
                    No hay proveedores registrados.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Formulario -->
    <div id="modalProv" class="hidden fixed inset-0 z-[100] bg-emerald-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 id="modalTitle" class="brand-title text-2xl text-emerald-900">Nuevo Proveedor</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form method="POST" class="p-8 space-y-4">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="prov_id" value="0">
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nombre Comercial</label>
                    <input type="text" name="nombre" id="prov_nombre" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">CUIT</label>
                        <input type="text" name="cuit" id="prov_cuit" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nombre de Contacto</label>
                        <input type="text" name="contacto" id="prov_contacto" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="prov_telefono" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Email</label>
                        <input type="email" name="email" id="prov_email" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-bold shadow-lg hover:opacity-90 active:scale-95 transition-all uppercase tracking-widest">
                        Guardar Proveedor
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
            document.getElementById('modalTitle').innerText = 'Nuevo Proveedor';
            document.getElementById('prov_id').value = '0';
            document.getElementById('prov_nombre').value = '';
            document.getElementById('prov_cuit').value = '';
            document.getElementById('prov_contacto').value = '';
            document.getElementById('prov_telefono').value = '';
            document.getElementById('prov_email').value = '';
            document.getElementById('modalProv').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalProv').classList.add('hidden');
        }

        function editProveedor(p) {
            document.getElementById('modalTitle').innerText = 'Editar Proveedor';
            document.getElementById('prov_id').value = p.IdProveedor;
            document.getElementById('prov_nombre').value = p.NombreComercial;
            document.getElementById('prov_cuit').value = p.Cuit;
            document.getElementById('prov_contacto').value = p.Contacto;
            document.getElementById('prov_telefono').value = p.Telefono;
            document.getElementById('prov_email').value = p.Email;
            document.getElementById('modalProv').classList.remove('hidden');
        }

        function deleteProveedor(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El proveedor quedará inactivo.",
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
