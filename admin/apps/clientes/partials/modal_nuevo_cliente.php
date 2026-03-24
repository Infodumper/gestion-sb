<!-- Modal Nuevo Cliente -->
<div id="modalNuevoCliente" class="hidden fixed inset-0 z-[100] bg-indigo-950/40 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4">
    <div class="max-w-2xl w-full card-premium overflow-hidden animate-in fade-in zoom-in duration-300">
        <!-- Close Button -->
        <button type="button" onclick="closeClientModal()" class="absolute top-4 sm:top-6 right-4 sm:right-6 text-gray-400 hover:text-gray-600 z-10 transition text-3xl">&times;</button>

        <div class="bg-orange-50 px-4 py-6 sm:py-8 border-b border-orange-100 text-center">
            <h2 class="brand-title text-3xl sm:text-4xl mb-2 text-orange-950">Nuevo Cliente</h2>
            <p class="text-orange-600 font-bold text-xs sm:text-sm uppercase tracking-widest">Ficha de Alta Rápida</p>
        </div>

        <form id="formNuevoCliente" class="p-4 sm:p-8 space-y-4 sm:space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 text-left">
                <!-- Nombre -->
                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase mb-2">Nombre</label>
                    <input type="text" name="nombre" class="input-premium" placeholder="Ej. Ana (Opcional)">
                </div>
                <!-- Apellido -->
                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase mb-2">Apellido</label>
                    <input type="text" name="apellido" class="input-premium" placeholder="Ej. López (Opcional)">
                </div>
                
                <!-- Teléfono (Requerido) -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Teléfono *</label>
                    <input type="text" name="telefono" id="inputTelNuevo" placeholder="Ej: 2231122333" class="input-premium border-orange-200 focus:border-orange-500 focus:ring-orange-200" required>
                    <p id="telWarningNuevo" class="text-red-500 text-xs font-bold mt-1 hidden"></p>
                </div>
                <!-- DNI -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">DNI <span class="text-gray-400 font-normal normal-case">(Opcional)</span></label>
                    <input type="text" name="dni" class="input-premium" placeholder="Número de documento">
                </div>
                
                <!-- Cumpleaños -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Cumpleaños</label>
                    <div class="flex gap-4">
                        <select name="dia_nac" class="w-1/3 input-premium bg-white cursor-pointer">
                            <option value="">Día</option>
                            <?php for($i=1;$i<=31;$i++) echo "<option value='$i'>$i</option>"; ?>
                        </select>
                        <select name="mes_nac" class="flex-1 input-premium bg-white cursor-pointer">
                            <option value="">Mes</option>
                            <?php 
                            $meses = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
                            foreach($meses as $n=>$m) echo "<option value='$n'>$m</option>"; 
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Options -->
                <div class="md:col-span-2 flex flex-col space-y-4 pt-2">
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="Promociones" value="1" checked class="w-5 h-5 text-orange-500 rounded border-gray-300 focus:ring-orange-500">
                        <span class="ml-3 text-sm text-indigo-900 font-bold">Desea recibir promociones y novedades</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 sm:pt-6 border-t border-gray-100 mt-2">
                <button type="submit" id="btnGuardar" class="w-full btn-premium bg-indigo-900 hover:bg-orange-500 text-lg sm:text-xl shadow-xl transition-colors">
                    GUARDAR CLIENTE 📁
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validación dinámica de Teléfono duplicado
document.getElementById('inputTelNuevo').addEventListener('input', function(e) {
    let t = this.value.replace(/[^0-9]/g, '');
    let w = document.getElementById('telWarningNuevo');
    
    if (t.length > 6) {
        const isInsideApps = window.location.pathname.includes('apps/clientes');
        const ajaxPath = isInsideApps ? 'ajax_check_duplicate.php' : 'apps/clientes/ajax_check_duplicate.php';
        
        fetch(ajaxPath + '?telefono=' + t)
        .then(r => r.json())
        .then(d => {
            if (d.exists) {
                w.innerText = '⚠️ Ojo: Este teléfono ya lo tiene ' + d.nombre;
                w.classList.remove('hidden');
            } else {
                w.classList.add('hidden');
            }
        }).catch(e => console.error(e));
    } else {
        w.classList.add('hidden');
    }
});

function openClientModal() {
    document.getElementById('modalNuevoCliente').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeClientModal() {
    document.getElementById('modalNuevoCliente').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

document.getElementById('formNuevoCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerText = 'GUARDANDO... ⏳';

    const formData = new FormData(this);
    const isInsideApps = window.location.pathname.includes('apps/clientes');
    const ajaxPath = isInsideApps ? 'ajax_save_client.php' : 'apps/clientes/ajax_save_client.php';

    fetch(ajaxPath, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en el servidor');
        return response.json();
    })
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#f28d1a'
            }).then(() => {
                closeClientModal();
                this.reset();
                if (isInsideApps) location.reload();
            });
        } else {
            Swal.fire({
                title: 'Atención',
                text: data.message,
                icon: 'warning',
                confirmButtonColor: '#f28d1a'
            });
        }
        btn.disabled = false;
        btn.innerText = 'GUARDAR CLIENTE 📁';
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        btn.disabled = false;
        btn.innerText = 'GUARDAR CLIENTE 📁';
    });
});
</script>
