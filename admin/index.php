<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';
require_once '../includes/security.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Control | Stefy Barroso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css?v=2.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen flex flex-col items-center pt-28 sm:pt-36 pb-4 px-4">

    <!-- Navbar Estilo Noa Mora -->
    <!-- Navbar Estilo Noa Mora -->
    <nav class="fixed top-0 left-0 w-full z-[110] px-0 flex items-center bg-white shadow-md h-20 sm:h-24">
        <!-- Contenedor Logo: Casi pegado a la izquierda -->
        <div class="flex items-center -ml-5 sm:-ml-4 shrink-0">
            <img src="../styles/images/logo_tmc_sin_fondo.png" alt="Logo" class="h-[130px] sm:h-[160px] w-auto transform hover:scale-105 transition-transform duration-300 relative -top-1 sm:-top-2 z-[60] drop-shadow-lg">
        </div>
        
        <!-- Título Centrado en el espacio restante - Ajuste sutil al centro visual -->
        <div class="flex-1 flex justify-center text-center px-0 -ml-6 sm:-ml-8">
            <h1 class="brand-title text-4xl sm:text-6xl text-emerald-900 leading-none whitespace-nowrap">Stefy Barroso</h1>
        </div>
        
        <!-- Perfil -->
        <div class="flex items-center gap-1 pr-1 sm:pr-2 shrink-0">
            <div class="flex flex-col items-end">
                <span class="text-[10px] sm:text-sm font-bold text-emerald-950 leading-none"><?php echo s($_SESSION['fullname'] ?? 'Usuario'); ?></span>
                <span class="text-[8px] sm:text-[10px] uppercase tracking-wider text-emerald-600 font-bold"><?php echo s($_SESSION['role'] ?? 'Admin'); ?></span>
            </div>
            <a href="logout.php" class="bg-red-50 hover:bg-red-100 text-red-600 p-1.5 sm:p-2 rounded-full transition-all duration-300 group" title="Cerrar Sesión">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </div>
    </nav>

    <!-- Espacio para compensar Navbar -->
    <div class="h-4 sm:h-6"></div>

    <!-- Main Navigation Grid -->
    <div class="dashboard-grid">
        
        <!-- Category: Ventas -->
        <div class="chapa-card chapa-ventas">
            <div class="chapa-header">
                <div class="chapa-icon-wrapper">✨</div>
                <h3 class="chapa-title">Ventas</h3>
            </div>
            <div class="chapa-links">
                <a href="javascript:void(0)" onclick="openAppModal('apps/ventas/nota_trabajo.php')" class="chapa-link-item chapa-link-primary">
                    <span class="chapa-link-icon">📝</span> Nuevo Pedido
                </a>
                <a href="javascript:void(0)" onclick="openAppModal('apps/ventas/agenda.php')" class="chapa-link-item">
                    <span class="chapa-link-icon">📅</span> Control de Ventas
                </a>
            </div>
        </div>

        <!-- Category: Productos -->
        <div class="chapa-card chapa-productos">
            <div class="chapa-header">
                <div class="chapa-icon-wrapper">🛍️</div>
                <h3 class="chapa-title">Productos</h3>
            </div>
            <div class="chapa-links">
                <a href="javascript:void(0)" onclick="openAppModal('apps/productos/stock.php')" class="chapa-link-item">
                    <span class="chapa-link-icon">📊</span> Gestión de Inventario
                </a>
                <a href="javascript:void(0)" onclick="openAppModal('apps/productos/proveedores.php')" class="chapa-link-item">
                    <span class="chapa-link-icon">🏭</span> Proveedores
                </a>
            </div>
        </div>

        <!-- Category: Clientes -->
        <div class="chapa-card chapa-clientes">
            <div class="chapa-header">
                <div class="chapa-icon-wrapper">👥</div>
                <h3 class="chapa-title">Clientes</h3>
            </div>
            <div class="chapa-links">
                <a href="javascript:void(0)" onclick="openClientModal()" class="chapa-link-item">
                    <span class="chapa-link-icon">➕</span> Nuevo Cliente
                </a>
                <a href="javascript:void(0)" onclick="openAppModal('apps/clientes/ver_clientes.php')" class="chapa-link-item">
                    <span class="chapa-link-icon">📖</span> Directorio de Clientes
                </a>
                <a href="javascript:void(0)" onclick="openAppModal('apps/clientes/atencion_cliente.php')" class="chapa-link-item">
                    <span class="chapa-link-icon">📞</span> CRM
                </a>
            </div>
        </div>

    </div>


    <!-- Modal para Apps (Iframe) -->
    <div id="modalApp" class="hidden fixed inset-0 z-[300] bg-emerald-950/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="w-full h-full max-w-7xl bg-white shadow-2xl relative animate-in fade-in zoom-in duration-300 sm:rounded-t-3xl flex flex-col">
            <!-- Iframe de la App (Sin cabecera en el wrapper, la app trae la suya) -->
            <div class="flex-1 overflow-hidden">
                <iframe id="iframeApp" src="" class="w-full h-full border-none sm:rounded-3xl"></iframe>
            </div>
        </div>
    </div>

    <!-- Modales de Negocio -->
    <?php include 'apps/clientes/partials/modal_nuevo_cliente.php'; ?>

    <script>
        function openAppModal(url) {
            document.getElementById('iframeApp').src = url;
            document.getElementById('modalApp').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Evitar scroll de fondo
        }

        function closeAppModal() {
            document.getElementById('modalApp').classList.add('hidden');
            document.getElementById('iframeApp').src = '';
            document.body.style.overflow = 'auto';
        }

        // --- Back Button Protection ---
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            if (!document.getElementById('modalApp').classList.contains('hidden')) {
                closeAppModal();
                window.history.pushState(null, null, window.location.href);
            } else if (!document.getElementById('modalNuevoCliente')?.classList.contains('hidden')) {
                closeClientModal();
                window.history.pushState(null, null, window.location.href);
            } else {
                window.history.pushState(null, null, window.location.href);
            }
        };
    </script>
</body>
</html>
