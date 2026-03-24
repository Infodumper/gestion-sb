<?php
// launcher_wa.php - Lanzador intermedio para WhatsApp
$phone = $_GET['phone'] ?? '';
$text = $_GET['text'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abriendo WhatsApp...</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0fdf4; /* Verde suave compatible con Stefy Barroso */
            color: #064e3b;
        }
        .msg {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #10b981; /* Emerald 500 */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px auto;
        }
        a { color: #10b981; font-weight: bold; text-decoration: none; border-bottom: 2px solid #10b981; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="msg">
        <div class="spinner"></div>
        <p>Iniciando WhatsApp...</p>
        <p style="font-size: 0.9em; margin-top: 15px; color: #6b7280;">
            Si no abre en unos segundos, <a id="manualLink" href="#">haz clic aquí</a>.
        </p>
    </div>

    <script>
        const phone = <?php echo json_encode($phone); ?>;
        const text = <?php echo json_encode($text); ?>;
        
        if (phone) {
            // Limpiar teléfono por si acaso
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            // Formatear mensaje para URL
            const deepLink = "whatsapp://send?phone=" + cleanPhone + "&text=" + encodeURIComponent(text);
            
            const manualLink = document.getElementById('manualLink');
            manualLink.href = deepLink;
            
            // Intento de apertura automática por iframe (menos intrusivo)
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = deepLink;
            document.body.appendChild(iframe);
            
            // Fallback: clic simulado
            setTimeout(() => { manualLink.click(); }, 1200);

            // Cerrar la ventana tras la acción
            setTimeout(() => { window.close(); }, 5000); 
        } else {
            document.querySelector('.msg').innerHTML = "<p style='color:red; font-weight:bold;'>Error: No se recibió información del contacto.</p>";
        }
    </script>
</body>
</html>
