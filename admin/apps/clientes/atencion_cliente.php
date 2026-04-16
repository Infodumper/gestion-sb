<?php
session_start();
if (!isset($_SESSION['userid']) && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') { 
    header('Location: ../../login.php'); 
    exit; 
}
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

            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- COLUMNA CUMPLEAÑOS -->
                <div class="flex flex-col bg-white rounded-[2.5rem] shadow-xl border border-rose-100 overflow-hidden">
                    <div class="px-8 py-6 flex justify-between items-center bg-rose-500 text-white shadow-lg">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-[0.2em] font-black opacity-60 mb-0.5">Atención Premium</span>
                            <h2 class="font-bold text-xl tracking-tight">CUMPLEAÑOS DE <?= $nombre_mes ?></h2>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-8 0h4m-4 5a2 2 0 112 2h-2zm0 0h4m-8 0h4"></path></svg>
                        </div>
                    </div>
                    <div class="p-3 overflow-y-auto max-h-[500px] flex flex-col gap-3">
                        <?php if(empty($cumpleanieros)): ?><p class="text-center text-xs p-10 text-gray-400">Sin cumpleaños este mes.</p><?php endi                        <?php foreach($cumpleanieros as $c): 
                            $c_dia = date('d', strtotime($c['FechaNac']));
                            $c_mes = date('m', strtotime($c['FechaNac']));
                            $es_hoy = ($c_dia == $hoy_dia && $c_mes == $hoy_mes);
                            $ya_contactado_wa = in_array($c['IdCliente'], $contactos_hoy['cumple']);
                            $ya_contactado_promo = in_array($c['IdCliente'], $contactos_hoy['habitual']);
                            $initials = strtoupper(substr($c['Nombre'], 0, 1) . substr($c['Apellido'], 0, 1));
                            $avatar_gradient = get_gradient_avatar($c['IdCliente']);
                        ?>
                        <div class="subplaca-adn group <?= $ya_contactado_promo ? 'contact-done' : '' ?> <?= $es_hoy ? '!border-rose-200 ring-4 ring-rose-500/5' : '' ?>">
                            <div class="subplaca-acento bg-rose-400"></div>
                            <div class="subplaca-cuerpo !p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $avatar_gradient ?> flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        <?= $initials ?>
                                    </div>
                                    <div class="subplaca-info">
                                        <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight"><?= s($c['Apellido']) ?> <?= s($c['Nombre']) ?></h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest"><?= $c_dia ?> DE <?= $nombre_mes ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                    <button onclick="openEditClient(<?= $c['IdCliente'] ?>)" class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100/50" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button id="btn-wa-<?= $c['IdCliente'] ?>" onclick="handleWAClick(this, '<?= get_wa_link($c['Telefono'], '¡Feliz Cumple!') ?>', <?= $c['IdCliente'] ?>, 'cumple', <?= $es_hoy ? 'true' : 'false' ?>, <?= $ya_contactado_wa ? 'true' : 'false' ?>)" 
                                            class="w-9 h-9 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20 transition-all <?= (!$es_hoy || $ya_contactado_wa) ? 'btn-wa-disabled' : 'hover:scale-110 active:scale-95' ?>" title="WhatsApp Cumple">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.52.909 3.125 1.388 4.773 1.389 5.233.002 9.491-4.258 9.493-9.492.001-2.533-.986-4.915-2.778-6.708s-4.177-2.779-6.709-2.78c-5.235 0-9.492 4.258-9.493 9.493-.001 1.761.488 3.476 1.415 4.974l-1.08 3.946 4.079-1.071zm9.178-6.035c-.255-.127-1.503-.734-1.737-.82-.233-.086-.403-.127-.573.127s-.657.82-.805.99c-.148.17-.297.191-.553.064-1.831-.916-2.825-1.526-3.951-3.456-.255-.436.255-.404.729-1.353.078-.159.039-.297-.021-.423-.06-.126-.573-1.38-.785-1.889-.208-.499-.42-.43-.573-.438-.148-.007-.318-.008-.488-.008s-.446.063-.679.297c-.234.233-.892.871-.892 2.122 0 1.25.912 2.46 1.039 2.63.127.17 1.794 2.738 4.346 3.84.607.262 1.08.419 1.448.536.611.194 1.167.166 1.607.101.491-.072 1.503-.615 1.714-1.209.211-.595.211-1.104.148-1.209-.063-.105-.233-.148-.488-.275z"/></svg>
                                    </button>
                                    <button onclick="handleCheckClick(this, <?= $c['IdCliente'] ?>, 'habitual')" 
                                            class="w-9 h-9 rounded-2xl border flex items-center justify-center transition-all <?= $ya_contactado_promo ? 'bg-rose-100 text-rose-600 border-rose-200' : 'bg-white text-gray-200 border-gray-100' ?>" title="Promo mes">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMNA HABITUALES -->
                <div class="flex flex-col bg-white rounded-[2.5rem] shadow-xl border border-amber-100 overflow-hidden">
                    <div class="px-8 py-6 flex justify-between items-center bg-amber-400 text-white shadow-lg">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-[0.2em] font-black opacity-60 mb-0.5">Fidelización</span>
                            <h2 class="font-bold text-xl tracking-tight">CLIENTES HABITUALES</h2>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                    </div>
                    <div class="p-3 overflow-y-auto max-h-[500px] flex flex-col gap-3">
                        <?php if(empty($clientes_frecuentes)): ?><p class="text-center text-xs p-10 text-gray-400">Sin compras frecuentes este mes.</p><?php endif; ?>
                        <?php foreach($clientes_frecuentes as $cf): 
                            $ya_promo = in_array($cf['IdCliente'], $contactos_hoy['habitual']);
                            $initials = strtoupper(substr($cf['Nombre'], 0, 1) . substr($cf['Apellido'], 0, 1));
                            $avatar_gradient = get_gradient_avatar($cf['IdCliente']);
                        ?>
                        <div class="subplaca-adn group <?= $ya_promo ? 'contact-done' : '' ?>">
                            <div class="subplaca-acento bg-amber-400"></div>
                            <div class="subplaca-cuerpo !p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $avatar_gradient ?> flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        <?= $initials ?>
                                    </div>
                                    <div class="subplaca-info text-left">
                                        <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight"><?= s($cf['Apellido']) ?> <?= s($cf['Nombre']) ?></h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-lg font-black uppercase"><?= $cf['CantidadCompras'] ?> COMPRAS</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                    <button onclick="openEditClient(<?= $cf['IdCliente'] ?>)" class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100/50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button id="btn-wa-habitual-<?= $cf['IdCliente'] ?>" onclick="handleWAClick(this, '<?= get_wa_link($cf['Telefono'], '¡Hola!') ?>', <?= $cf['IdCliente'] ?>, 'habitual', true, <?= $ya_promo ? 'true' : 'false' ?>)" 
                                            class="w-9 h-9 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20 transition-all <?= $ya_promo ? 'btn-wa-disabled' : 'hover:scale-110 active:scale-95' ?>" title="WhatsApp Habitual">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.52.909 3.125 1.388 4.773 1.389 5.233.002 9.491-4.258 9.493-9.492.001-2.533-.986-4.915-2.778-6.708s-4.177-2.779-6.709-2.78c-5.235 0-9.492 4.258-9.493 9.493-.001 1.761.488 3.476 1.415 4.974l-1.08 3.946 4.079-1.071zm9.178-6.035c-.255-.127-1.503-.734-1.737-.82-.233-.086-.403-.127-.573.127s-.657.82-.805.99c-.148.17-.297.191-.553.064-1.831-.916-2.825-1.526-3.951-3.456-.255-.436.255-.404.729-1.353.078-.159.039-.297-.021-.423-.06-.126-.573-1.38-.785-1.889-.208-.499-.42-.43-.573-.438-.148-.007-.318-.008-.488-.008s-.446.063-.679.297c-.234.233-.892.871-.892 2.122 0 1.25.912 2.46 1.039 2.63.127.17 1.794 2.738 4.346 3.84.607.262 1.08.419 1.448.536.611.194 1.167.166 1.607.101.491-.072 1.503-.615 1.714-1.209.211-.595.211-1.104.148-1.209-.063-.105-.233-.148-.488-.275z"/></svg>
                                    </button>
                                    <button onclick="handleCheckClick(this, <?= $cf['IdCliente'] ?>, 'habitual')" 
                                            class="w-9 h-9 rounded-2xl border flex items-center justify-center transition-all <?= $ya_promo ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-white text-gray-200 border-gray-100' ?>" title="Promo habitual">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMNA DIFUSIÓN (OCULTA A PEDIDO)
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
                -->
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
