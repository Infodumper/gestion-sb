<!-- Modal Editar Cliente -->
<div id="modalEditarCliente" class="hidden fixed inset-0 z-[300] bg-emerald-950/40 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="max-w-2xl w-full card-premium overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="modal-header-premium">
            <h2 class="modal-title-premium italic">Editar Cliente</h2>
            <button type="button" onclick="closeModal('modalEditarCliente')" class="btn-close-premium group" title="Cerrar">
                <svg class="w-6 h-6 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formEditarCliente" class="modal-form-container p-6 sm:p-8 space-y-4 sm:space-y-6">
            <input type="hidden" name="id_cliente" id="edit_id_cliente">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-bold text-emerald-950 uppercase mb-2">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" class="input-premium" required>
                </div>
                <!-- Apellido -->
                <div>
                    <label class="block text-sm font-bold text-emerald-950 uppercase mb-2">Apellido</label>
                    <input type="text" name="apellido" id="edit_apellido" class="input-premium" required>
                </div>
                
                <!-- Teléfono -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" class="input-premium">
                </div>
                <!-- DNI -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">DNI</label>
                    <input type="text" name="dni" id="edit_dni" class="input-premium">
                </div>
                
                <!-- Cumpleaños -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Cumpleaños</label>
                    <div class="flex gap-4">
                        <select name="dia_nac" id="edit_dia_nac" class="w-1/3 input-premium bg-white cursor-pointer">
                            <option value="">Día</option>
                            <?php for($i=1;$i<=31;$i++) echo "<option value='$i'>$i</option>"; ?>
                        </select>
                        <select name="mes_nac" id="edit_mes_nac" class="flex-1 input-premium bg-white cursor-pointer">
                            <option value="">Mes</option>
                            <?php 
                            $meses = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
                            foreach($meses as $n=>$m) echo "<option value='$n'>$m</option>"; 
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Options -->
                <div class="md:col-span-2 flex justify-between pt-4">
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="Promociones" id="edit_promociones" value="1" class="w-5 h-5 text-emerald-600 rounded">
                        <span class="ml-3 text-sm text-emerald-900 font-bold">Recibir promociones</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="estado" id="edit_estado" value="1" class="w-5 h-5 text-emerald-600 rounded">
                        <span class="ml-3 text-sm text-emerald-900 font-bold">Cliente Activo</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 sm:pt-8 border-t border-gray-100 mt-2">
                <button type="submit" id="btnActualizar" class="w-full btn-premium flex items-center justify-center gap-3 text-lg py-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>SINCRONIZAR CAMBIOS</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Modal Editar — funciones públicas ─────────────────────────────────────────

function cerrarModalEditar() {
    document.getElementById('modalEditarCliente').classList.add('hidden');
    document.body.style.overflow = '';
}

// Alias de compatibilidad
function openEditClient(id) { abrirModalEditar(id); }

async function abrirModalEditar(id) {
    try {
        const res  = await fetch('ajax_get_client_card.php?id=' + id);
        const json = await res.json();
        if (json.status !== 'ok') throw new Error(json.message);

        const c = json.data.client;
        document.getElementById('edit_id_cliente').value     = c.IdCliente;
        document.getElementById('edit_nombre').value          = c.Nombre;
        document.getElementById('edit_apellido').value        = c.Apellido;
        document.getElementById('edit_telefono').value        = c.Telefono;
        document.getElementById('edit_dni').value             = c.Dni || '';
        document.getElementById('edit_promociones').checked   = (c.Promociones == 1);
        document.getElementById('edit_estado').checked        = (c.Estado == 1);

        if (c.FechaNac) {
            const parts = c.FechaNac.split('-'); // YYYY-MM-DD
            document.getElementById('edit_dia_nac').value = parseInt(parts[2]);
            document.getElementById('edit_mes_nac').value = parseInt(parts[1]);
        } else {
            document.getElementById('edit_dia_nac').value = '';
            document.getElementById('edit_mes_nac').value = '';
        }

        document.getElementById('modalEditarCliente').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el cliente: ' + err.message });
    }
}

document.getElementById('formEditarCliente').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnActualizar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res  = await fetch('ajax_save_client.php', { method: 'POST', body: new FormData(this) });
        const json = await res.json();

        if (json.status === 'ok') {
            await Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: json.message,
                timer: 1800,
                showConfirmButton: false,
            });
            cerrarModalEditar();
            location.reload();
        } else {
            Swal.fire({ icon: 'warning', title: 'Atención', text: json.message, confirmButtonText: 'Entendido' });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo guardar los cambios' });
    } finally {
        btn.disabled = false;
        btn.textContent = 'Guardar cambios';
    }
});
</script>
