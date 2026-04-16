---
name: premium_attention
description: Especialista en automatización de fidelización CRM y envíos WhatsApp (Cumpleaños, habituales, campañas).
directiva: directives/build_atencion.md
---

# Skill: premium_attention

## 1. Rol y Responsabilidad

Eres el agente **premium_attention**. Tu dominio es el CRM relacional. Conviertes datos fríos (fechas de nacimiento, historial de compras) en **acciones relacionales de alto impacto** vía WhatsApp.

Leer directiva antes de ejecutar: `directives/build_atencion.md`.

---

## 2. Patrones de Código Obligatorios

### A. Consulta de Cumpleaños del Mes (`ajax_get_cumples.php`)

```php
<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $hoy    = date('Y-m-d');
    $mesHoy = date('m');
    $diaHoy = date('d');

    $stmt = $pdo->prepare("
        SELECT
            c.IdCliente,
            c.NombreCompleto,
            c.Telefono,
            c.FechaNac,
            DAY(c.FechaNac)  AS dia_nac,
            MONTH(c.FechaNac) AS mes_nac,
            -- Saber si YA fue contactado HOY
            (
                SELECT COUNT(*) FROM ContactosWhatsapp
                WHERE IdCliente = c.IdCliente
                  AND Tipo = 'cumple'
                  AND DATE(FechaContacto) = CURDATE()
            ) AS contactado_hoy
        FROM Clientes c
        WHERE MONTH(c.FechaNac) = :mes
          AND c.Estado = 1
        ORDER BY DAY(c.FechaNac) ASC
    ");
    $stmt->execute([':mes' => $mesHoy]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Marcar si es cumple hoy
    foreach ($clientes as &$c) {
        $c['es_cumple_hoy'] = ($c['dia_nac'] == $diaHoy) ? '1' : '0';
        // Limpiar teléfono para WhatsApp
        $c['telefono_wa'] = preg_replace('/\D/', '', $c['Telefono']);
    }

    echo json_encode(['status' => 'ok', 'data' => $clientes]);
} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al cargar cumpleaños']);
}
exit;
```

### B. Registrar Contacto WhatsApp (`ajax_mark_contacted.php`)

```php
<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$input     = json_decode(file_get_contents('php://input'), true);
$idCliente = intval($input['id_cliente'] ?? 0);
$tipo      = $input['tipo'] ?? ''; // 'cumple' | 'habitual' | 'mensual'

$tipos_validos = ['cumple', 'habitual', 'mensual'];
if ($idCliente <= 0 || !in_array($tipo, $tipos_validos)) {
    echo json_encode(['status' => 'error', 'message' => 'Parámetros inválidos']);
    exit;
}

try {
    // Verificar si ya fue contactado en el período correcto
    if ($tipo === 'cumple') {
        // Solo 1 por día
        $check = $pdo->prepare("
            SELECT COUNT(*) FROM ContactosWhatsapp
            WHERE IdCliente = :id AND Tipo = 'cumple' AND DATE(FechaContacto) = CURDATE()
        ");
    } else {
        // Solo 1 por mes (habitual, mensual)
        $check = $pdo->prepare("
            SELECT COUNT(*) FROM ContactosWhatsapp
            WHERE IdCliente = :id AND Tipo = :tipo
              AND MONTH(FechaContacto) = MONTH(NOW()) AND YEAR(FechaContacto) = YEAR(NOW())
        ");
    }
    $check->execute([':id' => $idCliente, ':tipo' => $tipo]);

    if ($tipo !== 'cumple') {
        $check->execute([':id' => $idCliente, ':tipo' => $tipo]);
    } else {
        $check->execute([':id' => $idCliente]);
    }

    if ($check->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Ya fue contactado en este período']);
        exit;
    }

    // Registrar el contacto
    $stmt = $pdo->prepare("
        INSERT INTO ContactosWhatsapp (IdCliente, Tipo, CreadoPor) VALUES (:id, :tipo, :uid)
    ");
    $stmt->execute([':id' => $idCliente, ':tipo' => $tipo, ':uid' => $_SESSION['userid']]);

    log_event('INSERT', "Contacto WA tipo=$tipo para IdCliente=$idCliente", __FILE__);
    echo json_encode(['status' => 'ok', 'message' => 'Contacto registrado']);

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar contacto']);
}
exit;
```

### C. Render de Subplaca de Cumpleaños (JavaScript)

```javascript
function renderCumpleSubplaca(c) {
  const esCumpleHoy   = c.es_cumple_hoy === '1';
  const yaContactado  = parseInt(c.contactado_hoy) > 0;
  const numeroWA      = c.telefono_wa;
  const msgCumple     = encodeURIComponent(`¡Hola ${c.NombreCompleto.split(' ')[0]}! 🎂 ¡Feliz cumpleaños! Desde Gestion SB te deseamos un hermoso día 🎉`);

  // Estado del botón WA: verde iluminado solo si es cumple HOY y no fue contactado
  const puedeContactar = esCumpleHoy && !yaContactado;
  const btnColor       = puedeContactar ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed';

  return `
    <div class="bg-white rounded-[1.5rem] shadow-sm p-4 flex items-center gap-3 ${esCumpleHoy ? 'ring-2 ring-emerald-200' : ''}">
      <!-- Avatar -->
      <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-sm"
           style="background: linear-gradient(135deg, ${esCumpleHoy ? '#059669, #10b981' : '#6366f1, #8b5cf6'})">
        ${c.NombreCompleto.split(' ').map(p => p[0]).slice(0,2).join('')}
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <h3 class="font-semibold text-gray-800 truncate text-sm">${c.NombreCompleto}</h3>
        <p class="text-xs text-gray-500">
          ${esCumpleHoy
            ? '<span class="text-emerald-600 font-medium">🎂 ¡Cumpleaños hoy!</span>'
            : `Cumple el ${String(c.dia_nac).padStart(2,'0')}/${String(c.mes_nac).padStart(2,'0')}`
          }
        </p>
      </div>

      <!-- Botón WhatsApp -->
      <button
        onclick="${puedeContactar ? `enviarWACumple(${c.IdCliente}, '${numeroWA}', '${msgCumple}', this)` : `mostrarAlertaYaContactado()`}"
        class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center transition-colors ${btnColor}"
        ${yaContactado ? 'title="Ya enviado hoy"' : 'title="Enviar saludo de cumpleaños"'}
      >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
      </button>
    </div>
  `;
}

async function enviarWACumple(idCliente, numeroWA, msgEncoded, btnEl) {
  // 1. Registrar el contacto en el backend
  try {
    const res  = await fetch('ajax_mark_contacted.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_cliente: idCliente, tipo: 'cumple' })
    });
    const json = await res.json();
    if (json.status !== 'ok') {
      Swal.fire({ icon: 'warning', title: 'Atención', text: json.message });
      return;
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar el contacto' });
    return;
  }

  // 2. Abrir WhatsApp
  window.open(`https://wa.me/54${numeroWA}?text=${msgEncoded}`, '_blank');

  // 3. Actualizar UI
  btnEl.className = btnEl.className.replace('bg-emerald-500 hover:bg-emerald-600 text-white', 'bg-gray-100 text-gray-400 cursor-not-allowed');
  btnEl.onclick = () => mostrarAlertaYaContactado();
}

function mostrarAlertaYaContactado() {
  Swal.fire({
    icon: 'info',
    title: 'Ya contactada',
    text: 'Ya enviaste el saludo a esta persona hoy.',
    confirmButtonText: 'Entendido'
  });
}
```

---

## 3. Reglas de Negocio CRM

| Acción | Frecuencia permitida | Control en BD |
|---|---|---|
| Saludo de Cumpleaños | 1 vez por día (el día exacto) | `DATE(FechaContacto) = CURDATE()` |
| Promo Mensual | 1 vez por mes | `MONTH() + YEAR()` iguales |
| Check Habitual | 1 vez por mes | `MONTH() + YEAR()` iguales |

---

## 4. Reglas de WhatsApp

| Regla | Implementación |
|---|---|
| **Limpieza del número** | `preg_replace('/\D/', '', $tel)` antes de insertar en URL |
| **Código de país** | Prefijo estándar `54` (Argentina) concatenado en el href |
| **Encoding del mensaje** | `encodeURIComponent()` en JS / `urlencode()` en PHP |
| **Apertura sin bloqueo** | `window.open(url, '_blank')` — nunca `window.location.href` |

---

## 5. Checklist Antes de Entregar

- [ ] ¿El teléfono fue limpiado con `preg_replace('/\D/', '', $tel)` antes de armar el link WA?
- [ ] ¿El mensaje usa `encodeURIComponent()` para no romper la URL?
- [ ] ¿Se verifica en el backend si ya fue contactado en el período antes de registrar?
- [ ] ¿El botón WA se deshabilita visualmente después del primer envío?
- [ ] ¿Los tipos de contacto están validados contra el array `['cumple', 'habitual', 'mensual']`?
- [ ] ¿Los avatars usan iniciales con gradiente (verde para cumpleaños hoy, violeta para el resto)?
- [ ] ¿Los cumpleaños del día tienen un ring/borde visual que los destaca?
