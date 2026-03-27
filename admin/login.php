<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Consultora de Belleza</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Libre+Baskerville:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css?v=1.1">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .brand-font { font-family: 'Libre Baskerville', serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-[#f0fdf4] min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full glass rounded-[2.5rem] shadow-2xl p-10 pt-8 -mt-24 sm:-mt-32 transform transition-all hover:scale-[1.01]">
        <div class="text-center mb-10">
            <!-- Logo Centrado encima del nombre - Margen reducido y un 20% más grande -->
            <img src="../styles/images/logo_tmc_sin_fondo.png" alt="Logo" class="h-44 sm:h-56 w-auto mx-auto mb-0 drop-shadow-lg">
            
            <h1 class="brand-font text-[2.1rem] sm:text-[2.4rem] text-emerald-600 mb-1 leading-tight">Stefy Barroso</h1>
            <p class="text-emerald-950/70 font-bold uppercase tracking-widest text-[12.5px]">Consultora de Belleza</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                <input type="email" name="email" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 outline-none transition-all"
                    placeholder="tunombre@ejemplo.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 outline-none transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-emerald-600 text-white py-4 rounded-xl font-semibold hover:bg-emerald-700 transform active:scale-95 transition-all shadow-lg">
                Iniciar Sesión
            </button>
        </form>

        <div id="message" class="mt-6 text-center text-sm hidden"></div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const msg = document.getElementById('message');
            const formData = new FormData(e.target);

            btn.disabled = true;
            btn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin h-5 w-5 mr-3 border-2 border-white border-t-transparent rounded-full" viewBox="0 0 24 24"></svg> Cargando...</span>';

            try {
                const response = await fetch('ajax_login.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                msg.classList.remove('hidden', 'text-red-500', 'text-green-600');
                if (data.success) {
                    msg.classList.add('text-green-600');
                    msg.textContent = '¡Bienvenido! Redirigiendo...';
                    setTimeout(() => window.location.replace('index.php'), 1500);
                } else {
                    msg.classList.add('text-red-500');
                    msg.textContent = data.message;
                    btn.disabled = false;
                    btn.textContent = 'Iniciar Sesión';
                }
            } catch (error) {
                msg.classList.remove('hidden');
                msg.classList.add('text-red-500');
                msg.textContent = 'Error de conexión con el servidor.';
                btn.disabled = false;
                btn.textContent = 'Iniciar Sesión';
            }
        });
    </script>
</body>
</html>
