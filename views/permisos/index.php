<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de permisos!</h5>
                    <h4 class="text-center mb-2 text-primary">Manipulacion de permisos</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormPermisos">
                        <input type="hidden" id="permiso_id" name="permiso_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="permiso_app_id" class="form-label">ID Aplicación</label>
                                <input type="number" class="form-control" id="permiso_app_id" name="permiso_app_id" placeholder="Ingrese el ID de la aplicación">
                            </div>
                            <div class="col-lg-6">
                                <label for="permiso_nombre" class="form-label">Nombre del Permiso</label>
                                <input type="text" class="form-control" id="permiso_nombre" name="permiso_nombre" placeholder="Ingrese el nombre del permiso">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="permiso_clave" class="form-label">Clave del Permiso</label>
                                <input type="text" class="form-control" id="permiso_clave" name="permiso_clave" placeholder="Ingrese la clave del permiso">
                            </div>
                            <div class="col-lg-6">
                                <label for="permiso_desc" class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="permiso_desc" name="permiso_desc" placeholder="Ingrese la descripción del permiso">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center mb-3">
                            <div class="col-lg-6">
                                <label for="permiso_fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="permiso_fecha" name="permiso_fecha">
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
                <h3 class="text-center">Permisos Registrados</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TablePermisos">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>


<script src="<?= asset('build/js/permisos/index.js') ?>"></script>