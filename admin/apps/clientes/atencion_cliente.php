<?php
session_start();
if (!isset($_SESSION['userid'])) { header('Location: ../../login.php'); exit; }
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../../includes/utils.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

$mes_actual_num = date('m'); $anio_actual = date('Y');
$hoy_full = date('Y-m-d');
$hoy_dia = date('d');
$hoy_mes = date('m');

// Inicializar variables para prevenir errores fatales
$contactos_hoy = ['cumple' => [], 'habitual' => []];
$cumpleanieros = [];
$clientes_promo = [];
$clientes_frecuentes = [];
$error_db = null;

try {
    $stmt_c = $pdo->query("SELECT IdCliente FROM contactoswhatsapp WHERE Tipo = 'cumple' AND DATE(FechaContacto) = CURDATE()");
    while($row = $stmt_c->fetch()) { $contactos_hoy['cumple'][] = $row['IdCliente']; }
    $stmt_h = $pdo->query("SELECT IdCliente FROM contactoswhatsapp WHERE Tipo = 'habitual' AND MONTH(FechaContacto) = MONTH(CURDATE()) AND YEAR(FechaContacto) = YEAR(CURDATE())");
    while($row = $stmt_h->fetch()) { $contactos_hoy['habitual'][] = $row['IdCliente']; }

    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE MONTH(FechaNac) = ? AND Estado = 1 ORDER BY DAY(FechaNac) ASC");
    $stmt->execute([(int)$mes_actual_num]); $cumpleanieros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt_promos = $pdo->prepare("SELECT * FROM clientes WHERE Promociones = 1 AND Estado = 1"); $stmt_promos->execute(); $clientes_promo = $stmt_promos->fetchAll(PDO::FETCH_ASSOC);

    $stmt_freq = $pdo->prepare("SELECT c.*, COUNT(p.IdPedido) as CantidadCompras FROM pedidos p JOIN clientes c ON p.IdCliente = c.IdCliente WHERE MONTH(p.Fecha) = ? AND YEAR(p.Fecha) = ? GROUP BY c.IdCliente HAVING CantidadCompras >= 3 ORDER BY CantidadCompras DESC");
    $stmt_freq->execute([$mes_actual_num, $anio_actual]); $clientes_frecuentes = $stmt_freq->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) { $error_db = $e->getMessage(); }

$nombre_mes = get_month_name($mes_actual_num);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css?v=5.4">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .btn-wa-disabled { filter: grayscale(1); opacity: 0.3; cursor: not-allowed; }
    </style>
</head>
<body class="p-2 sm:p-4 bg-[#f0fdf4]">
    <div class="max-w-7xl mx-auto">
        <?php if($error_db): ?>
             <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-4 border border-red-100 text-xs">Error de Base de Datos: <?= s($error_db) ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-100 flex flex-col">
            <div class="modal-header-premium mb-0">
                <h1 class="modal-title-premium italic">CRM</h1>
                <button onclick="window.parent.closeAppModal()" class="btn-close-premium" title="Cerrar">&times;</button>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- COLUMNA CUMPLEAÑOS -->
                <div class="flex flex-col bg-gray-100/30 rounded-[2rem] overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 flex justify-between items-center bg-rose-500 text-white font-bold uppercase tracking-widest text-xs">
                        <span>CUMPLEAÑOS <span class="opacity-50 text-[9px]">/ <?= $nombre_mes ?></span></span>
                        <span>🎂</span>
                    </div>
                    <div class="p-3 overflow-y-auto max-h-[500px] flex flex-col gap-3">
                        <?php if(empty($cumpleanieros)): ?><p class="text-center text-xs p-10 text-gray-400">Sin cumpleaños este mes.</p><?php endif; ?>
                        <?php foreach($cumpleanieros as $c): 
                            $c_dia = date('d', strtotime($c['FechaNac']));
                            $c_mes = date('m', strtotime($c['FechaNac']));
                            $es_hoy = ($c_dia == $hoy_dia && $c_mes == $hoy_mes);
                            $ya_contactado_wa = in_array($c['IdCliente'], $contactos_hoy['cumple']);
                            $ya_contactado_promo = in_array($c['IdCliente'], $contactos_hoy['habitual']);
                        ?>
                        <div class="subplaca-adn <?= $ya_contactado_promo ? 'contact-done' : '' ?>">
                            <div class="subplaca-acento bg-rose-400"></div>
                            <div class="subplaca-cuerpo">
                                <div class="subplaca-info">
                                    <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight"><?= s($c['Apellido']) ?> <?= s($c['Nombre']) ?></h3>
                                    <p class="text-[10px] font-black text-rose-500 mt-1 uppercase tracking-wider">📅 <?= $c_dia ?> DE <?= $nombre_mes ?></p>
                                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5"><?= s($c['Telefono']) ?></p>
                                </div>
                                <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                    <button onclick="openEditClient(<?= $c['IdCliente'] ?>)" class="w-8 h-8 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-sm hover:bg-emerald-950 hover:text-white transition-all shadow-sm" title="Editar">✏️</button>
                                    <button id="btn-wa-<?= $c['IdCliente'] ?>" onclick="handleWAClick(this, '<?= get_wa_link($c['Telefono'], '¡Feliz Cumple!') ?>', <?= $c['IdCliente'] ?>, 'cumple', <?= $es_hoy ? 'true' : 'false' ?>, <?= $ya_contactado_wa ? 'true' : 'false' ?>)" 
                                            class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-lg shadow-sm transition-all <?= (!$es_hoy || $ya_contactado_wa) ? 'btn-wa-disabled' : 'hover:scale-110' ?>" title="WhatsApp Cumple">📲</button>
                                    <button onclick="handleCheckClick(this, <?= $c['IdCliente'] ?>, 'habitual')" 
                                            class="w-8 h-8 rounded-full border flex items-center justify-center transition-colors <?= $ya_contactado_promo ? 'bg-rose-100 text-rose-600 border-rose-200' : 'bg-white text-gray-200 border-gray-100' ?>" title="Promo mes">✓</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMNA HABITUALES -->
                <div class="flex flex-col bg-gray-100/30 rounded-[2rem] overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 flex justify-between items-center bg-amber-400 text-white font-bold uppercase tracking-widest text-xs">
                        <span>HABITUALES</span>
                        <span>⭐</span>
                    </div>
                    <div class="p-3 overflow-y-auto max-h-[500px] flex flex-col gap-3">
                        <?php if(empty($clientes_frecuentes)): ?><p class="text-center text-xs p-10 text-gray-400">Sin compras frecuentes este mes.</p><?php endif; ?>
                        <?php foreach($clientes_frecuentes as $cf): 
                            $ya_promo = in_array($cf['IdCliente'], $contactos_hoy['habitual']);
                        ?>
                        <div class="subplaca-adn <?= $ya_promo ? 'contact-done' : '' ?>">
                            <div class="subplaca-acento bg-amber-400"></div>
                            <div class="subplaca-cuerpo">
                                <div class="subplaca-info">
                                    <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight"><?= s($cf['Apellido']) ?> <?= s($cf['Nombre']) ?></h3>
                                    <span class="text-[9px] bg-amber-500 text-white px-2 py-0.5 rounded-full font-black mt-1 uppercase inline-block self-start"><?= $cf['CantidadCompras'] ?> COMPRAS</span>
                                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5"><?= s($cf['Telefono']) ?></p>
                                </div>
                                <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                    <button onclick="openEditClient(<?= $cf['IdCliente'] ?>)" class="w-8 h-8 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-sm hover:bg-emerald-950 hover:text-white transition-all shadow-sm">✏️</button>
                                    <button id="btn-wa-habitual-<?= $cf['IdCliente'] ?>" onclick="handleWAClick(this, '<?= get_wa_link($cf['Telefono'], '¡Hola!') ?>', <?= $cf['IdCliente'] ?>, 'habitual', true, <?= $ya_promo ? 'true' : 'false' ?>)" class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-lg shadow-sm <?= $ya_promo ? 'btn-wa-disabled' : 'hover:scale-110' ?>" title="WhatsApp Habitual">💬</button>
                                    <button onclick="handleCheckClick(this, <?= $cf['IdCliente'] ?>, 'habitual')" 
                                            class="w-8 h-8 rounded-full border flex items-center justify-center transition-colors <?= $ya_promo ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-white text-gray-200 border-gray-100' ?>" title="Promo habitual">✓</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMNA DIFUSIÓN -->
                <div class="flex flex-col bg-gray-100/30 rounded-[2rem] overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 flex justify-between items-center bg-emerald-500 text-white font-bold uppercase tracking-widest text-xs">
                        <span>DIFUSIÓN</span>
                        <span>📢</span>
                    </div>
                    <div class="p-5 flex-1 bg-white flex flex-col gap-4">
                        <textarea readonly id="promoDisplay" class="w-full flex-1 bg-gray-50/50 rounded-2xl p-4 border-none text-[10px] resize-none focus:ring-0"><?php foreach($clientes_promo as $cp) { echo s($cp['Nombre']." ".$cp['Apellido']." - ".$cp['Telefono'])."\n"; } ?></textarea>
                        <button onclick="copyPromos()" class="btn-premium w-full !rounded-xl py-3">COPIAR LISTA</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php include 'partials/modal_editar_cliente.php'; ?>

    <script>
        async function handleWAClick(btn, url, id, tipo, esHoy, yaEnviado) {
            // Cumple: Solo HOY. Habitual: Siempre (es mensual)
            if (tipo === 'cumple' && !esHoy) { Swal.fire({ icon: 'info', title: 'Aún no es su cumpleaños', text: 'El saludo solo puede enviarse el mismo día.', confirmButtonText: 'Aceptar', confirmButtonColor: '#00a876' }); return; }
            
            // Si ya fue enviado, preguntar si se desea reenviar
            const isDone = btn.classList.contains('btn-wa-disabled');
            if (isDone) {
                const periodText = (tipo === 'habitual') ? 'este mes' : 'hoy';
                const res = await Swal.fire({
                    title: '¿Reenviar contacto?',
                    text: `Parece que ya has contactado este cliente ${periodText}. ¿Quieres enviarlo de nuevo?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, reenviar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#00a876'
                });
                if (!res.isConfirmed) return;
            }

            window.open(url, '_blank');
            btn.classList.add('btn-wa-disabled');
            
            // Marcar en la BD si no estaba marcado
            if (!isDone) {
                const fd = new FormData(); fd.append('id', id); fd.append('tipo', tipo);
                const response = await fetch('ajax_mark_contacted.php', { method: 'POST', body: fd });
                const data = await response.json();
                
                // Si es habitual, sincronizar visualmente el botón de tilde
                if (data.success && data.marked) {
                    const card = btn.closest('.subplaca-adn');
                    const checkBtn = card.querySelector('button[onclick*="handleCheckClick"]');
                    if (checkBtn) {
                        const col = tipo === 'habitual' ? 'amber' : 'rose';
                        checkBtn.classList.remove('bg-white', 'text-gray-200', 'border-gray-100');
                        checkBtn.classList.add('bg-'+col+'-100', 'text-'+col+'-600', 'border-'+col+'-200');
                        card.classList.add('contact-done');
                    }
                }
            }
        }

        async function handleCheckClick(btn, id, tipo) {
            const fd = new FormData(); fd.append('id', id); fd.append('tipo', tipo);
            const response = await fetch('ajax_mark_contacted.php', { method: 'POST', body: fd });
            const data = await response.json();
            
            if (data.success) {
                const card = btn.closest('.subplaca-adn');
                const col = (tipo === 'cumple' || tipo === 'habitual') ? (card.querySelector('.subplaca-acento').classList.contains('bg-rose-400') ? 'rose' : 'amber') : 'emerald';
                
                if (data.marked) {
                    btn.classList.remove('bg-white', 'text-gray-200', 'border-gray-100');
                    btn.classList.add('bg-'+col+'-100', 'text-'+col+'-600', 'border-'+col+'-200');
                    card.classList.add('contact-done');
                } else {
                    btn.classList.add('bg-white', 'text-gray-200', 'border-gray-100');
                    btn.classList.remove('bg-'+col+'-100', 'text-'+col+'-600', 'border-'+col+'-200');
                    card.classList.remove('contact-done');
                    
                    // Si desmarcamos, habilitar el botón de WA de nuevo
                    const waBtn = card.querySelector('.btn-wa-disabled');
                    if (waBtn) waBtn.classList.remove('btn-wa-disabled', 'animate-pulse');
                }
                
                Swal.fire({ 
                    toast: true, 
                    position: 'top-end', 
                    icon: 'success', 
                    title: data.marked ? 'Marcado' : 'Desmarcado', 
                    showConfirmButton: false, 
                    timer: 1500 
                });
            }
        }

        function copyPromos() { navigator.clipboard.writeText(document.getElementById('promoDisplay').value).then(() => { Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Copiado', showConfirmButton: false, timer: 1500 }); }); }
    </script>
</body>
</html>
