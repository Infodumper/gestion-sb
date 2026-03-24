<!-- Modal Editar Cliente -->
<div id="modalEditarCliente" class="hidden fixed inset-0 z-[120] bg-indigo-950/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="max-w-2xl w-full card-premium overflow-hidden animate-in fade-in zoom-in duration-300">
        <!-- Close Button -->
        <button onclick="closeModal('modalEditarCliente')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 z-10 transition text-3xl">&times;</button>

        <div class="bg-indigo-50 px-4 sm:px-8 py-6 sm:py-10 border-b border-indigo-100 text-center">
            <h2 class="brand-title text-3xl sm:text-4xl mb-2 text-indigo-900">Editar Cliente</h2>
            <p class="text-indigo-600 font-medium tracking-widest uppercase text-[10px] sm:text-xs">Actualización de datos maestros</p>
        </div>

        <form id="formEditarCliente" class="p-6 sm:p-8 space-y-4 sm:space-y-6">
            <input type="hidden" name="id_cliente" id="edit_id_cliente">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-bold text-indigo-900 uppercase mb-2">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" class="input-premium" required>
                </div>
                <!-- Apellido -->
                <div>
                    <label class="block text-sm font-bold text-indigo-900 uppercase mb-2">Apellido</label>
                    <input type="text" name="apellido" id="edit_apellido" class="input-premium" required>
                </div>
                
                <!-- Teléfono -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" class="input-premium" required>
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
                        <input type="checkbox" name="Promociones" id="edit_promociones" value="1" class="w-5 h-5 text-indigo-600 rounded">
                        <span class="ml-3 text-sm text-indigo-900 font-bold">Recibir promociones</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="estado" id="edit_estado" value="1" class="w-5 h-5 text-indigo-600 rounded">
                        <span class="ml-3 text-sm text-indigo-900 font-bold">Cliente Activo</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 sm:pt-8 border-t border-gray-100">
                <button type="submit" id="btnActualizar" class="w-full btn-premium text-lg sm:text-xl">
                    SINCRONIZAR CAMBIOS 🔄
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditClient(id) {
    const isInsideApps = window.location.pathname.includes('apps/clientes');
    const ajaxPath = isInsideApps ? 'ajax_get_client_card.php' : 'apps/clientes/ajax_get_client_card.php';

    fetch(ajaxPath + '?id=' + id)
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            const c = data.client;
            document.getElementById('edit_id_cliente').value = c.IdCliente;
            document.getElementById('edit_nombre').value = c.Nombre;
            document.getElementById('edit_apellido').value = c.Apellido;
            document.getElementById('edit_telefono').value = c.Telefono;
            document.getElementById('edit_dni').value = c.Dni || '';
            
            if(c.FechaNac) {
                const parts = c.FechaNac.split('-'); // YYYY-MM-DD
                document.getElementById('edit_dia_nac').value = parseInt(parts[2]);
                document.getElementById('edit_mes_nac').value = parseInt(parts[1]);
            } else {
                document.getElementById('edit_dia_nac').value = '';
                document.getElementById('edit_mes_nac').value = '';
            }

            document.getElementById('edit_promociones').checked = (c.Promociones == 1);
            document.getElementById('edit_estado').checked = (c.Estado == 1);

            document.getElementById('modalEditarCliente').classList.remove('hidden');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'No se pudo cargar la ficha del cliente', 'error');
    });
}

document.getElementById('formEditarCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnActualizar');
    btn.disabled = true;
    btn.innerHTML = 'SINCRONIZANDO... ⏳';

    const formData = new FormData(this);
    const isInsideApps = window.location.pathname.includes('apps/clientes');
    const ajaxPath = isInsideApps ? 'ajax_save_client.php' : 'apps/clientes/ajax_save_client.php';

    fetch(ajaxPath, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: '¡Actualizado!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#f28d1a'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Atención',
                text: data.message,
                icon: 'warning',
                confirmButtonColor: '#f28d1a'
            });
            btn.disabled = false;
            btn.innerHTML = 'SINCRONIZAR CAMBIOS 🔄';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo guardar los cambios', 'error');
        btn.disabled = false;
        btn.innerHTML = 'SINCRONIZAR CAMBIOS 🔄';
    });
});
</script>
