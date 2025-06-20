<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de marcas!</h5>
                    <h4 class="text-center mb-2 text-primary">Manipulacion de marcas</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormMarcas">
                        <input type="hidden" id="marca_id" name="marca_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="marca_nombre" class="form-label">Nombre de la Marca</label>
                                <input type="text" class="form-control" id="marca_nombre" name="marca_nombre" placeholder="Ingrese el nombre de la marca">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center mb-3">
                            <div class="col-lg-12">
                                <label for="marca_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="marca_descripcion" name="marca_descripcion" rows="3" placeholder="Ingrese una descripción de la marca (opcional)"></textarea>
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
                <h3 class="text-center">Marcas Registradas</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableMarcas">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/marcas/index.js') ?>"></script>