# HOJA DE RUTA: 30 DE MARZO - PRUEBA INTEGRAL DE CLIENTES Y NAVEGACIÓN

## 📅 FECHA: 30/03/2026

---

### 🎯 OBJETIVO DE LA PRUEBA
Validar que el Sistema de Gestión de Stefy Barroso ha alcanzado el estándar profesional (**Arquitectura de Placas Independientes de Información**), con una navegación robusta en móviles y una base de datos flexible que permite campos opcionales sin errores de duplicidad.

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

### 🛠️ 2. PASOS PARA EL TEST
*(Para probar directamente en Celular y PC)*

1.  **Reparar Base de Datos (Si no lo hiciste aún):**
    *   Visita: `tuweb.com/admin/apps/clientes/fix_db_null.php`.
    *   Si ves el mensaje verde de **"¡Éxito!"**, la base de datos ya es opcional.
    *   **⚠️ Borra este archivo del hosting tras ejecutarlo.**

2.  **Verificar Navegación Móvil:**
    *   Abre el panel desde tu celular.
    *   Toca en "Nuevo Cliente".
    *   **Check:** ¿Ves el título "Nuevo Cliente" y la X para cerrar ubicada correctamente.
    *   **Check:** Toca el botón "atrás" de tu móvil con el modal abierto. ¿Se cerró el modal en lugar de sacarte de la web?

3.  **Verificar Lógica de Datos:**
    *   Intenta crear un cliente **SIN** teléfono. ¿Se guardó?
    *   Intenta crear un cliente con un nombre vacío. ¿Aparece como "**Desconocido**"?
    *   Intenta crear dos clientes con un DNI repetido. ¿Lanzó el aviso de "Ya registrado"?

4.  **Control de Calidad Final:**
    *   Obtén confirmación de 100% éxito y que las "Subplacas" de los clientes aparecen sin problemas en la vista general. 

---

### 🤖 NOTA PARA EL PULL REQUEST (PR)
El sistema ha migrado a la **estrategia de 5 Súper-Skills consolidadas**, simplificando todo bajo `GEMINI.md` y depurando archivos basuras.
Hoy, tras hacer el test de la UI y grabar la capa visual perfecta, puedes armar el PR hacia `main`.
Una vez mergeado, estaremos oficialmente listos para lanzar todo el poder sobre el Módulo de **Ventas y Catálogo**.

**¡Mucho éxito con el test de mañana!**
