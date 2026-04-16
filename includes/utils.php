<?php
/**
 * ZEN UTILS - Antigravity (SISTEMA GLOBAL)
 * Ubicación: /includes/utils.php
 * Basado en los principios de legibilidad y simplicidad.
 */

if (!function_exists('fmt_money')) {
    /**
     * Formatea un número como moneda local.
     */
    function fmt_money($amount) {
        return '$ ' . number_format((float)$amount, 2, ',', '.');
    }
}

if (!function_exists('clean_phone_wa')) {
    /**
     * Limpia un número de teléfono para usar en WhatsApp.
     * Asume prefijo de Argentina (549) si no tiene.
     */
    function clean_phone_wa($phone) {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($clean) === 10) $clean = "549" . $clean;
        return $clean;
    }
}

if (!function_exists('get_wa_link')) {
    /**
     * Genera el enlace de WhatsApp con un mensaje sanitizado.
     */
    function get_wa_link($phone, $message) {
        $phone_clean = clean_phone_wa($phone);
        // Ajuste: si el mensaje ya está codificado, evitar doble codificación
        // Pero por "Zen", lo hacemos explícito aquí y lo recibimos limpio.
        return "launcher_wa.php?phone=" . $phone_clean . "&text=" . rawurlencode($message);
    }
}

if (!function_exists('get_month_name')) {
    /**
     * Devuelve el nombre del mes en español.
     */
    function get_month_name($month_num) {
        $months = [
            1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 
            5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto', 
            9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'
        ];
        return $months[(int)$month_num] ?? 'Desconocido';
    }
}
if (!function_exists('render_premium_header')) {
    /**
     * CENTRALIZA LA CABECERA DE LOS MÓDULOS (PREMIUM)
     * Asegura que el título, el botón NUEVO y la X de cierre sean idénticos en todo el sistema.
     */
    function render_premium_header($title, $btn_new_onclick = null) {
        $btn_html = '';
        if ($btn_new_onclick) {
            $btn_html = '
            <button onclick="'.htmlspecialchars($btn_new_onclick).'" class="btn-header-nuevo flex items-center gap-2 group">
                <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>NUEVO</span>
            </button>';
        }
        echo '
        <div class="modal-header-premium mb-6">
            <h1 class="modal-title-premium">'.htmlspecialchars($title).'</h1>
            '.$btn_html.'
            <button type="button" onclick="window.parent.closeAppModal()" class="btn-close-premium group" title="Cerrar">
                <svg class="w-6 h-6 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>';
    }
}

if (!function_exists('get_gradient_avatar')) {
    /**
     * Devuelve una clase de gradiente CSS basada en una cadena (ej. nombre) para consistencia.
     */
    function get_gradient_avatar($str) {
        $gradients = [
            'from-emerald-500 to-teal-700',
            'from-blue-500 to-indigo-700',
            'from-purple-500 to-fuchsia-700',
            'from-rose-500 to-pink-700',
            'from-amber-500 to-orange-700',
            'from-cyan-500 to-blue-600',
            'from-lime-500 to-emerald-600'
        ];
        $index = abs(crc32((string)$str)) % count($gradients);
        return $gradients[$index];
    }
}
?>
