<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?= asset('build/js/app.js') ?>"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>DemoApp</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark  bg-dark">
        
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/ejemplo/">
                <img src="<?= asset('./images/cit.png') ?>" width="35px'" alt="cit" >
                Aplicaciones
            </a>
            <div class="collapse navbar-collapse" id="navbarToggler">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app03_jemg/"><i class="bi bi-house-fill me-2"></i>Inicio</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app03_jemg/login"><i class="bi bi-door-open me-2"></i>Login</a>
                    </li>
  
                    <div class="nav-item dropdown " >
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Opciones
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-dark "id="dropwdownRevision" style="margin: 0;">
                            <!-- <h6 class="dropdown-header">Información</h6> -->
                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/clientes/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-person-add"></i> Crear Clientes</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/usuarios/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-person-gear"></i> Crear Usuarios</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/marcas/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-phone"></i> Crear Marcas</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/modelos/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-phone-fill"></i>   Crear Modelos</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/servicios/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-tools"></i>   Crear Servicios</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/inventario/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-clipboard"></i>
   Crear Inventarios</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/servicios/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-pencil-fill"></i>   Crear Permisos</a>
                            </li>

                            <li>
                                <a class="dropdown-item nav-link text-white " href="/app03_jemg/ventas/index"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i><i class="bi bi-check2-all"></i>   Crear Ventas</a>
                            </li>

                    
                        
                        </ul>
                    </div> 

                </ul> 
                <div class="col-lg-1 d-grid mb-lg-0 mb-2">
                    <!-- Ruta relativa desde el archivo donde se incluye menu.php -->
                    <a href="/menu/" class="btn btn-danger"><i class="bi bi-arrow-bar-left"></i>MENÚ</a>
                </div>

            
            </div>
        </div>
        
    </nav>
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">
        
        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid " >
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                        Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>
</html>