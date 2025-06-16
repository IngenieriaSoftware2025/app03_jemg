<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de inventario!</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Inventario</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormInventario">
                        <input type="hidden" id="inventario_id" name="inventario_id">

                        <!-- Modelo y Stock -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="modelo_id" class="form-label">Modelo de Celular</label>
                                <select class="form-select" id="modelo_id" name="modelo_id" required>
                                    <option value="">Seleccione un modelo</option>
                                    <?php if(isset($modelos) && !empty($modelos)): ?>
                                        <?php foreach($modelos as $modelo): ?>
                                            <option value="<?= $modelo->modelo_id ?>">
                                                <?= $modelo->marca_nombre ?> - <?= $modelo->modelo_nombre ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="inventario_stock_actual" class="form-label">Stock Actual</label>
                                <input type="number" class="form-control" id="inventario_stock_actual" name="inventario_stock_actual" 
                                       placeholder="Cantidad en stock" min="0" required>
                            </div>
                        </div>

                        <!-- Precios -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="inventario_precio_venta" class="form-label">Precio de Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="inventario_precio_venta" 
                                           name="inventario_precio_venta" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="inventario_precio_compra" class="form-label">Precio de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="inventario_precio_compra" 
                                           name="inventario_precio_compra" placeholder="0.00 (opcional)" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy"></i> Guardar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil"></i> Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">Inventario Registrado</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableInventario">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/inventario/index.js') ?>"></script>