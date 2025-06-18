<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #28a745;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Sistema de Ventas - Registro y Gestión de Transacciones!</h5>
                    <h4 class="text-center mb-2 text-success">Gestión de Ventas de Celulares</h4>
                </div>

                <div class="row">
                    <!-- Panel Izquierdo: Selección de Cliente e Inventario -->
                    <div class="col-lg-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-person-plus me-2"></i>Datos de la Venta</h6>
                            </div>
                            <div class="card-body">
                                <form id="FormVenta">
                                    <input type="hidden" id="venta_id" name="venta_id">
                                    
                                    <!-- Selección de Cliente -->
                                    <div class="mb-3">
                                        <label for="cliente_id" class="form-label">Cliente</label>
                                        <select class="form-select" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccione un cliente</option>
                                        </select>
                                    </div>

                                    <!-- Información del Cliente Seleccionado -->
                                    <div class="mb-3" id="info-cliente" style="display: none;">
                                        <div class="alert alert-info">
                                            <small>
                                                <strong>Cliente:</strong> <span id="cliente-nombre"></span><br>
                                                <strong>NIT:</strong> <span id="cliente-nit"></span>
                                            </small>
                                        </div>
                                    </div>
                                </form>

                                <!-- Inventario Disponible -->
                                <div class="mt-4">
                                    <h6><i class="bi bi-box-seam me-2"></i>Productos Disponibles</h6>
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" id="filtro-productos" 
                                               placeholder="Buscar producto por marca o modelo...">
                                    </div>
                                    <div style="max-height: 300px; overflow-y: auto;">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover" id="tabla-inventario">
                                                <thead class="table-success">
                                                    <tr>
                                                        <th>Producto</th>
                                                        <th>Stock</th>
                                                        <th>Precio</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="inventario-body">
                                                    <!-- Se carga dinámicamente -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Derecho: Carrito de Compras -->
                    <div class="col-lg-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-cart3 me-2"></i>Carrito de Compras</h6>
                            </div>
                            <div class="card-body">
                                <div id="carrito-vacio" class="text-center text-muted py-5">
                                    <i class="bi bi-cart-x display-4"></i>
                                    <p class="mt-2">Carrito vacío<br><small>Agregue productos del inventario</small></p>
                                </div>

                                <div id="carrito-productos" style="display: none;">
                                    <div style="max-height: 250px; overflow-y: auto;">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>Producto</th>
                                                        <th>Cant.</th>
                                                        <th>Precio</th>
                                                        <th>Subtotal</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="carrito-body">
                                                    <!-- Se carga dinámicamente -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Total y Acciones -->
                                    <div class="border-top pt-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Total: <span class="text-success" id="total-venta">Q. 0.00</span></h5>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button class="btn btn-success" type="button" id="btn-procesar-venta">
                                                <i class="bi bi-credit-card me-1"></i>Procesar Venta
                                            </button>
                                            <button class="btn btn-warning" type="button" id="btn-modificar-venta" style="display: none;">
                                                <i class="bi bi-pencil me-1"></i>Modificar Venta
                                            </button>
                                            <button class="btn btn-secondary" type="button" id="btn-limpiar-carrito">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Historial de Ventas -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center"><i class="bi bi-clock-history me-2"></i>Historial de Ventas</h3>

                <!-- Filtros del Historial -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filtro-fecha-desde" class="form-label">Desde:</label>
                        <input type="date" class="form-control" id="filtro-fecha-desde">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro-fecha-hasta" class="form-label">Hasta:</label>
                        <input type="date" class="form-control" id="filtro-fecha-hasta">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro-cliente-hist" class="form-label">Cliente:</label>
                        <select class="form-select" id="filtro-cliente-hist">
                            <option value="">Todos los clientes</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-info me-2" id="btn-aplicar-filtros">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                        <button class="btn btn-outline-secondary" id="btn-limpiar-filtros">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </button>
                    </div>
                </div>

                <!-- Tabla de Ventas -->
                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="tabla-ventas">
                        <!-- DataTable se inicializa en JavaScript -->
                    </table>
                </div>

                <!-- Resumen de Ventas -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">Total Ventas</h6>
                                <h4 class="text-success" id="resumen-total">Q. 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Cantidad</h6>
                                <h4 class="text-primary" id="resumen-cantidad">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-warning">
                            <div class="card-body">
                                <h6 class="card-title text-warning">Promedio</h6>
                                <h4 class="text-warning" id="resumen-promedio">Q. 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">Última Venta</h6>
                                <h6 class="text-info" id="resumen-ultima">-</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalles de Venta -->
<div class="modal fade" id="modal-detalle-venta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalle-venta-content">
                <!-- Se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-imprimir-venta">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/ventas/index.js') ?>"></script>