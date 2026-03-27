# HOJA DE RUTA: 24 DE MARZO - PRUEBA INTEGRAL DE CLIENTES Y NAVEGACIÓN

## 📅 FECHA: 24/03/2026

---

### 🎯 OBJETIVO DE LA PRUEBA
Validar que el Sistema de Gestión de Stefy Barroso ha alcanzado el estándar profesional ("ADN Chapitas"), con una navegación robusta en móviles y una base de datos flexible que permite campos opcionales sin errores de duplicidad.

---

### 📂 1. ARCHIVOS PARA SUBIR AL HOSTING (ORDENADO)

Para que todo funcione, debes subir estos archivos a sus carpetas correspondientes:

1.  **Visual:** `styles/main.css` (Nuevos z-index y branding verde esmeralda).
2.  **Navegación:** `admin/index.php` (Solución del botón "Atrás" y carga de modales).
3.  **Seguridad:** `admin/login.php` (Cierre de historial para evitar bucles de login).
4.  **Lógica:** `admin/apps/clientes/ajax_save_client.php` (Protección DNI/Teléfono y campos NULL).
5.  **Interfaz:** `admin/apps/clientes/partials/modal_nuevo_cliente.php` y `modal_editar_cliente.php`.
6.  **Parche:** `admin/apps/clientes/fix_db_null.php` (Úsalo solo si sale el error "cannot be null").
7.  **Test:** `admin/apps/clientes/test_clientes_logic.php` (Para verificar todo en un clic).

---

### 🛠️ 2. PASOS PARA EL TEST MAÑANA

1.  **Reparar Base de Datos (Primer paso):**
    *   Visita: `tuweb.com/admin/apps/clientes/fix_db_null.php`.
    *   Si ves el mensaje verde de **"¡Éxito!"**, la base de datos ya es opcional.
    *   **⚠️ Borra este archivo del hosting tras ejecutarlo.**

2.  **Verificar Navegación Móvil:**
    *   Abre el panel desde tu celular.
    *   Toca en "Nuevo Cliente".
    *   **Check:** ¿Ves el título "Nuevo Cliente" y la X para cerrar arriba de todo? (Si es así, el z-index funcionó).
    *   **Check:** Toca el botón "atrás" de tu móvil con el modal abierto. ¿Se cerró el modal en lugar de sacarte de la web? (Si es así, el "Back Button Guard" funcionó).

3.  **Verificar Lógica de Datos:**
    *   Intenta crear un cliente **SIN** teléfono. ¿Se guardó?
    *   Intenta crear un cliente con un nombre vacío. ¿Aparece como "**Desconocido**"?
    *   Intenta crear dos clientes con un DNI repetido. ¿Lanzó el aviso de "Ya registrado"?

4.  **Control de Calidad Final:**
    *   Visita: `tuweb.com/admin/apps/clientes/test_clientes_logic.php`.
    *   Si todos los puntos salen en **VERDE ✅**, la tarea de Clientes está oficialmente TERMINADA.

---

### 🤖 NOTA DEL ASISTENTE
El sistema ya sigue la **Regla 1:3** (1 Directiva : 3 Skills por módulo). Mañana estaremos listos para empezar el Módulo de Ventas con esta misma robustez.

**¡Buen descanso! Nos vemos mañana para el despliegue final.**
