<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de modelos!</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Modelos de Celulares</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormModelos">
                        <input type="hidden" id="modelo_id" name="modelo_id">

                        <!-- Marca y Nombre del Modelo -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="marca_id" class="form-label">Marca</label>
                                <select class="form-select" id="marca_id" name="marca_id" required>
                                    <option value="">Seleccione una marca</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="modelo_nombre" class="form-label">Nombre del Modelo</label>
                                <input type="text" class="form-control" id="modelo_nombre" name="modelo_nombre" 
                                       placeholder="Ej: Galaxy S23, iPhone 15" required>
                            </div>
                        </div>

                        <!-- Precio y Año -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="modelo_precio_referencia" class="form-label">Precio de Referencia</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="modelo_precio_referencia" 
                                           name="modelo_precio_referencia" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label for="modelo_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="modelo_descripcion" name="modelo_descripcion" 
                                          rows="3" placeholder="Descripción general del modelo"></textarea>
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
                <h3 class="text-center">Modelos Registrados</h3>

                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="filtro_marca" class="form-label">Filtrar por Marca:</label>
                        <select class="form-select" id="filtro_marca">
                            <option value="">Todas las marcas</option>
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <button class="btn btn-info" id="BtnLimpiarFiltros">
                            <i class="bi bi-funnel"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableModelos">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/modelos/index.js') ?>"></script>