<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de servicios!</h5>
                    <h4 class="text-center mb-2 text-primary">Manipulacion de servicios</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormServicios">
                        <input type="hidden" id="servicio_id" name="servicio_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="servicio_nombre" class="form-label">Nombre del Servicio</label>
                                <input type="text" class="form-control" id="servicio_nombre" name="servicio_nombre" placeholder="Ingrese el nombre del servicio">
                            </div>
                            <div class="col-lg-6">
                                <label for="servicio_precio" class="form-label">Precio</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="servicio_precio" name="servicio_precio" placeholder="Ingrese el precio del servicio">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="servicio_tiempo_estimado" class="form-label">Tiempo Estimado (horas)</label>
                                <input type="number" min="0" class="form-control" id="servicio_tiempo_estimado" name="servicio_tiempo_estimado" placeholder="Tiempo estimado en horas (opcional)">
                            </div>
                            <div class="col-lg-6">
                                <label for="servicio_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="servicio_descripcion" name="servicio_descripcion" rows="3" placeholder="Descripción del servicio (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar"><i class="bi bi-floppy"></i>
                                     Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar"><i class="bi bi-pencil"></i>

                                    Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar"><i class="bi bi-arrow-clockwise"></i>
                                     Limpiar
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
                <h3 class="text-center">Servicios Registrados</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableServicios">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>


<script src="<?= asset('build/js/servicios/index.js') ?>"></script>