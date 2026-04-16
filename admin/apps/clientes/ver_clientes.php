<?php
/**
 * Vista: Directorio de Clientes
 * Módulo: Clientes — Gestion SB
 * Requisito: security.php como primera línea (check_auth() se ejecuta automáticamente)
 */

require_once '../../../includes/security.php';
require_once '../../../includes/db.php';
require_once '../../../includes/utils.php';
header('Content-Type: text/html; charset=utf-8');

// Carga inicial del servidor — primera página sin filtro
// (La búsqueda/filtrado posterior se hace vía AJAX desde el cliente)
try {
    $stmt = $pdo->prepare("
        SELECT IdCliente, Nombre, Apellido, Telefono, Estado
        FROM clientes
        WHERE Estado = 1
        ORDER BY Apellido ASC, Nombre ASC
        LIMIT 30
    ");
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM clientes WHERE Estado = 1");
    $totalClientes = (int)$totalStmt->fetchColumn();

} catch (PDOException $e) {
    $clientes = [];
    $totalClientes = 0;
    log_event('ERROR', $e->getMessage(), __FILE__);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Clientes — Gestion SB</title>
    <link rel="stylesheet" href="../../../styles/colores.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="placa-maestra">

    <!-- ── Cabecera del módulo ── -->
    <header class="module-header pt-2 pb-1">
        <div>
            <h1 class="module-title">Clientes</h1>
            <p class="text-sm" style="color: var(--color-text-light)">
                <?= $totalClientes ?> cliente<?= $totalClientes !== 1 ? 's' : '' ?> activo<?= $totalClientes !== 1 ? 's' : '' ?>
            </p>
        </div>
        <button onclick="abrirModalNuevo()" class="btn-primary" id="btn-nuevo-cliente">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo
        </button>
    </header>

    <!-- ── Buscador AJAX ── -->
    <div class="mb-4 relative">
        <div class="input-search">
            <svg class="w-5 h-5 absolute left-3" style="color: var(--color-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="buscador-clientes" placeholder="Buscar por nombre, apellido o teléfono..."
                   autocomplete="off" spellcheck="false">
            <button id="btn-limpiar-busqueda" onclick="limpiarBusqueda()"
                    class="hidden w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center transition-colors"
                    style="background: var(--color-primary-light); color: var(--color-primary)">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- ── Listado de Subplacas ── -->
    <div id="lista-clientes" class="space-y-2">
        <?php if (empty($clientes)): ?>
            <div class="state-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p>No hay clientes registrados aún</p>
            </div>
        <?php else: ?>
            <?php foreach ($clientes as $c):
                $iniciales = strtoupper(substr($c['Nombre'] ?? '', 0, 1) . substr($c['Apellido'] ?? '', 0, 1));
                $gradient  = get_gradient_avatar($c['IdCliente']);
                $telWA     = preg_replace('/\D/', '', $c['Telefono'] ?? '');
            ?>
            <div class="subplaca-adn group" data-id="<?= $c['IdCliente'] ?>">
                <div class="subplaca-acento" style="background: var(--color-primary)"></div>
                <div class="subplaca-cuerpo">
                    <!-- Avatar + Info -->
                    <div class="flex items-center gap-3 cursor-pointer flex-1 min-w-0"
                         onclick="abrirFichaCliente(<?= $c['IdCliente'] ?>)">
                        <div class="avatar bg-gradient-to-br <?= $gradient ?>">
                            <?= $iniciales ?>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold truncate text-sm" style="color: var(--color-text)">
                                <?= s($c['Apellido']) ?> <?= s($c['Nombre']) ?>
                            </h3>
                            <p class="text-xs font-medium" style="color: var(--color-text-light)">
                                <?= s($c['Telefono']) ?>
                            </p>
                        </div>
                    </div>
                    <!-- Acciones rápidas -->
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button onclick="event.stopPropagation(); abrirModalEditar(<?= $c['IdCliente'] ?>)"
                                class="btn-icon btn-icon-edit" title="Editar cliente"
                                id="btn-edit-<?= $c['IdCliente'] ?>">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                        <a href="https://wa.me/549<?= $telWA ?>" target="_blank"
                           onclick="event.stopPropagation()"
                           class="btn-icon btn-icon-wa" title="Enviar WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.113 1.528 5.84L.057 23.999l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.865 0-3.605-.507-5.102-1.388l-.366-.217-3.737.98.997-3.648-.239-.376A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div><!-- /.placa-maestra -->

<!-- ── Modal Ficha 360° ────────────────────────────────────────────────────── -->
<div id="modal-ficha" class="modal-overlay hidden">
    <div class="modal-container">
        <div class="modal-header">
            <h2 class="modal-title" id="ficha-nombre">Cargando...</h2>
            <button onclick="cerrarFicha()" class="modal-close" title="Cerrar">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <!-- Avatar e info principal -->
            <div class="flex items-center gap-4 mb-5 p-4 rounded-2xl" style="background: var(--color-primary-light)">
                <div class="avatar avatar-lg" style="background: var(--gradient-primary)" id="ficha-avatar">--</div>
                <div>
                    <p class="text-sm font-medium" style="color: var(--color-text-light)">Teléfono</p>
                    <p class="font-bold" id="ficha-telefono" style="color: var(--color-text)">—</p>
                    <p class="text-xs mt-1" id="ficha-cumple" style="color: var(--color-text-light)"></p>
                </div>
            </div>

            <!-- Métricas -->
            <div class="grid grid-cols-3 gap-2 mb-5">
                <div class="text-center p-3 rounded-xl" style="background: var(--color-bg)">
                    <p class="text-xl font-bold" style="color: var(--color-primary)" id="ficha-total-pedidos">—</p>
                    <p class="text-xs" style="color: var(--color-muted)">Pedidos</p>
                </div>
                <div class="text-center p-3 rounded-xl" style="background: var(--color-bg)">
                    <p class="text-lg font-bold" style="color: var(--color-text)" id="ficha-monto-total">—</p>
                    <p class="text-xs" style="color: var(--color-muted)">Total</p>
                </div>
                <div class="text-center p-3 rounded-xl" style="background: var(--color-bg)">
                    <p class="text-lg font-bold" style="color: var(--color-text)" id="ficha-ticket">—</p>
                    <p class="text-xs" style="color: var(--color-muted)">Ticket prom.</p>
                </div>
            </div>

            <!-- Historial de pedidos -->
            <h4 class="text-xs font-bold uppercase tracking-widest mb-3" style="color: var(--color-muted)">Últimas compras</h4>
            <div id="ficha-historial" class="space-y-2">
                <div class="state-loading"><div class="spinner"></div></div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="abrirModalEditarDesdeDetalle()" class="btn-primary flex-1">Editar ficha</button>
            <a id="ficha-wa-link" href="#" target="_blank" class="btn-primary flex-1"
               style="background: #16a34a; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.113 1.528 5.84L.057 23.999l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.865 0-3.605-.507-5.102-1.388l-.366-.217-3.737.98.997-3.648-.239-.376A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                </svg>
                WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- ── Modales Nuevo / Editar ──────────────────────────────────────────────── -->
<?php include 'partials/modal_nuevo_cliente.php'; ?>
<?php include 'partials/modal_editar_cliente.php'; ?>

<!-- ── JavaScript ─────────────────────────────────────────────────────────── -->
<script>
// ── Estado global ─────────────────────────────────────────────────────────────
let fichaClienteActual = null;
let debounceTimer      = null;

// ── Buscador AJAX con debounce ────────────────────────────────────────────────
document.getElementById('buscador-clientes').addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    document.getElementById('btn-limpiar-busqueda').classList.toggle('hidden', q.length === 0);

    debounceTimer = setTimeout(() => buscarClientes(q), 320);
});

async function buscarClientes(q) {
    const lista = document.getElementById('lista-clientes');

    if (q.length === 0) {
        location.reload(); // Volver al listado inicial del servidor
        return;
    }
    if (q.length < 2) return;

    lista.innerHTML = '<div class="state-loading"><div class="spinner"></div></div>';

    try {
        const res  = await fetch(`ajax_buscar_clientes.php?q=${encodeURIComponent(q)}`);
        const json = await res.json();

        if (json.status !== 'ok') throw new Error(json.message);
        renderListaClientes(json.data);

    } catch (err) {
        lista.innerHTML = `<div class="state-empty"><p>Error al buscar: ${err.message}</p></div>`;
    }
}

function limpiarBusqueda() {
    document.getElementById('buscador-clientes').value = '';
    document.getElementById('btn-limpiar-busqueda').classList.add('hidden');
    location.reload();
}

function renderListaClientes(clientes) {
    const lista = document.getElementById('lista-clientes');

    if (!clientes || clientes.length === 0) {
        lista.innerHTML = `
          <div class="state-empty">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Sin resultados para esa búsqueda</p>
          </div>`;
        return;
    }

    lista.innerHTML = clientes.map(c => {
        const iniciales = c.label.split(' ').map(p => p[0]).slice(0, 2).join('');
        return `
          <div class="subplaca-adn group">
            <div class="subplaca-acento" style="background: var(--color-primary)"></div>
            <div class="subplaca-cuerpo">
              <div class="flex items-center gap-3 cursor-pointer flex-1 min-w-0"
                   onclick="abrirFichaCliente(${c.id})">
                <div class="avatar" style="background: var(--gradient-primary)">${iniciales.toUpperCase()}</div>
                <div class="min-w-0">
                  <h3 class="font-semibold truncate text-sm" style="color: var(--color-text)">${c.label}</h3>
                  <p class="text-xs" style="color: var(--color-text-light)">${c.telefono}</p>
                </div>
              </div>
              <div class="flex items-center gap-1.5 flex-shrink-0">
                <button onclick="event.stopPropagation(); abrirModalEditar(${c.id})"
                        class="btn-icon btn-icon-edit" title="Editar">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                  </svg>
                </button>
                <a href="https://wa.me/549${c.telefono.replace(/\D/g,'')}" target="_blank"
                   onclick="event.stopPropagation()" class="btn-icon btn-icon-wa" title="WhatsApp">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.113 1.528 5.84L.057 23.999l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.865 0-3.605-.507-5.102-1.388l-.366-.217-3.737.98.997-3.648-.239-.376A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>`;
    }).join('');
}

// ── Ficha 360° ────────────────────────────────────────────────────────────────
async function abrirFichaCliente(id) {
    fichaClienteActual = id;
    document.getElementById('modal-ficha').classList.remove('hidden');
    document.getElementById('ficha-historial').innerHTML =
        '<div class="state-loading"><div class="spinner"></div></div>';

    try {
        const res  = await fetch(`ajax_get_client_card.php?id=${id}`);
        const json = await res.json();
        if (json.status !== 'ok') throw new Error(json.message);

        const { client, history, metricas } = json.data;

        document.getElementById('ficha-nombre').textContent  = client.NombreCompleto;
        document.getElementById('ficha-avatar').textContent  = client.Iniciales;
        document.getElementById('ficha-telefono').textContent = client.Telefono || '—';
        document.getElementById('ficha-cumple').textContent  = client.FechaNacFormat
            ? `🎂 ${client.FechaNacFormat}` : '';
        document.getElementById('ficha-wa-link').href =
            `https://wa.me/549${client.TelefonoWA}`;

        // Métricas
        document.getElementById('ficha-total-pedidos').textContent = metricas.total_pedidos ?? '—';
        document.getElementById('ficha-monto-total').textContent   = metricas.monto_total ?? '—';
        document.getElementById('ficha-ticket').textContent        = metricas.ticket_promedio ?? '—';

        // Historial
        const histDiv = document.getElementById('ficha-historial');
        if (history.length === 0) {
            histDiv.innerHTML = '<p class="text-center text-sm py-4" style="color: var(--color-muted)">Sin historial de compras</p>';
        } else {
            const estadosLabel = ['', 'Pendiente', 'Pagado', 'Entregado'];
            const estadosBadge = ['', 'badge-pendiente', 'badge-pagado', 'badge-entregado'];
            histDiv.innerHTML = history.map(h => `
              <div class="subplaca flex items-center justify-between !py-2">
                <span class="font-bold text-sm" style="color: var(--color-text)">#${h.id}</span>
                <span class="text-xs" style="color: var(--color-muted)">${h.fecha}</span>
                <span class="badge ${estadosBadge[h.estado] || 'badge-inactivo'}">${estadosLabel[h.estado] || '—'}</span>
                <span class="font-bold text-sm" style="color: var(--color-primary)">${h.total}</span>
              </div>`).join('');
        }

    } catch (err) {
        document.getElementById('ficha-historial').innerHTML =
            `<p class="text-center py-4 text-sm" style="color: var(--color-error)">Error: ${err.message}</p>`;
    }
}

function cerrarFicha() {
    document.getElementById('modal-ficha').classList.add('hidden');
}

function abrirModalEditarDesdeDetalle() {
    cerrarFicha();
    if (fichaClienteActual) abrirModalEditar(fichaClienteActual);
}

// ── Cerrar modal con Escape ────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        cerrarFicha();
        cerrarModalNuevo?.();
        cerrarModalEditar?.();
    }
});

// ── Cerrar modal al click en el overlay ───────────────────────────────────────
document.getElementById('modal-ficha').addEventListener('click', function(e) {
    if (e.target === this) cerrarFicha();
});
</script>

</body>
</html>
