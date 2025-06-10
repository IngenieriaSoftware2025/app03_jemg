<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <img src="<?= asset('../public/images/logo_phone_shop.png') ?>"
                    style="width: 185px;" alt="logo">
                  <h4 class="mt-1 mb-5 pb-1">Sistema de Inventario</h4>
                </div>

                <form id="FormLogin">
                  <p>Ingrese sus credenciales para acceder al sistema</p>

                  <div class="form-outline mb-4">
                    <label class="form-label" for="usuario_correo">Correo Electrónico</label>
                    <input type="email" id="usuario_correo" name="usuario_correo" class="form-control"
                      placeholder="ejemplo@correo.com" required />
                  </div>

                  <div class="form-outline mb-4">
                    <label class="form-label" for="usuario_contra">Contraseña</label>
                    <input type="password" id="usuario_contra" name="usuario_contra" class="form-control" 
                      placeholder="Ingrese su contraseña" required />
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                    <button class="btn btn-primary btn-block btn-lg gradient-custom-2 mb-3 w-100" type="submit" id="BtnLogin">
                      <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>
                    <a class="text-muted" href="#!" id="LinkRecuperarPassword">¿Olvidó su contraseña?</a>
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">¿No tiene una cuenta?</p>
                    <button type="button" class="btn btn-outline-danger" id="BtnRegistrar">
                      <i class="bi bi-person-plus me-1"></i>Registrarse
                    </button>
                  </div>

                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">Sistema de Inventario y Reparaciones</h4>
                <p class="small mb-0">
                  Bienvenido al sistema integral para la gestión de inventario de celulares, 
                  control de reparaciones, ventas y estadísticas. Una herramienta completa 
                  para optimizar la operación de su negocio de reparación y venta de dispositivos móviles.
                </p>
                <br>
                <p class="small mb-0">
                  <strong>Funcionalidades principales:</strong>
                </p>
                <ul class="small">
                  <li>Control de inventario de celulares</li>
                  <li>Gestión de reparaciones</li>
                  <li>Registro de ventas</li>
                  <li>Administración de clientes</li>
                  <li>Reportes y estadísticas</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CSS personalizado para gradientes -->
<style>
.gradient-custom-2 {
  background: linear-gradient(to right, #007bff, #0056b3);
}

.gradient-form {
  background: linear-gradient(to right, #e3f2fd, #bbdefb);
}

.btn.gradient-custom-2 {
  background: linear-gradient(to right, #007bff, #0056b3);
  border: none;
  color: white;
}

.btn.gradient-custom-2:hover {
  background: linear-gradient(to right, #0056b3, #004085);
}
</style>

<script src="<?= asset('build/js/auth/login.js') ?>"></script>