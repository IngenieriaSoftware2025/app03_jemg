<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de usuarios!</h5>
                    <h4 class="text-center mb-2 text-primary">Manipulacion de usuarios</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormUsuarios">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <!-- Nombres -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_nom1" class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="usuario_nom1" name="usuario_nom1" placeholder="Ingrese el primer nombre">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_nom2" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="usuario_nom2" name="usuario_nom2" placeholder="Ingrese el segundo nombre (opcional)">
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_ape1" class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="usuario_ape1" name="usuario_ape1" placeholder="Ingrese el primer apellido">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_ape2" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="usuario_ape2" name="usuario_ape2" placeholder="Ingrese el segundo apellido (opcional)">
                            </div>
                        </div>

                        <!-- DPI y Teléfono -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_dpi" class="form-label">DPI</label>
                                <input type="text" class="form-control" id="usuario_dpi" name="usuario_dpi" placeholder="Ingrese el DPI (13 dígitos)" maxlength="13">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_tel" class="form-label">Teléfono</label>
                                <input type="number" class="form-control" id="usuario_tel" name="usuario_tel" placeholder="Ingrese el número de teléfono">
                            </div>
                        </div>

                        <!-- Correo y Dirección -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="usuario_correo" name="usuario_correo" placeholder="Ingrese el correo electrónico">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_direc" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="usuario_direc" name="usuario_direc" placeholder="Ingrese la dirección completa">
                            </div>
                        </div>

                        <!-- ROL Y PUESTO - NUEVOS CAMPOS -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="usuario_rol" class="form-label">Rol</label>
                                <select class="form-select" id="usuario_rol" name="usuario_rol">
                                    <option value="">Seleccione un rol</option>
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="EMPLEADO">Empleado</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="usuario_puesto" class="form-label">Puesto</label>
                                <select class="form-select" id="usuario_puesto" name="usuario_puesto">
                                    <option value="">Seleccione un puesto</option>
                                    <option value="GERENTE_GENERAL">Gerente General</option>
                                    <option value="GERENTE_VENTAS">Gerente de Ventas</option>
                                    <option value="GERENTE_TECNICO">Gerente Técnico</option>
                                    <option value="SUPERVISOR">Supervisor</option>
                                    <option value="VENDEDOR">Vendedor</option>
                                    <option value="TECNICO">Técnico</option>
                                    <option value="CAJERO">Cajero</option>
                                    <option value="RECEPCIONISTA">Recepcionista</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="usuario_estado" class="form-label">Estado</label>
                                <select class="form-select" id="usuario_estado" name="usuario_estado">
                                    <option value="">Seleccione un estado</option>
                                    <option value="ACTIVO">Activo</option>
                                    <option value="INACTIVO">Inactivo</option>
                                    <option value="SUSPENDIDO">Suspendido</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="usuario_contrasena" name="usuario_contrasena" placeholder="Ingrese la contraseña">
                            </div>
                            <div class="col-lg-6">
                                <label for="confirmar_contra" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contra" name="confirmar_contra" placeholder="Confirme la contraseña">
                            </div>
                        </div>

                        <!-- Fecha de Contratación y Fotografía -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_fecha_contra" class="form-label">Fecha de Contratación</label>
                                <input type="datetime-local" class="form-control" id="usuario_fecha_contra" name="usuario_fecha_contra">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_fotografia" class="form-label">Fotografía de Perfil</label>
                                <input type="file" class="form-control" id="usuario_fotografia" name="usuario_fotografia" accept="image/*">
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
                <h3 class="text-center">Usuarios Registrados</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/usuarios/index.js') ?>"></script>