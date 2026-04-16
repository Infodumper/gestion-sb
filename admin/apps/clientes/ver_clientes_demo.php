<?php
/**
 * DEMO VERSION - NO DATABASE / NO LOGIN
 * Use this to verify the Premium UI design.
 */
require_once '../../../includes/utils.php';

// Mock Data
$clientes = [
    ['IdCliente' => 1, 'Nombre' => 'Stefy', 'Apellido' => 'Barroso', 'Telefono' => '2235551234', 'Dni' => '12345678', 'FechaNac' => '1990-04-14'],
    ['IdCliente' => 2, 'Nombre' => 'Ana', 'Apellido' => 'García', 'Telefono' => '2234449876', 'Dni' => '22333444', 'FechaNac' => '1985-05-20'],
    ['IdCliente' => 3, 'Nombre' => 'Carlos', 'Apellido' => 'Pérez', 'Telefono' => '1155554321', 'Dni' => '33111222', 'FechaNac' => '1992-12-01'],
    ['IdCliente' => 4, 'Nombre' => 'Lucía', 'Apellido' => 'Martínez', 'Telefono' => '2239998877', 'Dni' => '44000111', 'FechaNac' => date('Y-m-d')], // Cumple Hoy
    ['IdCliente' => 5, 'Nombre' => 'Mariano', 'Apellido' => 'Rodríguez', 'Telefono' => '2266667788', 'Dni' => '18222333', 'FechaNac' => '1980-08-15']
];

$filtro = '';
$total_pages = 1;
$page = 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO | Directorio de Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/main.css?v=demo">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen p-4 bg-[#f0fdf4]">
    <div class="max-w-7xl mx-auto">
        <!-- PLACA MAESTRA -->
        <div class="master-placa bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-100 flex flex-col mb-10">
            
            <!-- Cabecera Maestra Estándar -->
            <?php render_premium_header('Directorio de Clientes (DEMO)', 'Swal.fire("Demo", "Función de nuevo cliente en modo visual", "info")'); ?>

            <div class="p-8">
                <!-- Barra de Búsqueda Premium -->
                <div class="mb-10 relative max-w-2xl mx-auto group">
                    <form class="relative">
                        <input type="text" class="w-full py-4 pl-14 pr-12 rounded-[2rem] bg-white border border-emerald-100 shadow-sm focus:shadow-xl transition-all outline-none font-medium text-emerald-950" placeholder="Buscar en el directorio (Modo Demo)...">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-emerald-500/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </form>
                </div>

                <!-- Listado -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach($clientes as $c): 
                        $initials = strtoupper(substr($c['Nombre'], 0, 1) . substr($c['Apellido'], 0, 1));
                        $avatar_gradient = get_gradient_avatar($c['IdCliente']);
                    ?>
                    <div class="subplaca-adn !mb-0 group cursor-pointer hover:!border-emerald-200" onclick="Swal.fire('Demo', 'Aquí se abriría la ficha de <?= $c['Nombre'] ?>', 'success')">
                        <div class="subplaca-acento bg-emerald-500 group-hover:bg-emerald-600 transition-colors"></div>
                        <div class="subplaca-cuerpo !p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $avatar_gradient ?> flex items-center justify-center text-white font-bold text-sm shadow-sm transition-transform group-hover:scale-110">
                                    <?= $initials ?>
                                </div>
                                <div class="subplaca-info">
                                    <h3 class="font-bold text-emerald-950 text-[1.1rem] leading-tight group-hover:text-emerald-700 transition-colors"><?= htmlspecialchars($c['Apellido']) ?> <?= htmlspecialchars($c['Nombre']) ?></h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <p class="text-[11px] font-bold text-emerald-600/70 tracking-wider"><?= htmlspecialchars($c['Telefono']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="subplaca-acciones !flex-row !items-center !gap-2">
                                 <button class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100/50">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                 </button>
                                 <a href="#" onclick="event.stopPropagation();" class="w-10 h-10 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20 hover:scale-110 transition-all">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.52.909 3.125 1.388 4.773 1.389 5.233.002 9.491-4.258 9.493-9.492.001-2.533-.986-4.915-2.778-6.708s-4.177-2.779-6.709-2.78c-5.235 0-9.492 4.258-9.493 9.493-.001 1.761.488 3.476 1.415 4.974l-1.08 3.946 4.079-1.071zm9.178-6.035c-.255-.127-1.503-.734-1.737-.82-.233-.086-.403-.127-.573.127s-.657.82-.805.99c-.148.17-.297.191-.553.064-1.831-.916-2.825-1.526-3.951-3.456-.255-.436.255-.404.729-1.353.078-.159.039-.297-.021-.423-.06-.126-.573-1.38-.785-1.889-.208-.499-.42-.43-.573-.438-.148-.007-.318-.008-.488-.008s-.446.063-.679.297c-.234.233-.892.871-.892 2.122 0 1.25.912 2.46 1.039 2.63.127.17 1.794 2.738 4.346 3.84.607.262 1.08.419 1.448.536.611.194 1.167.166 1.607.101.491-.072 1.503-.615 1.714-1.209.211-.595.211-1.104.148-1.209-.063-.105-.233-.148-.488-.275z"/></svg>
                                 </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
