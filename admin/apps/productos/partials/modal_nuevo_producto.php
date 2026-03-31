<!-- Modal Nuevo Producto -->
<div id="modalProd" class="hidden fixed inset-0 z-[300] bg-emerald-950/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 id="modalTitle" class="brand-title text-2xl text-emerald-900 italic">Nuevo Producto</h2>
            <button onclick="closeModal()" class="text-3xl text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="formNuevoProducto" class="p-8 space-y-4">
            <input type="hidden" name="id" id="prod_id" value="0">
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Código de Producto</label>
                <input type="text" name="codigo" id="prod_codigo" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" placeholder="Ej: CRAv01">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-bold">Nombre del Producto</label>
                <input type="text" name="nombre" id="prod_nombre" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-bold">Descripción</label>
                <textarea name="descripcion" id="prod_descripcion" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none h-20"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-bold">Precio</label>
                    <input type="number" step="0.01" name="precio" id="prod_precio" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-bold">Stock Inicial</label>
                    <input type="number" name="stock" id="prod_stock" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-bold">Proveedor</label>
                <select name="id_proveedor" id="prod_id_proveedor" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all appearance-none">
                    <option value="">Seleccionar Proveedor...</option>
                    <?php 
                    // Obtener lista de proveedores para el select
                    if (!isset($proveedores)) {
                        require_once(realpath(__DIR__ . '/../../../../includes/db.php'));
                        $proveedores = $pdo->query("SELECT IdProveedor, NombreComercial FROM proveedores WHERE Estado = 1 ORDER BY NombreComercial ASC")->fetchAll();
                    }
                    foreach($proveedores as $prov): ?>
                        <option value="<?= $prov['IdProveedor'] ?>"><?= htmlspecialchars($prov['NombreComercial']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="pt-4">
                <button type="submit" id="btnGuardarProd" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-bold shadow-lg hover:opacity-90 active:scale-95 transition-all uppercase tracking-widest">
                    Guardar Producto 📁
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').innerText = 'Nuevo Producto';
        document.getElementById('prod_id').value = '0';
        document.getElementById('prod_codigo').value = '';
        document.getElementById('prod_nombre').value = '';
        document.getElementById('prod_descripcion').value = '';
        document.getElementById('prod_precio').value = '';
        document.getElementById('prod_stock').value = '0';
        document.getElementById('prod_id_proveedor').value = '';
        document.getElementById('modalProd').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalProd').classList.add('hidden');
    }

    document.getElementById('formNuevoProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarProd');
        btn.disabled = true;
        btn.innerText = 'GUARDANDO... ⏳';

        const formData = new FormData(this);
        const isInsideApps = window.location.pathname.includes('apps/productos');
        const ajaxPath = isInsideApps ? 'ajax_save_product.php' : '../productos/ajax_save_product.php';

        fetch(ajaxPath, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#00a876'
                }).then(() => {
                    closeModal();
                    this.reset();
                    // Si estamos en stock.php, recargar. Si estamos en nota_trabajo, no recargar obligatoriamente.
                    if (window.location.pathname.includes('stock.php')) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo comunicar con el servidor', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'Guardar Producto 📁';
        });
    });
</script>
