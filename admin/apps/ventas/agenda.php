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
    <link rel="stylesheet" href="../../../styles/main.css?v=4.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen p-2 sm:p-4">
    <div class="max-w-6xl mx-auto">
        <!-- Cabecera Estilo Pop-up (Centralizada) -->
        <?php render_premium_header('Control de Ventas'); ?>

        <!-- Buscador -->
        <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-100 mb-6 flex items-center">
            <form class="flex-1 relative" method="GET">
                <input type="text" name="filtro" value="<?= htmlspecialchars($filtro) ?>" 
                       class="w-full bg-gray-50 border-none rounded-xl py-3 pl-12 pr-4 text-emerald-900 font-medium focus:ring-2 focus:ring-emerald-400 outline-none transition-all shadow-inner" 
                       placeholder="Buscar por cliente o nro de pedido...">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl opacity-40">🔍</span>
            </form>
        </div>

        <!-- Lista de Pedidos -->
        <div class="space-y-3">
            <?php if(empty($pedidos)): ?>
                <div class="bg-white rounded-3xl p-10 text-center shadow-sm border border-emerald-50 text-gray-400 italic font-medium">
                    No se encontraron pedidos.
                </div>
            <?php else: ?>
                <?php foreach($pedidos as $p): 
                    $es_activo = ($p['Estado'] === 'Activo' || $p['Estado'] == 1); // Manejamos ambos casos para mayor compatibilidad
                    $badge_class = $es_activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                    $estado_label = $es_activo ? 'ACTIVO' : 'CANCELADO';
                    $acento_bg = $es_activo ? 'bg-emerald-500' : 'bg-red-400';
                ?>
                <div class="subplaca-adn cursor-pointer" onclick="verDetalles(<?= $p['IdPedido'] ?>)">
                    <div class="subplaca-acento <?= $acento_bg ?>"></div>
                    <div class="subplaca-cuerpo flex-col sm:flex-row gap-4">
                        <div class="subplaca-info flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-bold text-emerald-900 border border-emerald-100 bg-emerald-50 px-2 rounded-md">#<?= $p['IdPedido'] ?></span>
                                <span class="text-xs font-semibold text-gray-500"><?= date('d/m/Y H:i', strtotime($p['Fecha'])) ?></span>
                            </div>
                            <div class="font-bold text-emerald-950 text-[1.1rem] leading-tight">
                                <?= htmlspecialchars($p['ClienteNombre'] . ' ' . $p['ClienteApellido']) ?>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto mt-2 sm:mt-0">
                            <div class="text-center">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black tracking-widest <?= $badge_class ?>">
                                    <?= $estado_label ?>
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total</span>
                                <span class="font-black text-xl text-emerald-900">$ <?= number_format($p['Total'], 2, ',', '.') ?></span>
                            </div>
                        </div>
                        <div class="subplaca-acciones border-t sm:border-l sm:border-t-0 border-gray-100 pt-3 sm:pt-0 sm:pl-4 mt-3 sm:mt-0 justify-center w-full sm:w-auto">
                            <?php if($es_activo): ?>
                                <button onclick="cambiarEstado(<?= $p['IdPedido'] ?>, 'Inactivo')" class="w-full sm:w-auto bg-red-50 text-red-500 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    CANCELAR
                                </button>
                            <?php else: ?>
                                <button onclick="cambiarEstado(<?= $p['IdPedido'] ?>, 'Activo')" class="w-full sm:w-auto bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                    REACTIVAR
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
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

    <!-- Modal Detalle Venta -->
    <div id="modalDetalle" class="hidden fixed inset-0 z-[110] bg-emerald-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="max-w-2xl w-full card-premium overflow-hidden animate-in fade-in zoom-in duration-300">
             <div class="bg-emerald-50 px-8 py-6 border-b border-indigo-100 text-center relative">
                <button type="button" onclick="closeModal()" class="btn-close-premium" title="Cerrar">&times;</button>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest block mb-1" id="detNroPedido"></span>
                <h2 id="detCliente" class="brand-title text-3xl mb-1 text-emerald-950"></h2>
                <p id="detFecha" class="text-emerald-950/60 font-bold tracking-widest text-[10px]"></p>
            </div>
            
            <div class="p-6">
                <div class="bg-gray-50 p-4 rounded-3xl">
                    <h4 class="font-bold text-emerald-950 mb-3 flex items-center text-sm">🛍️ PRODUCTOS EN ESTE PEDIDO</h4>
                    <div id="detItems" class="space-y-2">
                        <!-- AJAX generated -->
                    </div>
                </div>
            </div>

            <div class="px-8 py-4 border-t border-gray-100 flex justify-between items-center bg-gray-50/30">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total del Pedido</span>
                <span class="text-3xl font-black text-emerald-900" id="detTotal"></span>
            </div>
            <div class="p-6">
                <button onclick="closeModal()" class="w-full btn-premium">CERRAR DETALLE</button>
            </div>
        </div>
    </div>

    <script>
        function verDetalles(id) {
            fetch(`ajax_get_pedido_details.php?id=${id}`)
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    const p = data.pedido;
                    document.getElementById('detNroPedido').innerText = `Pedido #${p.id}`;
                    document.getElementById('detCliente').innerText = p.cliente;
                    document.getElementById('detFecha').innerText = p.fecha;
                    document.getElementById('detTotal').innerText = `$ ${p.total}`;
                    
                    const itemsDiv = document.getElementById('detItems');
                    itemsDiv.innerHTML = '';
                    data.items.forEach(h => {
                        itemsDiv.innerHTML += `
                            <div class="flex justify-between items-center bg-white p-3 rounded-2xl shadow-sm border border-emerald-50/50">
                                <div class="flex-1">
                                    <span class="block text-xs font-bold text-emerald-950">${h.nombre}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Cant: ${h.cantidad} x ${h.precio}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-black text-emerald-900 text-sm">$${h.subtotal}</span>
                                </div>
                            </div>
                        `;
                    });

                    document.getElementById('modalDetalle').classList.remove('hidden');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'No se pudo comunicar con el servidor', 'error');
            });
        }

        function closeModal() {
            document.getElementById('modalDetalle').classList.add('hidden');
        }

        function cambiarEstado(id, nuevoEstado) {
            event.stopPropagation(); // Evitar abrir el modal al clickear el botón
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
