<?php
session_start();
if (!isset($_SESSION['userid'])) { header('Location: ../../login.php'); exit; }
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
require_once '../../../includes/utils.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

$mes_actual_num = date('m'); $anio_actual = date('Y');

try {
    $contactos_hoy = ['cumple' => [], 'habitual' => []];
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
    <link rel="stylesheet" href="../../../styles/main.css?v=5.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="p-4 sm:p-10 bg-[#f0fdf4]">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-100 flex flex-col">
            <div class="px-8 py-5 border-b border-emerald-50 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">💎</span>
                    <h1 class="brand-title text-32xl text-emerald-950 italic">CRM</h1>
                </div>
                <button onclick="window.parent.closeAppModal()" class="w-10 h-10 bg-emerald-950 text-white rounded-full flex items-center justify-center text-xl">&times;</button>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- COLUMNA CUMPLEAÑOS -->
                <div class="flex flex-col bg-gray-100/30 rounded-[2rem] overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 flex justify-between items-center bg-rose-500 text-white font-bold uppercase tracking-widest text-xs">
                        <span>CUMPLEAÑOS <span class="opacity-50 text-[9px]">/ <?= $nombre_mes ?></span></span>
                        <span>🎂</span>
                    </div>
                    <div class="p-3 overflow-y-auto max-h-[500px] flex flex-col gap-3">
                        <?php foreach($cumpleanieros as $c): 
                            $dia = date('d', strtotime($c['FechaNac']));
                            $ya_contactado = in_array($c['IdCliente'], $contactos_hoy['cumple']);
                        ?>
                        <div class="subplaca-adn <?= $ya_contactado ? 'contact-done' : '' ?>">
                            <div class="subplaca-acento bg-rose-400"></div>
                            <div class="subplaca-cuerpo">
                                <div class="subplaca-info">
                                    <h3 class="font-bold text-emerald-950 text-sm leading-tight"><?= s($c['Apellido']) ?> <?= s($c['Nombre']) ?></h3>
                                    <p class="text-[11px] text-emerald-900/40 mt-1"><?= s($c['Telefono']) ?></p>
                                    <p class="text-[9px] font-bold text-rose-500 mt-2 uppercase">GOLOSINA: <?= $dia ?> DE <?= $nombre_mes ?></p>
                                </div>
                                <div class="subplaca-acciones">
                                    <button onclick="openWAPopup('<?= get_wa_link($c['Telefono'], '¡Feliz Cumple!') ?>', <?= $c['IdCliente'] ?>, 'cumple', this)" class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-lg">📲</button>
                                    <button onclick="toggleContact(<?= $c['IdCliente'] ?>, 'cumple', this)" class="w-8 h-8 rounded-full border flex items-center justify-center <?= $ya_contactado ? 'bg-rose-100 text-rose-600' : 'bg-white text-gray-200' ?>">✓</button>
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
                        <?php foreach($clientes_frecuentes as $cf): 
                            $ya_contactado_h = in_array($cf['IdCliente'], $contactos_hoy['habitual']);
                        ?>
                        <div class="subplaca-adn <?= $ya_contactado_h ? 'contact-done' : '' ?>">
                            <div class="subplaca-acento bg-amber-400"></div>
                            <div class="subplaca-cuerpo">
                                <div class="subplaca-info">
                                    <h3 class="font-bold text-emerald-950 text-sm leading-tight"><?= s($cf['Apellido']) ?> <?= s($cf['Nombre']) ?></h3>
                                    <p class="text-[11px] text-emerald-900/40 mt-1"><?= s($cf['Telefono']) ?></p>
                                    <span class="text-[8px] bg-amber-500 text-white px-2 py-0.5 rounded-full font-bold mt-2 uppercase inline-block"><?= $cf['CantidadCompras'] ?> COMPRAS</span>
                                </div>
                                <div class="subplaca-acciones">
                                    <button onclick="openWAPopup('<?= get_wa_link($cf['Telefono'], '¡Hola!') ?>', <?= $cf['IdCliente'] ?>, 'habitual', this)" class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-lg">💬</button>
                                    <button onclick="toggleContact(<?= $cf['IdCliente'] ?>, 'habitual', this)" class="w-8 h-8 rounded-full border flex items-center justify-center <?= $ya_contactado_h ? 'bg-amber-100 text-amber-600' : 'bg-white text-gray-200' ?>">✓</button>
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

    <script>
        function copyPromos() { navigator.clipboard.writeText(document.getElementById('promoDisplay').value).then(() => { Swal.fire('¡Copiado!', '', 'success'); }); }
        function openWAPopup(url, id, tipo, btn) { window.open(url, '_blank'); toggleContact(id, tipo, btn.closest('.subplaca-adn').querySelector('button[onclick*="toggleContact"]')); }
        async function toggleContact(id, tipo, btn) {
            const isY = btn.classList.contains('bg-rose-100') || btn.classList.contains('bg-amber-100');
            const pod = btn.closest('.subplaca-adn'); const col = (tipo==='cumple')?'rose':'amber';
            if(isY) { btn.classList.remove('bg-'+col+'-100', 'text-'+col+'-600'); btn.classList.add('bg-white', 'text-gray-200'); pod.classList.remove('contact-done'); }
            else { btn.classList.add('bg-'+col+'-100', 'text-'+col+'-600'); btn.classList.remove('bg-white', 'text-gray-200'); pod.classList.add('contact-done'); }
            const fd = new FormData(); fd.append('id', id); fd.append('tipo', tipo);
            await fetch('ajax_mark_contacted.php', { method: 'POST', body: fd });
        }
    </script>
</body>
</html>
