---
name: login_manager
description: Gestión de autenticación, PHP Sessions y seguridad sin recarga de vistas para entornos móviles.
directiva: directives/build_login.md
---

# Skill: login_manager

## 1. Rol y Responsabilidad

Eres el agente **login_manager**. Garantizas el acceso seguro al sistema. Tu mandato se resume en dos ejes:

1. **Seguridad sin concesiones**: `password_hash` / `password_verify` + PDO siempre. Cero texto plano en BD.
2. **UX sin fricciones**: El form de login no recarga la página. El submit va por AJAX y redirige en caso de éxito.

Leer directiva antes de ejecutar: `directives/build_login.md`.

---

## 2. Patrones de Código Obligatorios

### A. Vista de Login (`admin/login.php`)

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso — Gestion SB</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/styles/colores.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center p-4 font-poppins">

  <div class="w-full max-w-sm">
    <!-- Logo / Marca -->
    <div class="text-center mb-8">
      <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-4"
           style="background: var(--color-primary)">
        <span class="text-white text-2xl font-bold">SB</span>
      </div>
      <h1 class="text-white text-2xl font-semibold">Gestion SB</h1>
      <p class="text-gray-400 text-sm mt-1">Ingresá tus credenciales para continuar</p>
    </div>

    <!-- Tarjeta de Login (Subplaca) -->
    <div class="bg-white rounded-[1.5rem] shadow-2xl p-6">
      <form id="form-login" novalidate>
        <div class="mb-4">
          <label for="usuario" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
          <input type="text" id="usuario" name="usuario" required
                 class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-gray-800 text-sm"
                 placeholder="tu@usuario.com"
                 autocomplete="username">
        </div>

        <div class="mb-6">
          <label for="clave" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
          <div class="relative">
            <input type="password" id="clave" name="clave" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-gray-800 text-sm pr-12"
                   placeholder="••••••••"
                   autocomplete="current-password">
            <button type="button" onclick="togglePassword()" tabindex="-1"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
              <svg id="icon-eye" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" id="btn-login"
                class="w-full py-3 rounded-xl text-white font-medium text-sm transition-all"
                style="background: var(--color-primary)">
          Ingresar
        </button>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('clave');
      input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('form-login').addEventListener('submit', async function(e) {
      e.preventDefault();
      const usuario = document.getElementById('usuario').value.trim();
      const clave   = document.getElementById('clave').value;
      const btn     = document.getElementById('btn-login');

      if (!usuario || !clave) {
        Swal.fire({ icon: 'warning', title: 'Campos vacíos', text: 'Completá usuario y contraseña', confirmButtonText: 'Aceptar' });
        return;
      }

      btn.disabled = true;
      btn.textContent = 'Verificando...';

      try {
        const res  = await fetch('ajax_login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ usuario, clave })
        });
        const json = await res.json();

        if (json.status === 'ok') {
          btn.textContent = '¡Acceso concedido!';
          window.location.href = json.data.redirect ?? '/admin/index.php';
        } else {
          Swal.fire({ icon: 'error', title: 'Acceso denegado', text: json.message, confirmButtonText: 'Reintentar' });
          btn.disabled = false;
          btn.textContent = 'Ingresar';
        }
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar al servidor', confirmButtonText: 'Aceptar' });
        btn.disabled = false;
        btn.textContent = 'Ingresar';
      }
    });
  </script>
</body>
</html>
```

### B. Endpoint de Autenticación (`admin/ajax_login.php`)

```php
<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../utils/logger.php';
header('Content-Type: application/json; charset=utf-8');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

$input   = json_decode(file_get_contents('php://input'), true);
$usuario = trim($input['usuario'] ?? '');
$clave   = $input['clave'] ?? '';

if (empty($usuario) || empty($clave)) {
    echo json_encode(['status' => 'error', 'message' => 'Credenciales incompletas']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT IdUsuario, Usuario, Clave, Rol FROM DbLogin WHERE Usuario = :usr AND Estado = 1");
    $stmt->execute([':usr' => $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($clave, $user['Clave'])) {
        log_event('AUTH_FAIL', "Intento fallido para usuario: $usuario", __FILE__);
        // Esperar 1s para mitigar fuerza bruta
        sleep(1);
        echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos']);
        exit;
    }

    // Sesión establecida
    session_regenerate_id(true); // Previene session fixation
    $_SESSION['userid']   = $user['IdUsuario'];
    $_SESSION['username'] = $user['Usuario'];
    $_SESSION['rol']      = $user['Rol'];
    $_SESSION['login_at'] = time();

    log_event('AUTH_OK', "Login exitoso: uid={$user['IdUsuario']}", __FILE__);
    echo json_encode([
        'status' => 'ok',
        'data'   => ['redirect' => '/admin/index.php'],
        'message' => 'Acceso concedido'
    ]);

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor']);
}
exit;
```

### C. Middleware de Protección (`includes/security.php`)

```php
<?php
// Incluir en TODA vista privada como primera instrucción
if (session_status() === PHP_SESSION_NONE) session_start();

// Timeout de sesión: 8 horas
$SESSION_TIMEOUT = 8 * 3600;

if (
    !isset($_SESSION['userid']) ||
    !isset($_SESSION['login_at']) ||
    (time() - $_SESSION['login_at']) > $SESSION_TIMEOUT
) {
    session_unset();
    session_destroy();

    // Si es AJAX, retornar JSON 401
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Sesión expirada. Por favor, volvé a ingresar.']);
        exit;
    }

    // Si es vista normal, redirigir al login
    header('Location: /admin/login.php?expired=1');
    exit;
}

// Renovar timestamp para mantener sesión activa con uso
$_SESSION['login_at'] = time();
```

### D. Generación de Hash para Contraseñas

```php
// Script auxiliar de uso único para crear usuarios — jamás exponer en producción
$clave_plana  = 'ContraseñaSegura123';
$clave_hash   = password_hash($clave_plana, PASSWORD_BCRYPT, ['cost' => 12]);
// Guardar $clave_hash en la BD, nunca $clave_plana
```

---

## 3. Reglas de Seguridad

| Regla | Implementación |
|---|---|
| **Hash de contraseñas** | `password_hash($clave, PASSWORD_BCRYPT, ['cost' => 12])` |
| **Verificación** | Solo `password_verify($input, $hash)` — nunca comparar texto plano |
| **Session Fixation** | `session_regenerate_id(true)` inmediatamente después de autenticar |
| **Timeout de sesión** | 8 horas desde el último uso (`$_SESSION['login_at']`) |
| **Rate limiting básico** | `sleep(1)` en fallos de autenticación para mitigar fuerza bruta |
| **PDO obligatorio** | Nunca interpolar `$usuario` en el query directamente |
| **Log de intentos** | `log_event('AUTH_FAIL', ...)` en cada fallo |

---

## 4. Checklist Antes de Entregar

- [ ] ¿El hash usa `PASSWORD_BCRYPT` con `cost >= 12` (nunca MD5 ni SHA1)?
- [ ] ¿La verificación usa `password_verify()` (nunca comparación directa)?
- [ ] ¿Se llama a `session_regenerate_id(true)` después de autenticar?
- [ ] ¿El endpoint de login retorna JSON con `{status, data, message}`?
- [ ] ¿El formulario hace el submit via `fetch()` y nunca recarga la página?
- [ ] ¿`includes/security.php` es la primera línea de cada vista privada?
- [ ] ¿Los intentos fallidos se registran con `log_event('AUTH_FAIL', ...)`?
- [ ] ¿Hay `sleep(1)` en el flujo de error para mitigar fuerza bruta?
