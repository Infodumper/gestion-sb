<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
require_once '../../../includes/utils.php';
header('Content-Type: text/html; charset=utf-8');
$fecha_hoy = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../styles/main.css?v=4.0">
    <style>
        .input-line {
            background-color: transparent;
            border: none;
            border-bottom: 2px solid #e2e8f0;
            width: 100%;
            padding: 4px 0;
            transition: all 0.3s;
        }
        .input-line:focus {
            outline: none;
            border-bottom: 2px solid var(--color-marca-principal);
            background-color: var(--color-fondo-pagina);
        }
        
        /* Mostrar flechas de input number (habilitadas a pedido del usuario) */
        input[type=number] { -moz-appearance: number-input; }

        .dropdown-item:hover { background-color: var(--color-fondo-pagina); }
        
        .details-row {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8">

    <div class="max-w-6xl mx-auto">
        <!-- Cabecera Estilo Pop-up (Subplaca) -->
        <!-- Cabecera Estilo Pop-up (Centralizada) -->
        <?php render_premium_header('Pedido'); ?>

        <div class="max-w-5xl mx-auto bg-white rounded-3xl p-4 sm:p-6 card-shadow border border-emerald-50">
            <!-- Formulario Info Base -->
        <div class="mb-2 px-4 sm:px-0">
            <div class="relative max-w-xl">
                <input type="text" id="cliente_busqueda" class="input-line font-bold text-xl" style="color: #000000;" placeholder="Cliente" autocomplete="off">
                <input type="hidden" id="cliente_id">
                <div id="lista_clientes" class="absolute z-50 w-full bg-white border border-gray-100 rounded-xl shadow-xl mt-1 hidden max-h-60 overflow-y-auto"></div>
            </div>
        </div>

        <!-- Lista de Productos (Grid Responsivo) -->
        <div class="mb-4">
            <!-- Headers Desktop -->
            <div class="hidden md:grid grid-cols-12 gap-4 text-xs font-bold text-emerald-900/30 uppercase tracking-widest border-b border-emerald-50 pb-2 mb-2 px-4">
                <div class="col-span-2">Cod.</div>
                <div class="col-span-3">Producto</div>
                <div class="col-span-1 text-center">Cant.</div>
                <div class="col-span-1 text-center">Stock</div>
                <div class="col-span-3 text-right">Precio</div>
                <div class="col-span-2 text-right pr-12">Subtotal</div>
            </div>

            <div id="tbody_items" class="space-y-2">
                <!-- Fila base / Card -->
                <div class="item-row group bg-transparent md:bg-transparent rounded-none md:rounded-3xl p-0 md:p-0 border-b md:border-transparent border-gray-100 pb-2 md:pb-0 transition-all">
                    <div class="flex flex-col md:grid md:grid-cols-12 gap-1 md:gap-6 items-center">
                        
                        <!-- Código (2 cols en desktop) -->
                        <div class="w-full md:col-span-2 px-4 md:px-0">
                            <input type="text" class="input-line font-bold text-xl md:text-base input-codigo" style="color: var(--color-marca-principal);" placeholder="Cod." autocomplete="off">
                        </div>
                        
                        <!-- Producto (3 cols en desktop) -->
                        <div class="w-full md:col-span-3 relative px-4 md:px-0">
                            <input type="text" class="input-line font-bold text-xl md:text-base input-servicio" style="color: #000000;" placeholder="Producto" autocomplete="off">
                            <input type="hidden" class="servicio-id">
                            <div class="lista-servicios absolute z-50 w-full bg-white border border-gray-100 rounded-2xl shadow-2xl mt-1 hidden max-h-60 overflow-y-auto"></div>
                        </div>

                        <!-- Bloque de Detalles (Inputs y Subtotal) -->
                        <div class="details-row w-full md:col-span-7 flex flex-col gap-2 items-end opacity-40 transition-all duration-500 px-4 md:px-0">
                            <!-- Fila Superior: Inputs -->
                            <div class="w-full flex flex-row items-center gap-2 md:gap-4">
                                <!-- Cantidad -->
                                <div class="w-16">
                                    <input type="number" class="input-line text-center font-bold input-cantidad text-xl" style="color: var(--color-marca-principal);" value="" min="0" placeholder="0">
                                </div>

                                <!-- Stock -->
                                <div class="w-20 flex flex-col items-center">
                                    <span class="text-[8px] text-emerald-900/40 font-bold uppercase -mb-1">Stock</span>
                                    <input type="text" readonly class="input-line text-center font-bold input-stock rounded-lg text-sm" style="color: var(--color-marca-principal); background: #f8fafc;" value="-" placeholder="St.">
                                </div>

                                <!-- Precio -->
                                <div class="flex-1 max-w-[120px]">
                                    <div class="flex items-center justify-center font-bold">
                                        <span class="mr-1 text-sm text-black">$</span>
                                        <input type="number" class="input-line text-left w-full input-precio" style="color: #000000;" step="0.01" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Fila Inferior: Subtotal y Acción -->
                            <div class="w-full flex justify-between items-center mt-1">
                                <div class="flex-1 flex flex-col items-start md:items-end pr-4">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Subtotal</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-3xl font-bold" style="color: var(--color-marca-principal);">$</span>
                                        <span class="font-bold text-3xl subtotal-display" style="color: var(--color-marca-principal);">0,00</span>
                                    </div>
                                </div>
                                <button onclick="eliminarFila(this)" class="text-gray-300 hover:text-red-500 transition-colors text-3xl">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Acciones -->
        <div class="flex flex-col sm:flex-row justify-between items-center bg-gray-50/50 sm:rounded-3xl p-4 sm:p-8 gap-4 mt-2 border-t sm:border-0 border-gray-100">
            <button onclick="agregarFila()" class="w-full sm:w-auto px-6 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-emerald-600 active:bg-gray-50 transition-all shadow-sm flex items-center justify-center gap-2 tracking-tight">
                <span>➕</span> AGREGAR PRODUCTO
            </button>
            <div class="flex flex-col items-center sm:items-end w-full sm:w-auto">
                <span class="text-4xl font-bold text-emerald-950" id="total_footer">$ 0,00</span>
            </div>
        </div>

        <div class="mt-4 px-4 pb-8 sm:px-0 sm:pb-0 text-center">
            <button onclick="guardarNota()" class="w-full sm:w-auto px-12 py-4 bg-[var(--color-marca-principal)] text-white rounded-xl text-lg font-bold shadow-lg hover:opacity-90 active:scale-95 transition-all">
                FINALIZAR PEDIDO
            </button>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize security check
        <?php check_auth(); ?>

        const cliInput = document.getElementById('cliente_busqueda');
        const cliId = document.getElementById('cliente_id');
        const cliList = document.getElementById('lista_clientes');
        let cliTimer;

        cliInput.addEventListener('input', function() {
            clearTimeout(cliTimer);
            const val = this.value.trim();
            if(val.length < 1) { cliList.classList.add('hidden'); return; }

            cliTimer = setTimeout(() => {
                fetch(`../clientes/ajax_buscar_clientes.php?term=${val}`)
                .then(r => r.json())
                .then(data => {
                    cliList.innerHTML = '';
                    if(data && Array.isArray(data) && data.length > 0) {
                        data.forEach(c => {
                            const d = document.createElement('div');
                            d.className = 'px-4 py-3 dropdown-item cursor-pointer text-sm font-medium text-emerald-950 border-b border-emerald-50 last:border-0';
                            d.textContent = c.label;
                            d.onclick = () => {
                                cliInput.value = c.label;
                                cliId.value = c.id;
                                cliList.classList.add('hidden');
                            };
                            cliList.appendChild(d);
                        });
                        cliList.classList.remove('hidden');
                    } else { 
                        if (data.error) console.error("Error buscando clientes:", data.error);
                        cliList.classList.add('hidden'); 
                    }
                })
                .catch(err => {
                    console.error("Error en fetch de clientes:", err);
                });
            }, 300);
        });

        // --- MANEJO DE TABLA Y PRODUCTOS ---
        function agregarFila() {
            const tbody = document.getElementById('tbody_items');
            const newRow = document.querySelector('.item-row').cloneNode(true);
            
            // Limpiar inputs
            newRow.querySelector('.input-codigo').value = '';
            newRow.querySelector('.input-servicio').value = '';
            newRow.querySelector('.servicio-id').value = '';
            newRow.querySelector('.input-cantidad').value = '';
            newRow.querySelector('.input-precio').value = '';
            newRow.querySelector('.input-stock').value = '-';
            newRow.querySelector('.input-stock').style.backgroundColor = '#f8fafc';
            newRow.querySelector('.input-stock').style.color = 'var(--color-marca-principal)';
            newRow.querySelector('.input-codigo').focus();
            
            tbody.appendChild(newRow);
            
            // Re-aplicar estado inicial para móviles
            const dRow = newRow.querySelector('.details-row');
            if (window.innerWidth < 768) {
                dRow.classList.add('opacity-40', 'scale-[0.98]', 'pointer-events-none');
            }

            initRowListeners(newRow);
        }

        function eliminarFila(btn) {
            const rows = document.querySelectorAll('.item-row');
            if(rows.length > 1) {
                btn.closest('.item-row').remove();
                calcularTotal();
            } else {
                Swal.fire('Atención', 'Al menos debe haber un producto cargado', 'info');
            }
        }

        function initRowListeners(row) {
            const sInp = row.querySelector('.input-servicio');
            const sList = row.querySelector('.lista-servicios');
            const sId = row.querySelector('.servicio-id');
            const qInp = row.querySelector('.input-cantidad');
            const pInp = row.querySelector('.input-precio');
            let sTimer;

            sInp.addEventListener('input', function() {
                clearTimeout(sTimer);
                const val = this.value.trim();
                if(val.length < 1) { 
                    sList.classList.add('hidden'); 
                    // Si se borra el nombre, atenuar detalles en móvil
                    if (window.innerWidth < 768 && !sId.value) {
                        row.querySelector('.details-row').classList.replace('opacity-100', 'opacity-40');
                        row.querySelector('.details-row').classList.add('pointer-events-none', 'scale-[0.98]');
                    }
                    return; 
                }

                sTimer = setTimeout(() => {
                    fetch(`ajax_buscar_servicio.php?term=${val}`)
                    .then(r => r.json())
                    .then(data => {
                        sList.innerHTML = '';
                        if(data && Array.isArray(data) && data.length > 0) {
                            data.forEach(s => {
                                const d = document.createElement('div');
                                d.className = 'px-4 py-4 dropdown-item cursor-pointer text-sm font-medium text-emerald-900 border-b border-emerald-50 last:border-0';
                                d.textContent = s.label;
                                d.onclick = () => {
                                    row.querySelector('.input-codigo').value = s.codigo || '';
                                    sInp.value = s.nombre;
                                    sId.value = s.id;
                                    pInp.value = s.precio;
                                    
                                    // Actualizar Stock Display
                                    const stockInp = row.querySelector('.input-stock');
                                    stockInp.value = s.stock;
                                    if (s.stock < 3) {
                                        stockInp.style.backgroundColor = '#fee2e2'; // Red-100
                                        stockInp.style.color = '#dc2626'; // Red-600
                                    } else {
                                        stockInp.style.backgroundColor = '#f8fafc';
                                        stockInp.style.color = 'var(--color-marca-principal)';
                                    }

                                    sList.classList.add('hidden');
                                    
                                    // Efecto de aparición en móvil
                                    const dRow = row.querySelector('.details-row');
                                    dRow.classList.remove('opacity-40', 'pointer-events-none', 'scale-[0.98]');
                                    dRow.classList.add('opacity-100', 'scale-100');
                                    
                                    qInp.focus();
                                    calcularRow(row);
                                };
                                sList.appendChild(d);
                            });
                            sList.classList.remove('hidden');
                        } else { 
                            if (data.error) {
                                console.error("Error buscando productos:", data.error);
                            }
                            sList.classList.add('hidden'); 
                        }
                    })
                    .catch(err => {
                        console.error("Error en fetch de productos:", err);
                    });
                }, 250);
            });

            // Permitir que si escribe manualmente también se active la fila
            sInp.addEventListener('blur', () => {
                if (sInp.value.trim().length > 2) {
                    const dRow = row.querySelector('.details-row');
                    dRow.classList.remove('opacity-40', 'pointer-events-none', 'scale-[0.98]');
                    dRow.classList.add('opacity-100', 'scale-100');
                }
            });

            [qInp, pInp].forEach(inp => {
                inp.addEventListener('input', () => {
                    calcularRow(row);
                    // Validar stock sobre la marcha
                    const currentStock = parseInt(row.querySelector('.input-stock').value) || 0;
                    const requested = parseInt(qInp.value) || 0;
                    if (requested > currentStock && currentStock > 0) {
                        row.querySelector('.input-stock').style.backgroundColor = '#fef3c7'; // Amber-100 para advertencia
                    } else if (currentStock < 3) {
                        row.querySelector('.input-stock').style.backgroundColor = '#fee2e2';
                    } else {
                        row.querySelector('.input-stock').style.backgroundColor = '#f8fafc';
                    }
                });
            });
        }

        function calcularRow(row) {
            const q = parseFloat(row.querySelector('.input-cantidad').value) || 0;
            const p = parseFloat(row.querySelector('.input-precio').value) || 0;
            const sub = q * p;
            
            // Separamos el formato para el display de 3xl
            const formatted = sub.toLocaleString('es-AR', {minimumFractionDigits: 2});
            
            row.querySelectorAll('.subtotal-display').forEach(el => {
                el.textContent = formatted;
            });
            calcularTotal();
        }

        function calcularTotal() {
            let t = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const q = parseFloat(row.querySelector('.input-cantidad').value) || 0;
                const p = parseFloat(row.querySelector('.input-precio').value) || 0;
                t += (q * p);
            });
            document.getElementById('total_footer').textContent = '$ ' + t.toLocaleString('es-AR', {minimumFractionDigits: 2});
        }

        function guardarNota() {
            if(!cliId.value) {
                Swal.fire('Error', 'Debe seleccionar un cliente de la lista', 'error');
                return;
            }
            
            const rows = document.querySelectorAll('.item-row');
            const items = [];
            let stockWarning = false;
            let warningProducts = [];

            rows.forEach(row => {
                const idS = row.querySelector('.servicio-id').value;
                const cant = parseInt(row.querySelector('.input-cantidad').value) || 0;
                const prec = row.querySelector('.input-precio').value;
                const stock = parseInt(row.querySelector('.input-stock').value) || 0;
                const nombre = row.querySelector('.input-servicio').value;
                
                if(idS && cant > 0) {
                    items.push({
                        id_servicio: idS,
                        cantidad: cant,
                        precio: prec
                    });
                    
                    if (cant > stock) {
                        stockWarning = true;
                        warningProducts.push(`${nombre} (Solicitado: ${cant}, Stock: ${stock})`);
                    }
                }
            });

            if(items.length === 0) {
                Swal.fire('Atención', 'Debe cargar al menos un producto válido', 'warning');
                return;
            }

            if (stockWarning) {
                Swal.fire({
                    title: 'Stock Insuficiente',
                    html: `Se está intentando comprar más de lo que hay en stock para:<br><br><b>${warningProducts.join('<br>')}</b><br><br>¿Desea continuar de todas formas?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, acepto como vendedor',
                    cancelButtonText: 'No, corregir',
                    confirmButtonColor: '#00a876',
                    cancelButtonColor: '#ef4444'
                }).then((result) => {
                    if (result.isConfirmed) {
                        realizarGuardado(items);
                    }
                });
            } else {
                realizarGuardado(items);
            }
        }

        function realizarGuardado(items) {
            const btn = document.querySelector('button[onclick="guardarNota()"]');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'GUARDANDO... ⏳';

            const formData = new FormData();
            formData.append('cliente_id', cliId.value);
            items.forEach((item, index) => {
                formData.append(`items[${index}][id_servicio]`, item.id_servicio);
                formData.append(`items[${index}][cantidad]`, item.cantidad);
                formData.append(`items[${index}][precio]`, item.precio);
            });

            fetch('ajax_guardar_nota.php', {
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
                        if (window.parent && window.parent.closeAppModal) {
                            window.parent.closeAppModal();
                        } else {
                            window.location.href = '../../../index.php';
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
                btn.innerText = originalText;
            });
        }

        // Inicializar la primera fila
        document.querySelectorAll('.item-row').forEach(initRowListeners);

        // Cerrar dropdowns al clickear fuera
        document.addEventListener('click', (e) => {
            if(!e.target.closest('.relative') && !e.target.closest('td')) {
                cliList.classList.add('hidden');
                document.querySelectorAll('.lista-servicios').forEach(l => l.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>
