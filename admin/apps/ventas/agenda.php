<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
require_once '../../../includes/utils.php';

// Filtros
$filtro = $_GET['filtro'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

try {
    $where = " WHERE 1=1 ";
    $params = [];
    if (!empty($filtro)) {
        $where .= " AND (c.Nombre LIKE ? OR c.Apellido LIKE ? OR p.IdPedido = ?)";
        $f = "%$filtro%";
        $params = [$f, $f, str_replace('#', '', $filtro)];
    }

    // Contar total
    $count_sql = "SELECT COUNT(*) FROM pedidos p JOIN clientes c ON p.IdCliente = c.IdCliente $where";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute($params);
    $total_rows = $stmt_count->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // Obtener pedidos
    $sql = "SELECT p.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido 
            FROM pedidos p 
            JOIN clientes c ON p.IdCliente = c.IdCliente 
            $where 
            ORDER BY p.Fecha DESC 
            LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Ventas | Stefy Barroso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="brand-title text-4xl text-emerald-900">Control de Ventas</h1>
            <button onclick="window.parent.closeAppModal()" class="w-10 h-10 bg-white border border-gray-200 flex items-center justify-center rounded-full text-2xl hover:bg-gray-100 transition-all text-gray-500 shadow-sm">
                &times;
            </button>
        </div>

        <!-- Buscador -->
        <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-100 mb-6 flex items-center">
            <form class="flex-1 relative" method="GET">
                <input type="text" name="filtro" value="<?= htmlspecialchars($filtro) ?>" 
                       class="w-full bg-gray-50 border-none rounded-xl py-3 pl-12 pr-4 text-emerald-900 font-medium focus:ring-2 focus:ring-emerald-400 outline-none transition-all shadow-inner" 
                       placeholder="Buscar por cliente o nro de pedido...">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl opacity-40">🔍</span>
            </form>
        </div>

        <!-- Tabla de Pedidos -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-emerald-50">
            <table class="w-full text-left">
                <thead class="bg-emerald-50 text-emerald-900 uppercase text-xs font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Pedido</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-50">
                    <?php if(empty($pedidos)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-gray-400 italic">No se encontraron pedidos.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($pedidos as $p): 
                            $es_activo = ($p['Estado'] === 'Activo' || $p['Estado'] == 1); // Manejamos ambos casos para mayor compatibilidad
                            $badge_class = $es_activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                            $estado_label = $es_activo ? 'ACTIVO' : 'CANCELADO';
                        ?>
                        <tr class="hover:bg-emerald-50/20 transition-colors">
                            <td class="px-6 py-4 font-bold text-emerald-900">#<?= $p['IdPedido'] ?></td>
                            <td class="px-6 py-4 text-gray-500 text-sm"><?= date('d/m/Y H:i', strtotime($p['Fecha'])) ?></td>
                            <td class="px-6 py-4 font-medium text-emerald-950"><?= htmlspecialchars($p['ClienteNombre'] . ' ' . $p['ClienteApellido']) ?></td>
                            <td class="px-6 py-4 font-black text-emerald-900">$ <?= number_format($p['Total'], 2, ',', '.') ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest <?= $badge_class ?>">
                                    <?= $estado_label ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-3">
                                    <?php if($es_activo): ?>
                                        <button onclick="cambiarEstado(<?= $p['IdPedido'] ?>, 'Inactivo')" class="bg-red-50 text-red-500 px-3 py-1 rounded-lg text-xs font-bold hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                            CANCELAR
                                        </button>
                                    <?php else: ?>
                                        <button onclick="cambiarEstado(<?= $p['IdPedido'] ?>, 'Activo')" class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-lg text-xs font-bold hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                            REACTIVAR
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if($total_pages > 1): ?>
        <div class="mt-8 flex justify-center gap-2">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&filtro=<?= urlencode($filtro) ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all <?= ($i == $page) ? 'bg-emerald-900 text-white shadow-lg' : 'bg-white text-emerald-900 hover:bg-emerald-50 shadow-sm' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function cambiarEstado(id, nuevoEstado) {
            const titulo = nuevoEstado === 'Inactivo' ? '¿Cancelar pedido?' : '¿Reactivar pedido?';
            const texto = nuevoEstado === 'Inactivo' ? 'Esto devolverá el stock de los productos al sistema.' : 'Esto volverá a descontar el stock de los artículos.';
            const color = nuevoEstado === 'Inactivo' ? '#ef4444' : '#059669';

            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id_pedido', id);
                    formData.append('estado', nuevoEstado);

                    fetch('ajax_actualizar_estado_pedido.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('¡Listo!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'No se pudo comunicar con el servidor', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
