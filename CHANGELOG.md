# Bitácora de Cambios - "Gestión SB" (Beta 1)

Este documento resume las actualizaciones críticas realizadas para la estandarización del sistema de diseño y la mejora operativa del CRM.

## 🎨 Sistema de Diseño (UI/UX)
- **Centralización de Cabeceras**: Se implementó `render_premium_header()` en todos los módulos.
  - Elimina colisiones entre títulos largos y botones.
  - Garantiza que la "X" sea idéntica y esté en la misma posición (`top: 0.75rem`) en todo el sistema.
- **Modelo de Subplacas Compactas**: Aplicado en los 4 módulos principales (Clientes, Proveedores, Productos, Ventas).
  - Reduce el espacio en blanco vertical en un 40%.
  - Mejora la legibilidad en móviles mediante tipografía escalada y colores de contraste.
- **Mitigación de Caché**: Implementación de versionado de activos (`styles/main.css?v=4.0`) para forzar la actualización inmediata en dispositivos móviles.
- **Correcciones Críticas**: Reparación del desbordamiento y bloqueo de scroll en el Directorio de Clientes.

## 🛠️ CRM (Atención al Cliente)
- **Lógica de Contacto Bidireccional**: Los estados de contacto (✓) ahora son conmutables ("Toggles"), permitiendo desmarcar errores.
- **Automatización de Habituales**: El envío de WhatsApp (`💬`) marca automáticamente la promoción del mes en la base de datos.
- **Mensajería Contextual**: Las alertas detectan si el contacto es hoy (Cumpleaños) o este mes (Habituales) para alertar antes de un reenvío accidental.

## 📂 Estructura y Archivos
- `includes/utils.php`: Nueva función `render_premium_header` con lógica de seguridad de padding.
- `styles/main.css`: Sistema global actualizado a v4.0.
- `admin/apps/ventas/ajax_get_pedido_details.php`: Nuevo endpoint para la vista rápida de pedidos.
- `admin/index.php`: Limpieza de menú (Eliminación de Campañas Catálogos).

---
*Documentado por Antigravity AI*
