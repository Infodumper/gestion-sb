## Directiva: Gestión Integral de Clientes
> **Skills Asociados:** `client_manager` · `premium_attention` · `client_profiler`
> **Versión:** 2.0 · **Estado:** Producción

---

## Capa 1: Objetivo y Alcance

**Misión del módulo:**
Ser el repositorio centralizado y veraz de la base de clientes. El sistema habilita una gestión 360° que abarca desde la captura de datos básicos hasta la visualización profunda del historial transaccional y las acciones de fidelización.

**Alcance:**
- Alta, modificación y baja lógica de clientes
- Búsqueda y filtrado en tiempo real
- Historial de pedidos por cliente (Ficha 360°)
- Indicadores de estado (activo, inactivo, baja)

---

## Capa 2: Orquestación (Procesos de Negocio)

### 1. Captura y Normalización de Datos
- **Teléfono como clave de negocio**: Solo dígitos (`preg_replace('/\D/', '', $tel)`). Es `UNIQUE NOT NULL` en la BD.
- **DNI**: Opcional, normalizado eliminando espacios y guiones.
- **Nombres**: Con `trim()` y capitalización correcta (ej: `ucwords(strtolower($nombre))`).
- **Duplicación bloqueada**: Antes de cualquier INSERT, verificar que el teléfono no exista.

### 2. Estados del Cliente

| Código | Estado | Significado |
|---|---|---|
| `1` | Activo | Cliente operativo |
| `2` | Inactivo | Sin actividad reciente |
| `0` | Baja | Dado de baja (borrado lógico) |

**Regla**: Nunca usar `DELETE` en Clientes. Usar `UPDATE Estado = 0`.

### 3. Ficha 360° del Cliente
- Al abrir la ficha de un cliente, cargar vía AJAX:
  - Datos personales (nombre, teléfono, email, fecha de nacimiento)
  - Historial de pedidos (agrupados por fecha, con totales)
  - Última visita / último contacto WA
  - Métricas: total pedidos, monto total acumulado, ticket promedio

---

## Capa 3: Ejecución (Componentes y Archivos)

| Archivo | Rol |
|---|---|
| `admin/apps/clientes/ver_clientes.php` | Vista maestro — listado y búsqueda |
| `admin/apps/clientes/atencion_cliente.php` | CRM — cumpleaños y habituales |
| `admin/apps/clientes/partials/modal_editar_cliente.php` | Modal de alta/edición |
| `admin/apps/clientes/partials/modal_ficha_cliente.php` | Ficha 360° del cliente |
| `admin/apps/clientes/ajax_save_client.php` | Endpoint CREATE/UPDATE |
| `admin/apps/clientes/ajax_get_clients.php` | Endpoint GET listado |
| `admin/apps/clientes/ajax_get_client_card.php` | Endpoint GET ficha 360° |
| `admin/apps/clientes/ajax_delete_client.php` | Endpoint baja lógica |
| `admin/apps/clientes/ajax_search_client.php` | Endpoint búsqueda dinámica |
| `admin/apps/clientes/ajax_mark_contacted.php` | Endpoint registro contacto WA |

---

## Capa 4: Estándar Visual (Regla de las Subplacas)

- **Listado**: Cada cliente = una `subplaca-adn` con avatar de iniciales (gradiente), nombre, teléfono y acciones rápidas.
- **Acciones rápidas obligatorias**: Editar (SVG lápiz), WhatsApp (SVG WA verde), y según contexto: Check de habitual.
- **Buscador**: Input con debounce de 300ms al inicio del listado.
- **Estado vacío**: Mensaje amigable cuando no hay clientes o la búsqueda no retorna resultados.
- **Avatar**: Iniciales del nombre con gradiente `var(--color-primary)` → `var(--color-secondary)`. Nunca fotos ni emojis de persona.

---

## Capa 5: Base de Datos

### Tabla: `Clientes`

```sql
CREATE TABLE Clientes (
    IdCliente     INT          NOT NULL AUTO_INCREMENT,
    NombreCompleto VARCHAR(150) NOT NULL,
    Telefono      VARCHAR(20)  NOT NULL,          -- Solo dígitos, sin código de país
    Dni           VARCHAR(20)  NULL,
    Email         VARCHAR(150) NULL,
    FechaNac      DATE         NULL,               -- Para módulo de cumpleaños
    Notas         TEXT         NULL,
    Estado        TINYINT      NOT NULL DEFAULT 1, -- 1=Activo, 2=Inactivo, 0=Baja
    CreadoPor     INT          NULL,               -- FK a DbLogin.IdUsuario
    FechaCreacion DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FechaModif    DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (IdCliente),
    UNIQUE KEY uq_telefono (Telefono),
    UNIQUE KEY uq_dni (Dni),
    FOREIGN KEY (CreadoPor) REFERENCES DbLogin(IdUsuario) ON DELETE SET NULL
);
```

### Relaciones Hacia Afuera

| Tabla relacionada | Columna FK | ON DELETE |
|---|---|---|
| `Pedidos` | `IdCliente` | `CASCADE` — al borrar cliente, se eliminan sus pedidos |
| `ContactosWhatsapp` | `IdCliente` | `CASCADE` — historial de contactos |

---

## Reglas de Validación (Frontend + Backend)

| Campo | Validación frontend | Validación backend |
|---|---|---|
| NombreCompleto | No vacío, mín 3 chars | `trim()` + `!empty()` |
| Telefono | Solo números, mín 8 dígitos | `preg_replace('/\D/', '', $tel)` + check UNIQUE |
| Dni | Opcional, 7-9 dígitos | `preg_replace('/[\s\-]/', '', $dni)` + check UNIQUE si no vacío |
| FechaNac | Formato dd/mm/aaaa | Convertir a `Y-m-d` para MySQL |
| Email | Formato básico (si se informa) | `filter_var($email, FILTER_VALIDATE_EMAIL)` |
