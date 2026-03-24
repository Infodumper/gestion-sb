<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
require_once '../../../includes/utils.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

$mes_actual_num = date('m');
$mes_actual_str = date('F'); 
$anio_actual = date('Y');

try {
    // 4. Contactos realizados recientemente (Log de WhatsApp)
    $contactados_hoy = ['cumple' => [], 'habitual' => []];
    
    // Cumpleaños contactados HOY
    $stmt_c = $pdo->query("SELECT IdCliente FROM contactoswhatsapp WHERE Tipo = 'cumple' AND DATE(FechaContacto) = CURDATE()");
    while($row = $stmt_c->fetch()) { $contactados_hoy['cumple'][] = $row['IdCliente']; }
    
    // Habituales contactados ESTE MES
    $stmt_h = $pdo->query("SELECT IdCliente FROM contactoswhatsapp WHERE Tipo = 'habitual' AND MONTH(FechaContacto) = MONTH(CURDATE()) AND YEAR(FechaContacto) = YEAR(CURDATE())");
    while($row = $stmt_h->fetch()) { $contactados_hoy['habitual'][] = $row['IdCliente']; }

    // 1. Cumpleaños del Mes
    // Convertir mes a entero para asegurar coincidencia perfecta en MySQL
    $mes_int = (int)$mes_actual_num;
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE MONTH(FechaNac) = ? AND Estado = 1 ORDER BY DAY(FechaNac) ASC");
    $stmt->execute([$mes_int]);
    $cumpleanieros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sort: Today NOT CONTACTED first, then Chronological
    usort($cumpleanieros, function($a, $b) use ($contactados_hoy) {
        $today = date('d');
        $dayA = date('d', strtotime($a['FechaNac']));
        $dayB = date('d', strtotime($b['FechaNac']));
        
        $contactedA = in_array($a['IdCliente'], $contactados_hoy['cumple']);
        $contactedB = in_array($b['IdCliente'], $contactados_hoy['cumple']);
        
        $isTodayA = ($dayA == $today);
        $isTodayB = ($dayB == $today);

        // Prioridad 1: Es hoy y NO está contactado
        $prioA = ($isTodayA && !$contactedA) ? 1 : 0;
        $prioB = ($isTodayB && !$contactedB) ? 1 : 0;

        if ($prioA !== $prioB) return $prioB - $prioA;
        
        // Prioridad 2: Orden cronológico por día
        return $dayA - $dayB;
    });

    // 2. Clientes para Difusión (Promociones)
    $stmt_promos = $pdo->prepare("SELECT * FROM clientes WHERE Promociones = 1 AND Estado = 1");
    $stmt_promos->execute();
    $clientes_promo = $stmt_promos->fetchAll(PDO::FETCH_ASSOC);
    $total_promos = count($clientes_promo);

    // 3. Clientes Frecuentes (Habituales) - 3 o más pedidos este mes
    $stmt_freq = $pdo->prepare("
        SELECT c.*, COUNT(p.IdPedido) as CantidadCompras, MAX(p.Fecha) as UltimaCompra
        FROM pedidos p
        JOIN clientes c ON p.IdCliente = c.IdCliente
        WHERE MONTH(p.Fecha) = ? AND YEAR(p.Fecha) = ?
        GROUP BY c.IdCliente
        HAVING CantidadCompras >= 3
        ORDER BY CantidadCompras DESC
    ");
    $stmt_freq->execute([$mes_actual_num, $anio_actual]);
    $clientes_frecuentes = $stmt_freq->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = $e->getMessage();
}

function value_in_array($val, $arr) {
    return is_array($arr) && in_array($val, $arr);
}

$nombre_mes = get_month_name($mes_actual_num);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Stefy Barroso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css?v=1.2">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .is-iframe .back-panel-link { display: none !important; }
        .is-iframe { background-color: white !important; }
        .is-iframe body { padding-top: 1rem !important; padding-bottom: 1rem !important; }
        .is-iframe .max-w-7xl { padding-top: 0 !important; }
        
        .contact-done { opacity: 0.4; filter: grayscale(1); transition: all 0.3s; }
        .contact-today { border-left: 6px solid #fb923c !important; background-color: #fffaf0 !important; }
        .card-premium { min-height: 520px; }
    </style>
</head>
<body class="min-h-screen py-8 px-4 sm:px-10 bg-[#f0fdf4]">
    <script>if (window.self !== window.top) document.body.classList.add('is-iframe');</script>

    <div class="max-w-7xl mx-auto relative">
        <!-- Debug Info (Solo visible si hay error o para técnicos) -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="bg-black text-lime-400 p-4 mb-6 rounded-lg font-mono text-xs overflow-auto">
            MES ACTUAL: <?= $mes_actual_num ?> (Entero: <?= $mes_int ?>)<br>
            CUMPLEAÑEROS ENCONTRADOS: <?= count($cumpleanieros) ?><br>
            <?php
            $chk = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
            $chk_nac = $pdo->query("SELECT COUNT(*) FROM clientes WHERE FechaNac IS NOT NULL")->fetchColumn();
            ?>
            TOTAL CLIENTES: <?= $chk ?><br>
            CLIENTES CON FECHA NAC: <?= $chk_nac ?><br>
            SQL: SELECT * FROM clientes WHERE MONTH(FechaNac) = <?= $mes_int ?>
        </div>
        <?php endif; ?>

        <!-- Botón de Cerrar Modal (X) -->
        <button onclick="parent.closeAppModal()" class="fixed top-4 right-4 z-[120] w-12 h-12 bg-white/80 backdrop-blur-md text-emerald-900 rounded-full flex items-center justify-center text-3xl shadow-xl hover:bg-red-500 hover:text-white transition-all scale-75 sm:scale-100" title="Cerrar CRM">
            &times;
        </button>

        <!-- Header -->
        <div class="mb-10 text-center">
            <a href="../../index.php" class="back-panel-link text-emerald-900 font-bold hover:text-emerald-500 transition-colors uppercase tracking-widest text-xs flex items-center justify-center mb-4">
                🏠 REGRESAR AL MENÚ
            </a>
            <h1 class="brand-title text-5xl">CRM</h1>
            <p class="text-emerald-900/40 font-bold uppercase tracking-[0.3em] text-sm mt-2"><?= $nombre_mes ?> <?= $anio_actual ?><a href="?debug=1" class="opacity-0">.</a></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- TARJETA: CUMPLEAÑOS -->
            <div class="card-premium h-full flex flex-col bg-white rounded-[2.5rem] shadow-xl overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-semantic-cumple">
                    <div>
                        <h2 class="text-2xl font-black leading-tight text-white uppercase tracking-tight">CUMPLEAÑOS</h2>
                    </div>
                    <span class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl">🎂</span>
                </div>
                <div class="p-6 flex-1 overflow-y-auto max-h-[500px] space-y-4">
                    <?php if(empty($cumpleanieros)): ?>
                        <p class="text-center text-gray-400 italic py-10">No hay cumpleaños este mes.</p>
                    <?php else: ?>
                        <?php foreach($cumpleanieros as $c): 
                            $dia = date('d', strtotime($c['FechaNac']));
                            $is_today = ($dia == date('d'));
                            $ya_contactado = value_in_array($c['IdCliente'], $contactos_hoy['cumple']);
                            $phone_clean = preg_replace('/[^0-9]/','',$c['Telefono']);
                        ?>
                        <div id="cumple-<?= $c['IdCliente'] ?>" class="flex items-center justify-between p-4 rounded-3xl border border-gray-100 transition-all hover:shadow-md <?= $ya_contactado ? 'contact-done' : '' ?> <?= $is_today ? 'contact-today' : 'bg-gray-50' ?>">
                            <div>
                                <h3 onclick="openClientCard(<?= $c['IdCliente'] ?>)" class="font-bold text-emerald-950 cursor-pointer hover:underline leading-tight mb-0.5"><?= s($c['Nombre']) ?></h3>
                                <p class="text-sm text-emerald-950/60 font-medium leading-none mb-2"><?= s($c['Apellido']) ?></p>
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest"><?= $dia ?> de <?= $nombre_mes ?></p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="toggleContact(<?= $c['IdCliente'] ?>, 'cumple', this)" class="w-10 h-10 <?= $ya_contactado ? 'text-green-500' : 'text-gray-300' ?> hover:bg-white rounded-xl flex items-center justify-center transition-all" title="Marcar contacto">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </button>
                                <?php 
                                    $msg = "¡Hola ".s($c['Nombre'])."! Paso a saludarte porque hoy es tu cumple. 🥳 ¡Espero que tengas un día hermoso y lo disfrutes muchísimo! Te mando un beso grande, Stefy. 🎂✨";
                                    $waLink = get_wa_link($c['Telefono'], $msg);
                                    
                                    if ($is_today) {
                                        $onWA = "openWAPopup('$waLink', ".$c['IdCliente'].", 'cumple', this)";
                                        $btnClass = "bg-orange-500 text-white shadow-lg shadow-orange-200";
                                        $titleWA = "Enviar Saludo Personalizado";
                                    } else {
                                        $onWA = "Swal.fire('Atención', 'Solo se puede saludar en la fecha exacta ($dia de $nombre_mes)', 'info')";
                                        $btnClass = "bg-gray-200 text-gray-400 cursor-not-allowed";
                                        $titleWA = "Solo disponible el día de su cumple";
                                    }
                                ?>
                                <button onclick="<?= $onWA ?>" class="w-10 h-10 <?= $btnClass ?> rounded-xl flex items-center justify-center hover:scale-110 transition-all font-bold" title="<?= $titleWA ?>">
                                    📱
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TARJETA: HABITUALES -->
            <div class="card-premium h-full flex flex-col bg-white rounded-[2.5rem] shadow-xl overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-semantic-habitual">
                    <div>
                        <h2 class="text-2xl font-black leading-tight text-white uppercase tracking-tight">HABITUALES</h2>
                    </div>
                    <span class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl">⭐</span>
                </div>
                <div class="p-6 flex-1 overflow-y-auto max-h-[500px] space-y-4">
                    <?php if(empty($clientes_frecuentes)): ?>
                        <p class="text-center text-gray-400 italic py-10">Sin clientes frecuentes registrados.</p>
                    <?php else: ?>
                        <?php foreach($clientes_frecuentes as $cf): 
                            $ya_contactado_h = value_in_array($cf['IdCliente'], $contactados_hoy['habitual']);
                            $msg_h = "¡Hola ".s($cf['Nombre'])."! ¿Cómo estás? Quería agradecerte por comprar tan seguido este mes. ¡Es un placer que nos elijas siempre! Te mando un beso, Stefy. ✨💅";
                            $waLink_h = get_wa_link($cf['Telefono'], $msg_h);
                        ?>
                        <div id="habitual-<?= $cf['IdCliente'] ?>" class="flex items-center justify-between p-4 <?= $ya_contactado_h ? 'contact-done' : 'bg-gray-50' ?> border border-gray-100 rounded-3xl transition-all">
                            <div>
                                <h3 onclick="openClientCard(<?= $cf['IdCliente'] ?>)" class="font-bold text-emerald-950 cursor-pointer hover:underline leading-tight mb-0.5"><?= s($cf['Nombre']) ?></h3>
                                <p class="text-sm text-emerald-950/60 font-medium leading-none mb-2"><?= s($cf['Apellido']) ?></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] bg-indigo-900 text-white px-2 py-0.5 rounded-full font-bold uppercase tracking-widest"><?= $cf['CantidadCompras'] ?> Compras</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="toggleContact(<?= $cf['IdCliente'] ?>, 'habitual', this)" class="w-10 h-10 <?= $ya_contactado_h ? 'text-green-500' : 'text-gray-300' ?> hover:bg-white rounded-xl flex items-center justify-center transition-all">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </button>
                                <button onclick="openWAPopup('<?= $waLink_h ?>', <?= $cf['IdCliente'] ?>, 'habitual', this)" class="w-10 h-10 bg-green-500 text-white rounded-xl flex items-center justify-center shadow-lg hover:scale-110 transition-all">
                                    💬
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TARJETA: DIFUSIÓN -->
            <div class="card-premium h-full flex flex-col bg-white rounded-[2.5rem] shadow-xl overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-semantic-difusion">
                    <div>
                        <h2 class="text-2xl font-black leading-tight uppercase tracking-tight">DIFUSIÓN</h2>
                    </div>
                    <span class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-xl">📢</span>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <p class="text-xs text-emerald-900/40 font-bold mb-4 uppercase">Lista copiable (<?= $total_promos ?> contactos)</p>
                    <div id="promoList" class="hidden"><?php 
                        foreach($clientes_promo as $cp) { echo s($cp['Nombre']." ".$cp['Apellido']).",".s($cp['Telefono'])."\n"; }
                    ?></div>
                    <textarea readonly id="promoDisplay" class="w-full flex-1 bg-semantic-difusion/30 rounded-3xl p-6 border-none text-[10px] font-mono focus:ring-0 resize-none shadow-inner mb-6"><?php 
                        foreach($clientes_promo as $cp) { echo s($cp['Nombre']." ".$cp['Apellido']." - ".$cp['Telefono'])."\n"; }
                    ?></textarea>
                    <button onclick="copyPromos()" class="btn-premium w-full text-xl shadow-xl">COPIAR LISTA 📋</button>
                    <p class="text-[10px] text-center mt-3 text-gray-400 italic">Copia en formato CSV (Nombre, Teléfono) para importar.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- FICHA DE CLIENTE (Modal) -->
    <div id="modalFicha" class="hidden fixed inset-0 z-[100] bg-emerald-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 id="fc_nombre" class="brand-title text-3xl text-emerald-900 leading-tight">Cargando...</h2>
                    <p id="fc_telefono" class="text-xs font-bold text-emerald-500 uppercase tracking-widest"></p>
                </div>
                <button onclick="closeFicha()" class="text-4xl text-gray-300 hover:text-red-500 transition-colors">&times;</button>
            </div>
            <div class="p-8">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 border-b pb-2">Últimos Pedidos</h4>
                <div id="fc_historial" class="space-y-3">
                    <!-- History here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyPromos() {
            const list = document.getElementById('promoList').innerText;
            navigator.clipboard.writeText(list).then(() => {
                Swal.fire({
                    title: '¡Copiado!',
                    text: 'Lista CSV en el portapapeles.',
                    icon: 'success',
                    confirmButtonColor: '#00a876'
                });
            });
        }

        async function openClientCard(id) {
            const modal = document.getElementById('modalFicha');
            document.getElementById('fc_nombre').innerText = 'Cargando...';
            document.getElementById('fc_historial').innerHTML = '<p class="text-center py-10 opacity-30 italic">Obteniendo datos...</p>';
            modal.classList.remove('hidden');

            try {
                const res = await fetch('ajax_get_client_card.php?id=' + id);
                const data = await res.json();
                if(data.success) {
                    document.getElementById('fc_nombre').innerText = data.client.Nombre + ' ' + data.client.Apellido;
                    document.getElementById('fc_telefono').innerText = '📞 ' + data.client.Telefono;
                    
                    let hist = '';
                    if(data.history && data.history.length > 0) {
                        data.history.forEach(h => {
                            hist += `<div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <div><p class="text-xs font-bold text-emerald-900">#${h.id}</p><p class="text-[10px] text-gray-400 italic">${h.fecha}</p></div>
                                        <div class="text-lg font-bold text-emerald-700">$ ${h.total}</div>
                                     </div>`;
                        });
                    } else {
                        hist = '<p class="text-center py-10 text-gray-400 italic">Sin pedidos registrados.</p>';
                    }
                    document.getElementById('fc_historial').innerHTML = hist;
                }
            } catch(e) { console.error(e); }
        }

        function closeFicha() { document.getElementById('modalFicha').classList.add('hidden'); }

        function openWAPopup(url, id, tipo, btn) {
            const row = btn.closest('.flex.items-center.justify-between');
            const checkBtn = row.querySelector('button[onclick*="toggleContact"]');
            const yaContactado = checkBtn.classList.contains('text-green-500');

            if (yaContactado) {
                Swal.fire({
                    title: '¿Volver a contactar?',
                    text: 'Este cliente ya figura como contactado hoy.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#fb923c',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Sí, enviar de nuevo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(url, 'wa_popup', 'width=500,height=400');
                    }
                });
            } else {
                window.open(url, 'wa_popup', 'width=500,height=400');
                toggleContact(id, tipo, checkBtn);
            }
        }

        async function toggleContact(id, tipo, btn) {
            const isMarked = btn.classList.contains('text-green-500');
            const row = btn.closest('.flex.items-center.justify-between');
            
            // UI optimista
            if(isMarked) {
                btn.classList.replace('text-green-500', 'text-gray-300');
                row.classList.remove('contact-done');
            } else {
                btn.classList.replace('text-gray-300', 'text-green-500');
                row.classList.add('contact-done');
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('tipo', tipo);

            try {
                const res = await fetch('ajax_mark_contacted.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(!data.success) {
                    // Revertir si falla
                    if(isMarked) { btn.classList.replace('text-gray-300', 'text-green-500'); row.classList.add('contact-done'); }
                    else { btn.classList.replace('text-green-500', 'text-gray-300'); row.classList.remove('contact-done'); }
                    Swal.fire('Error', 'No se pudo registrar el contacto', 'error');
                } else {
                    // Acción después del éxito (si se movió o cambió estado)
                    if (tipo === 'cumple') {
                        // El ordenamiento se hace en PHP, por lo que para reflejar el cambio de posición
                        // real necesitamos recargar o esperar al siguiente render.
                        // Para una experiencia fluida, avisamos que se actualizó.
                        const msg = data.marked ? 'Marcado como saludado' : 'Marca eliminada';
                        const toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        toast.fire({ icon: 'success', title: msg });
                        
                        // Si queremos que cambie de posición inmediatamente, recargamos
                        setTimeout(() => location.reload(), 1500);
                    }
                }
            } catch(e) { console.error(e); }
        }
    </script>
</body>
</html>

