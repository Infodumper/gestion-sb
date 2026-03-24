<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
header('Content-Type: text/html; charset=utf-8');

// --- Lógica de Filtros y Búsqueda ---
$filtro = $_GET['filtro'] ?? '';
$sort_col = $_GET['sort'] ?? 'Apellido';
$sort_order = $_GET['order'] ?? 'ASC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Columnas seguras para ordenar
$allowed_cols = ['Nombre', 'Apellido', 'Telefono', 'Dni', 'FechaNac'];
if (!in_array($sort_col, $allowed_cols)) $sort_col = 'Apellido';
$sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

try {
    // 1. Contar Total
    $where = " WHERE 1=1 ";
    $params = [];
    if (!empty($filtro)) {
        $where .= " AND (Nombre LIKE ? OR Apellido LIKE ? OR Telefono LIKE ? OR Dni LIKE ?)";
        $f = "%$filtro%";
        $params = [$f, $f, $f, $f];
    }
    
    $count_sql = "SELECT COUNT(*) FROM clientes $where";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute($params);
    $total_rows = $stmt_count->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // 2. Obtener Clientes
    $sql = "SELECT * FROM clientes $where ORDER BY $sort_col $sort_order LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = $e->getMessage();
}

function make_sort_link($col, $label, $current_col, $current_order, $current_filter) {
    $new_order = ($current_col === $col && $current_order === 'ASC') ? 'DESC' : 'ASC';
    $icon = '';
    if ($current_col === $col) {
        $icon = ($current_order === 'ASC') ? ' ▲' : ' ▼';
    }
    $url = "?sort=$col&order=$new_order";
    if (!empty($current_filter)) $url .= "&filtro=" . urlencode($current_filter);
    return "<a href=\"$url\" class=\"inline-flex items-center hover:text-orange-500 transition-colors uppercase tracking-wider\">$label<span class='ml-1 text-[10px]'>$icon</span></a>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de Clientes | Stefy Barroso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .table-custom { border-collapse: separate; border-spacing: 0 0.5rem; }
        .table-custom tr { transition: all 0.2s; }
        .table-custom thead th { border-bottom: 2px solid #e2e8f0; }
        .table-custom tbody tr:hover { transform: scale(1.005); box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="min-h-screen pt-12 pb-4 px-2 sm:px-10">
    <script>
        if (window.self !== window.top) {
            document.body.classList.add('is-iframe');
        }
    </script>
    <style>
        .is-iframe .back-panel-link { display: none !important; }
        .is-iframe { background-color: white !important; py: 2rem !important; }
    </style>

    <div class="max-w-7xl mx-auto">
        <!-- Header Simplificado (Index Navbar visible arriba) -->
        <div class="h-4"></div>

        <div class="flex flex-row justify-between items-baseline mb-4 gap-2">
            <h2 class="brand-title text-4xl ml-2">Clientes</h2>
            <div class="flex items-center gap-2">
                <button onclick="openClientModal()" class="btn-premium flex items-center shadow-lg px-4 py-2 text-sm">
                    <span class="mr-1">➕</span> <span class="hidden sm:inline">NUEVO</span><span class="sm:hidden">NUEVO</span>
                </button>
                <button onclick="window.parent.closeAppModal()" class="w-10 h-10 bg-white border border-gray-200 flex items-center justify-center rounded-full text-2xl hover:bg-gray-100 transition-all text-gray-500 shadow-sm" title="Cerrar">
                    &times;
                </button>
            </div>
        </div>

        <!-- Search Card -->
        <div class="bg-white rounded-2xl p-3 shadow-lg border border-gray-100 mb-4 flex items-center">
            <form class="flex-1 relative" method="GET">
                <input type="text" name="filtro" value="<?= htmlspecialchars($filtro) ?>" 
                       class="w-full bg-gray-50 border-none rounded-xl py-2 pl-10 pr-4 text-indigo-900 font-medium focus:ring-1 focus:ring-purple-400 outline-none transition-all shadow-inner text-sm" 
                       placeholder="Buscar...">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-lg">🔍</span>
            </form>
        </div>

        <!-- Contenedor "Chapa" Principal -->
        <div class="bg-gray-50/50 rounded-3xl p-2 md:p-6 shadow-none border-none overflow-hidden">
            
            <!-- Listado en "Chapitas" -->
            <div class="flex flex-col gap-3">
                <?php if (empty($clientes)): ?>
                    <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                        <p class="text-gray-400 font-medium italic text-lg">No se encontraron clientes.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($clientes as $c): ?>
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 relative group cursor-pointer hover:shadow-md transition-all flex flex-col sm:flex-row justify-between" onclick="showDetails(<?= $c['IdCliente'] ?>)">
                        
                        <!-- Barra de Acento Izquierdo -->
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-orange-400 rounded-l-2xl group-hover:bg-purple-500 transition-colors"></div>
                        
                        <!-- Bloque de Info Principal -->
                        <div class="pl-2 flex-1">
                            <h3 class="font-bold text-[1.15rem] text-indigo-900 leading-snug">
                                <?= htmlspecialchars($c['Apellido']) ?> <?= htmlspecialchars($c['Nombre']) ?>
                            </h3>
                            <p class="text-indigo-900/70 font-bold tracking-widest text-sm mt-1">
                                <?= htmlspecialchars($c['Telefono']) ?>
                            </p>
                            
                            <!-- Metadatos de la chapita (DNI, Cumpleaños) -->
                            <div class="flex flex-wrap gap-4 mt-3 pt-3 border-t border-slate-50 text-xs font-semibold text-slate-400 uppercase">
                                <?php if($c['Dni']): ?>
                                    <span class="flex items-center gap-1">ID: <?= htmlspecialchars($c['Dni']) ?></span>
                                <?php endif; ?>
                                <?php if($c['FechaNac']): ?>
                                    <span class="flex items-center gap-1 text-orange-500">BDAY: <?= date('d/m', strtotime($c['FechaNac'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Panel de Acciones Celular (Botones) -->
                        <div class="flex sm:flex-col justify-end items-center sm:items-end gap-2 mt-4 sm:mt-0 pt-3 sm:pt-0 border-t sm:border-0 border-slate-50">
                            <button onclick="event.stopPropagation(); openEditClient(<?= $c['IdCliente'] ?>)" class="w-12 h-12 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center hover:bg-orange-100 transition-all text-xl" title="Editar Ficha">
                                ✏️
                            </button>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$c['Telefono']) ?>" target="_blank" onclick="event.stopPropagation();" class="flex-1 sm:flex-none w-full sm:w-12 h-12 bg-green-500 text-white rounded-xl flex items-center justify-center hover:bg-green-600 transition-all text-2xl shadow-sm" title="WhatsApp Directo">
                                💬
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="mt-10 flex justify-center gap-2">
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>&filtro=<?= urlencode($filtro) ?>&sort=<?= $sort_col ?>&order=<?= $sort_order ?>" 
                       class="w-12 h-12 flex items-center justify-center rounded-2xl font-bold transition-all <?= ($i == $page) ? 'bg-indigo-900 text-white shadow-lg scale-110' : 'bg-gray-50 text-indigo-900 hover:bg-orange-100 shadow-sm' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <!-- Action Buttons at the Bottom (Optional Desktop) -->
            <div class="hidden md:flex mt-12 pt-8 border-t border-gray-100 flex-wrap justify-between items-center gap-4">
                <div class="flex gap-4">
                    <button type="button" onclick="exportExcel()" class="bg-green-500 text-white px-6 py-4 rounded-2xl hover:bg-green-600 transition shadow-lg flex items-center" title="Exportar a Excel">
                        <span class="mr-2">📊</span> Exportar lista
                    </button>
                    <button type="button" onclick="openImportModal()" class="bg-blue-500 text-white px-6 py-4 rounded-2xl hover:bg-blue-600 transition shadow-lg flex items-center" title="Importar desde CSV">
                        <span class="mr-2">📤</span> Importar datos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Cliente -->
    <div id="modalDetalle" class="hidden fixed inset-0 z-[110] bg-indigo-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="max-w-2xl w-full card-premium overflow-hidden animate-in fade-in zoom-in duration-300">
             <button onclick="closeModal('modalDetalle')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 z-10 transition text-3xl">&times;</button>
             <div class="bg-indigo-50 px-8 py-10 border-b border-indigo-100 text-center">
                <div id="detInitials" class="w-20 h-20 bg-indigo-900 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 border-4 border-white shadow-xl"></div>
                <h2 id="detNombre" class="brand-title text-4xl mb-2 text-indigo-900"></h2>
                <p id="detTelefono" class="text-indigo-900/60 font-bold tracking-widest"></p>
            </div>
            <div class="p-8 grid grid-cols-2 gap-8 text-left">
                <div>
                   <label class="block text-xs font-bold text-indigo-900/40 uppercase mb-1">DNI</label>
                   <p id="detDni" class="font-bold text-lg"></p>
                </div>
                <div>
                   <label class="block text-xs font-bold text-indigo-900/40 uppercase mb-1">Cumpleaños</label>
                   <p id="detCumple" class="font-bold text-lg"></p>
                </div>
                <div class="col-span-2 bg-gray-50 p-6 rounded-3xl">
                   <h4 class="font-bold text-indigo-900 mb-4 flex items-center">🛍️ ÚLTIMAS VENTAS</h4>
                   <div id="detHistory" class="space-y-3">
                       <!-- AJAX generated -->
                   </div>
                </div>
            </div>
            <div class="p-8 border-t border-gray-100 flex gap-4">
                <button onclick="openEditFromDetail()" class="flex-1 btn-premium">EDITAR FICHA</button>
                <a id="detWaLink" href="#" target="_blank" class="flex-1 btn-premium bg-green-500 hover:bg-green-600 flex items-center justify-center">ENVIAR WHATSAPP</a>
            </div>
        </div>
    </div>

    <!-- Inclusion Modales Reutilizables -->
    <?php include 'partials/modal_nuevo_cliente.php'; ?>
    <?php include 'partials/modal_editar_cliente.php'; ?>

    <script>
        let currentClientId = null;

        function showDetails(id) {
            currentClientId = id;
            const isInsideApps = window.location.pathname.includes('apps/clientes');
            const ajaxPath = isInsideApps ? 'ajax_get_client_card.php' : 'apps/clientes/ajax_get_client_card.php';

            fetch(ajaxPath + '?id=' + id)
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    const c = data.client;
                    document.getElementById('detNombre').innerText = c.Nombre + ' ' + c.Apellido;
                    document.getElementById('detInitials').innerText = (c.Nombre[0] + c.Apellido[0]).toUpperCase();
                    document.getElementById('detTelefono').innerText = c.Telefono;
                    document.getElementById('detDni').innerText = c.Dni || 'No cargado';
                    document.getElementById('detCumple').innerText = c.FechaNacFormat;
                    document.getElementById('detWaLink').href = 'https://wa.me/' + c.Telefono.replace(/[^0-9]/g, '');
                    
                    const histDiv = document.getElementById('detHistory');
                    histDiv.innerHTML = '';
                    if(data.history.length > 0) {
                        data.history.forEach(h => {
                            histDiv.innerHTML += `
                                <div class="flex justify-between items-center bg-white p-3 rounded-2xl shadow-sm">
                                    <span class="font-bold">#${h.id}</span>
                                    <span class="text-xs text-gray-400 font-bold">${h.fecha}</span>
                                    <span class="font-black text-indigo-900">$${h.total}</span>
                                </div>
                            `;
                        });
                    } else {
                        histDiv.innerHTML = '<p class="text-center text-gray-400 italic py-2">Sin historial de compras.</p>';
                    }

                    document.getElementById('modalDetalle').classList.remove('hidden');
                }
            });
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openEditFromDetail() {
            closeModal('modalDetalle');
            openEditClient(currentClientId);
        }


        function exportExcel() {
             Swal.fire('Exportación', 'Generando reporte Excel...', 'success');
        }

        function openImportModal() {
             Swal.fire('Importación', 'Prepara tu archivo CSV con: Nombre, Apellido, Telefono, DNI, FechaNac', 'info');
        }
    </script>
</body>
</html>
