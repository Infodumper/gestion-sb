<?php
/**
 * DEMO VERSION - NO DATABASE / NO LOGIN
 * Use this to verify the Premium UI design of the CRM.
 */
require_once '../../../includes/utils.php';

$nombre_mes = "Abril";
$hoy_dia = date('d');
$hoy_mes = date('m');

$cumpleanieros = [
    ['IdCliente' => 10, 'Nombre' => 'Lucía', 'Apellido' => 'Martínez', 'Telefono' => '2239998877', 'FechaNac' => date('Y-m-d')], // Hoy
    ['IdCliente' => 11, 'Nombre' => 'Mariano', 'Apellido' => 'Pérez', 'Telefono' => '2231112222', 'FechaNac' => '1990-04-20'],
    ['IdCliente' => 12, 'Nombre' => 'Silvia', 'Apellido' => 'Gómez', 'Telefono' => '2233334444', 'FechaNac' => '1987-04-25']
];

$clientes_frecuentes = [
    ['IdCliente' => 20, 'Nombre' => 'Ana', 'Apellido' => 'García', 'Telefono' => '2234449876', 'CantidadCompras' => 5],
    ['IdCliente' => 21, 'Nombre' => 'Carlos', 'Apellido' => 'Pérez', 'Telefono' => '1155554321', 'CantidadCompras' => 3]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO | CRM Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css?v=demo">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="p-4 bg-[#f0fdf4]">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-100 flex flex-col">
            <?php render_premium_header('CRM (MODO DEMO)'); ?>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- COLUMNA CUMPLEAÑOS -->
                <div class="flex flex-col bg-white rounded-[2.5rem] shadow-xl border border-rose-100 overflow-hidden">
                    <div class="px-8 py-6 flex justify-between items-center bg-rose-500 text-white shadow-lg">
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] uppercase tracking-[0.2em] font-black opacity-60 mb-0.5">Atención Premium</span>
                            <h2 class="font-bold text-xl tracking-tight">CUMPLEAÑOS DE <?= $nombre_mes ?></h2>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-8 0h4m-4 5a2 2 0 112 2h-2zm0 0h4m-8 0h4"></path></svg>
                        </div>
                    </div>
                    <div class="p-3 flex flex-col gap-3">
                        <?php foreach($cumpleanieros as $c): 
                            $c_dia = date('d', strtotime($c['FechaNac']));
                            $es_hoy = ($c_dia == $hoy_dia);
                            $initials = strtoupper(substr($c['Nombre'], 0, 1) . substr($c['Apellido'], 0, 1));
                            $avatar_gradient = get_gradient_avatar($c['IdCliente']);
                        ?>
                        <div class="subplaca-adn group <?= $es_hoy ? '!border-rose-200 ring-4 ring-rose-500/5' : '' ?>">
                            <div class="subplaca-acento bg-rose-400"></div>
                            <div class="subplaca-cuerpo !p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $avatar_gradient ?> flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        <?= $initials ?>
                                    </div>
                                    <div class="subplaca-info text-left">
                                        <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight"><?= htmlspecialchars($c['Apellido']) ?> <?= htmlspecialchars($c['Nombre']) ?></h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest"><?= $c_dia ?> DE <?= $nombre_mes ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                    <button class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-100/50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button class="w-9 h-9 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20 <?= !$es_hoy ? 'opacity-30' : '' ?>">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.52.909 3.125 1.388 4.773 1.389 5.233.002 9.491-4.258 9.493-9.492.001-2.533-.986-4.915-2.778-6.708s-4.177-2.779-6.709-2.78c-5.235 0-9.492 4.258-9.493 9.493-.001 1.761.488 3.476 1.415 4.974l-1.08 3.946 4.079-1.071zm9.178-6.035c-.255-.127-1.503-.734-1.737-.82-.233-.086-.403-.127-.573.127s-.657.82-.805.99c-.148.17-.297.191-.553.064-1.831-.916-2.825-1.526-3.951-3.456-.255-.436.255-.404.729-1.353.078-.159.039-.297-.021-.423-.06-.126-.573-1.38-.785-1.889-.208-.499-.42-.43-.573-.438-.148-.007-.318-.008-.488-.008s-.446.063-.679.297c-.234.233-.892.871-.892 2.122 0 1.25.912 2.46 1.039 2.63.127.17 1.794 2.738 4.346 3.84.607.262 1.08.419 1.448.536.611.194 1.167.166 1.607.101.491-.072 1.503-.615 1.714-1.209.211-.595.211-1.104.148-1.209-.063-.105-.233-.148-.488-.275z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMNA HABITUALES -->
                <div class="flex flex-col bg-white rounded-[2.5rem] shadow-xl border border-amber-100 overflow-hidden">
                    <div class="px-8 py-6 flex justify-between items-center bg-amber-400 text-white shadow-lg">
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] uppercase tracking-[0.2em] font-black opacity-60 mb-0.5">Fidelización</span>
                            <h2 class="font-bold text-xl tracking-tight">CLIENTES HABITUALES</h2>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                    </div>
                    <div class="p-3 flex flex-col gap-3">
                        <?php foreach($clientes_frecuentes as $cf): 
                            $initials = strtoupper(substr($cf['Nombre'], 0, 1) . substr($cf['Apellido'], 0, 1));
                            $avatar_gradient = get_gradient_avatar($cf['IdCliente']);
                        ?>
                        <div class="subplaca-adn group">
                            <div class="subplaca-acento bg-amber-400"></div>
                            <div class="subplaca-cuerpo !p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $avatar_gradient ?> flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        <?= $initials ?>
                                    </div>
                                    <div class="subplaca-info text-left">
                                        <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight"><?= htmlspecialchars($cf['Apellido']) ?> <?= htmlspecialchars($cf['Nombre']) ?></h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-lg font-black uppercase"><?= $cf['CantidadCompras'] ?> COMPRAS</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                    <button class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-100/50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button class="w-9 h-9 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.52.909 3.125 1.388 4.773 1.389 5.233.002 9.491-4.258 9.493-9.492.001-2.533-.986-4.915-2.778-6.708s-4.177-2.779-6.709-2.78c-5.235 0-9.492 4.258-9.493 9.493-.001 1.761.488 3.476 1.415 4.974l-1.08 3.946 4.079-1.071zm9.178-6.035c-.255-.127-1.503-.734-1.737-.82-.233-.086-.403-.127-.573.127s-.657.82-.805.99c-.148.17-.297.191-.553.064-1.831-.916-2.825-1.526-3.951-3.456-.255-.436.255-.404.729-1.353.078-.159.039-.297-.021-.423-.06-.126-.573-1.38-.785-1.889-.208-.499-.42-.43-.573-.438-.148-.007-.318-.008-.488-.008s-.446.063-.679.297c-.234.233-.892.871-.892 2.122 0 1.25.912 2.46 1.039 2.63.127.17 1.794 2.738 4.346 3.84.607.262 1.08.419 1.448.536.611.194 1.167.166 1.607.101.491-.072 1.503-.615 1.714-1.209.211-.595.211-1.104.148-1.209-.063-.105-.233-.148-.488-.275z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
