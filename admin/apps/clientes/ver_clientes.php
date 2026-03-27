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
    return "<a href=\"$url\" class=\"inline-flex items-center hover:text-emerald-500 transition-colors uppercase tracking-wider\">$label<span class='ml-1 text-[10px]'>$icon</span></a>";
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
    <link rel="stylesheet" href="../../../styles/main.css?v=2.0">
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
        .is-iframe .back-panel-link, .is-iframe .header-original-clientes { display: none !important; }
        .is-iframe { background-color: white !important; }
        .is-iframe body { padding-top: 0 !important; padding-bottom: 0 !important; overflow-x: hidden; }
        .is-iframe .app-header-premium { display: block !important; }
        .app-header-premium { display: none; } /* Solo visible en iframe mode */
    </style>

    <div class="max-w-7xl mx-auto">
        <!-- MASTER CHAPITA: Directorio de Clientes -->
        <div class="master-chapita bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-100 flex flex-col mb-10 animate-in fade-in duration-300">
            
            <!-- Cabecera Maestra Compacta -->
            <div class="px-8 py-5 border-b border-emerald-50 flex justify-between items-center bg-gradient-to-r from-emerald-50 to-white">
                <div class="flex items-center gap-4">
                    <span class="text-3xl">👥</span>
                    <div>
                        <h1 class="brand-title text-3xl text-emerald-950 italic leading-none">Clientes</h1>
                        <p class="text-[9px] font-black text-emerald-900/30 uppercase tracking-[0.2em] mt-1">Directorio de Contactos</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openClientModal()" class="w-10 h-10 bg-emerald-700 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 active:scale-95" title="Nuevo Cliente">➕</button>
                    <button onclick="window.parent.closeAppModal()" class="w-10 h-10 bg-emerald-950 text-white rounded-full flex items-center justify-center text-2xl font-light hover:bg-emerald-700 transition-all shadow-lg">&times;</button>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="p-4 sm:p-8">
                <!-- Barra de Búsqueda Premium -->
                <div class="search-container-premium !mb-8 relative max-w-2xl mx-auto">
                    <form method="GET" class="flex items-center">
                        <input type="text" name="filtro" value="<?= htmlspecialchars($filtro) ?>" 
                               class="search-input-premium w-full !py-4 !pl-12 !rounded-[2rem] shadow-sm focus:shadow-md" 
                               placeholder="Buscar por nombre, apellido, DNI...">
                        <span class="absolute left-4 text-xl opacity-30">🔍</span>
                        <?php if(!empty($filtro)): ?>
                            <a href="?" class="absolute right-4 text-gray-300 hover:text-red-500 text-2xl">&times;</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Listado en Subplacas (Regla de las Subplacas) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php if (empty($clientes)): ?>
                        <div class="col-span-full py-20 text-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 italic text-lg">No se hallaron clientes.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($clientes as $c): ?>
                        <div class="flex flex-col bg-white border border-gray-100 rounded-[1.8rem] shadow-sm hover:shadow-md transition-all group overflow-hidden" onclick="showDetails(<?= $c['IdCliente'] ?>)">
                            <div class="p-5 flex-1 cursor-pointer">
                                <!-- Cabecera de Subplaca: Nombre y WA -->
                                <div class="flex justify-between items-start mb-2">
                                    <div class="min-w-0 pr-3">
                                        <h3 class="font-black text-[1rem] text-emerald-950 truncate leading-tight">
                                            <?= htmlspecialchars($c['Apellido']) ?> <?= htmlspecialchars($c['Nombre']) ?>
                                        </h3>
                                        <p class="text-[10px] text-emerald-500 font-bold tracking-widest mt-0.5">
                                            <?= htmlspecialchars($c['Telefono']) ?>
                                        </p>
                                    </div>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$c['Telefono']) ?>" target="_blank" onclick="event.stopPropagation();" 
                                       class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all">
                                       <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.52.909 3.125 1.388 4.773 1.389 5.233.002 9.491-4.258 9.493-9.492.001-2.533-.986-4.915-2.778-6.708s-4.177-2.779-6.709-2.78c-5.235 0-9.492 4.258-9.493 9.493-.001 1.761.488 3.476 1.415 4.974l-1.08 3.946 4.079-1.071zm9.178-6.035c-.255-.127-1.503-.734-1.737-.82-.233-.086-.403-.127-.573.127s-.657.82-.805.99c-.148.17-.297.191-.553.064-1.831-.916-2.825-1.526-3.951-3.456-.255-.436.255-.404.729-1.353.078-.159.039-.297-.021-.423-.06-.126-.573-1.38-.785-1.889-.208-.499-.42-.43-.573-.438-.148-.007-.318-.008-.488-.008s-.446.063-.679.297c-.234.233-.892.871-.892 2.122 0 1.25.912 2.46 1.039 2.63.127.17 1.794 2.738 4.346 3.84.607.262 1.08.419 1.448.536.611.194 1.167.166 1.607.101.491-.072 1.503-.615 1.714-1.209.211-.595.211-1.104.148-1.209-.063-.105-.233-.148-.488-.275z"/></svg>
                                    </a>
                                </div>
                                
                                <!-- Detalle Inferior Compacto -->
                                <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-50">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-gray-300">
                                        <?= (int)$c['Dni'] > 0 ? 'DNI: '.s($c['Dni']) : 'Sin DNI' ?>
                                    </span>
                                    <button onclick="event.stopPropagation(); openEditClient(<?= $c['IdCliente'] ?>)" 
                                            class="w-8 h-8 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center hover:bg-emerald-700 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                </div>
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
                       class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center rounded-2xl font-bold transition-all <?= ($i == $page) ? 'bg-emerald-700 text-white shadow-lg scale-110' : 'bg-gray-50 text-emerald-900 hover:bg-emerald-50 shadow-sm' ?>">
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
    <div id="modalDetalle" class="hidden fixed inset-0 z-[110] bg-emerald-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="max-w-2xl w-full card-premium overflow-hidden animate-in fade-in zoom-in duration-300">
             <div class="bg-emerald-50 px-8 py-10 border-b border-indigo-100 text-center relative">
                <button type="button" onclick="closeModal('modalDetalle')" class="btn-close-premium" title="Cerrar">&times;</button>
                <div id="detInitials" class="w-20 h-20 bg-emerald-950 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 border-4 border-white shadow-xl"></div>
                <h2 id="detNombre" class="brand-title text-4xl mb-2 text-emerald-950"></h2>
                <p id="detTelefono" class="text-emerald-950/60 font-bold tracking-widest"></p>
            </div>
            <div class="p-8 grid grid-cols-2 gap-8 text-left">
                <div>
                   <label class="block text-xs font-bold text-emerald-950/40 uppercase mb-1">DNI</label>
                   <p id="detDni" class="font-bold text-lg"></p>
                </div>
                <div>
                   <label class="block text-xs font-bold text-emerald-950/40 uppercase mb-1">Cumpleaños</label>
                   <p id="detCumple" class="font-bold text-lg"></p>
                </div>
                <div class="col-span-2 bg-gray-50 p-6 rounded-3xl">
                   <h4 class="font-bold text-emerald-950 mb-4 flex items-center">🛍️ ÚLTIMAS VENTAS</h4>
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
                                    <span class="font-black text-emerald-950">$${h.total}</span>
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
