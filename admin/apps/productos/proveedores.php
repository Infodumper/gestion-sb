<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';

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
    <link rel="stylesheet" href="../../../styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="brand-title text-4xl text-emerald-900">Proveedores</h1>
            <div class="flex gap-2">
                <button onclick="openModal()" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold shadow-lg hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <span>➕</span> NUEVO PROVEEDOR
                </button>
                <button onclick="window.parent.closeAppModal()" class="w-10 h-10 bg-white border border-gray-200 flex items-center justify-center rounded-full text-2xl hover:bg-gray-100 transition-all text-gray-500 shadow-sm">
                    &times;
                </button>
            </div>
        </div>

        <?php if(isset($success_msg)): ?>
            <script>Swal.fire('¡Éxito!', '<?= $success_msg ?>', 'success');</script>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-emerald-50">
            <table class="w-full text-left">
                <thead class="bg-emerald-50 text-emerald-900 uppercase text-xs font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Nombre Comercial</th>
                        <th class="px-6 py-4">Contacto</th>
                        <th class="px-6 py-4">Teléfono</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-50">
                    <?php foreach($proveedores as $p): ?>
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-emerald-950"><?= htmlspecialchars($p['NombreComercial']) ?></div>
                            <div class="text-xs text-gray-400">CUIT: <?= htmlspecialchars($p['Cuit'] ?: '-') ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($p['Contacto'] ?: '-') ?></td>
                        <td class="px-6 py-4 text-gray-600 font-medium"><?= htmlspecialchars($p['Telefono'] ?: '-') ?></td>
                        <td class="px-6 py-4 text-gray-500 text-sm"><?= htmlspecialchars($p['Email'] ?: '-') ?></td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button onclick='editProveedor(<?= json_encode($p) ?>)' class="p-2 hover:bg-white rounded-lg transition-all" title="Editar">✏️</button>
                                <button onclick='deleteProveedor(<?= $p["IdProveedor"] ?>)' class="p-2 hover:bg-white rounded-lg transition-all text-red-400" title="Eliminar">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($proveedores)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-gray-400 italic">No hay proveedores registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
