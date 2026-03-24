# Directiva: Gestión Integral de Clientes

## Capa 1: Directiva (Objetivo y Alcance)

**Objetivo:**
Establecer un repositorio centralizado y veraz de la base instalada de clientes. El sistema debe permitir una gestión 360° que abarque desde la captura de datos básicos hasta la visualización profunda del historial transaccional, garantizando la integridad de la información y la facilidad de contacto.

## Capa 2: Orquestación (Procesos de Gestión)

1. **Captura y Normalización**:
    * **Validación de Identidad**: Uso de Teléfono como identificador primario (DNI opcional).
    * **Limpieza de Datos**: Formateo automático de nombres y normalización de números telefónicos para uso internacional/WhatsApp.
2. **Ciclo de Vida del Dato**:
    * Alta rápida, edición en caliente (vía AJAX) y gestión de estados (Activo, Inactivo, VIP).
3. **Visión 360° (Ficha del Cliente)**:
    * Agregación visual de los últimos 10 pedidos y perfiles de consumo dentro de modales persistentes.

## Capa 3: Ejecución (Componentes y Archivos)

*   **Ubicación Maestro**: `admin/apps/clientes/ver_clientes.php`
*   **Gestión AJAX**:
    *   `ajax_save_client.php`: Lógica unificada para INSERT/UPDATE.
    *   `ajax_get_client_card.php`: Motor de renderizado para la Ficha Técnica del cliente.
*   **Partials Reutilizables**:
    *   `partials/modal_nuevo_cliente.php`: Formulario estándar de captura.
    *   `partials/modal_editar_cliente.php`: Interfaz de modificación.

## Capa 4: Observabilidad

*   **Auditoría de Cambios**: Registro obligatorio de cada modificación de datos de contacto.
*   **Navegación Premium**: Transiciones suaves entre el listado maestro y los modales de edición para evitar la pérdida de contexto del operador.