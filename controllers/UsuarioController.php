<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Usuarios;

//C:\docker\app03_jemg\views\usuarios\index.php
class UsuarioController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('usuarios/index', []);
    }

    public static function guardarUsuario()
    {
        getHeadersApi();

        // echo json_encode($_POST);
        // return;

        // Validación del primer nombre (obligatorio)
        $_POST['usuario_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
        $cantidad_nom1 = strlen($_POST['usuario_nom1']);

        if ($cantidad_nom1 < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el primer nombre debe de ser mayor a dos'
            ]);
            return;
        }

        // Validación del segundo nombre (opcional)
        if (!empty($_POST['usuario_nom2'])) {
            $_POST['usuario_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2']))));
            $cantidad_nom2 = strlen($_POST['usuario_nom2']);

            if ($cantidad_nom2 < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad de digitos que debe de contener el segundo nombre debe de ser mayor a dos'
                ]);
                return;
            }
        } else {
            $_POST['usuario_nom2'] = null;
        }

        // Validación del primer apellido (obligatorio)
        $_POST['usuario_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
        $cantidad_ape1 = strlen($_POST['usuario_ape1']);

        if ($cantidad_ape1 < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el primer apellido debe de ser mayor a dos'
            ]);
            return;
        }

        // Validación del segundo apellido (opcional)
        if (!empty($_POST['usuario_ape2'])) {
            $_POST['usuario_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2']))));
            $cantidad_ape2 = strlen($_POST['usuario_ape2']);

            if ($cantidad_ape2 < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad de digitos que debe de contener el segundo apellido debe de ser mayor a dos'
                ]);
                return;
            }
        } else {
            $_POST['usuario_ape2'] = null;
        }

        // Validación del teléfono
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_VALIDATE_INT);

        if (strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos de telefono debe de ser igual a 8'
            ]);
            return;
        }

        // Validación de la dirección
        $_POST['usuario_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_direc']))));
        $cantidad_direc = strlen($_POST['usuario_direc']);

        if ($cantidad_direc < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La direccion debe de contener al menos 5 caracteres'
            ]);
            return;
        }

        // Validación del DPI
        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_VALIDATE_INT);

        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos del DPI debe de ser igual a 13'
            ]);
            return;
        }

        // Validación del correo electrónico
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico ingresado es invalido'
            ]);
            return;
        }

        // Verificar si el correo ya existe
        $usuarioExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_correo = '{$_POST['usuario_correo']}'");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico ya esta registrado'
            ]);
            return;
        }

        // Verificar si el DPI ya existe
        $dpiExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_dpi = '{$_POST['usuario_dpi']}'");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya esta registrado'
            ]);
            return;
        }

        // VALIDACIÓN DE CONTRASEÑA (del RegistroController)
        if (strlen($_POST['usuario_contrasena']) < 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contrasena debe tener al menos 8 caracteres'
            ]);
            return;
        }

        // VALIDACIÓN DE CONFIRMACIÓN DE CONTRASEÑA (del RegistroController)
        if ($_POST['usuario_contrasena'] !== $_POST['confirmar_contra']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Las contrasenas no coinciden'
            ]);
            return;
        }

        // Validación del rol
        $_POST['usuario_rol'] = htmlspecialchars($_POST['usuario_rol']);
        $rol = $_POST['usuario_rol'];

        if ($rol != "ADMINISTRADOR" && $rol != "EMPLEADO") {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El rol solo puede ser "ADMINISTRADOR" o "EMPLEADO"'
            ]);
            return;
        }

        // Validación del puesto
        $_POST['usuario_puesto'] = htmlspecialchars($_POST['usuario_puesto']);
        $puesto = $_POST['usuario_puesto'];

        if ($puesto != "GERENTE_GENERAL" && $puesto != "GERENTE_VENTAS" && $puesto != "GERENTE_TECNICO" && 
            $puesto != "SUPERVISOR" && $puesto != "VENDEDOR" && $puesto != "TECNICO" && 
            $puesto != "CAJERO" && $puesto != "RECEPCIONISTA") {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El puesto seleccionado no es valido'
            ]);
            return;
        }

        // Validación del estado
        $_POST['usuario_estado'] = htmlspecialchars($_POST['usuario_estado']);
        $estado = $_POST['usuario_estado'];

        if ($estado != "ACTIVO" && $estado != "INACTIVO" && $estado != "SUSPENDIDO") {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El estado solo puede ser "ACTIVO", "INACTIVO" o "SUSPENDIDO"'
            ]);
            return;
        }

        // Validación de fecha de contratación (opcional)
        if (!empty($_POST['usuario_fecha_contra'])) {
            $_POST['usuario_fecha_contra'] = date('Y-m-d H:i:s', strtotime($_POST['usuario_fecha_contra']));
        } else {
            $_POST['usuario_fecha_contra'] = null;
        }

        // GENERAR TOKEN ÚNICO (del RegistroController)
        $_POST['usuario_token'] = uniqid();
        $dpi = $_POST['usuario_dpi'];
        $_POST['usuario_fecha_creacion'] = null;

        // MANEJO DE FOTOGRAFÍA (adaptado del RegistroController)
        $rutaFoto = null;
        
        if (isset($_FILES['usuario_fotografia']) && $_FILES['usuario_fotografia']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['usuario_fotografia'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Extensiones permitidas
            $allowed = ['jpg', 'jpeg', 'png'];
            
            if (!in_array($fileExtension, $allowed)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 2,
                    'mensaje' => 'Solo puede cargar archivos JPG, PNG o JPEG'
                ]);
                return;
            }
            
            if ($fileSize >= 2000000) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 2,
                    'mensaje' => 'La imagen debe pesar menos de 2MB'
                ]);
                return;
            }
            
            if ($fileError === 0) {
                $rutaFoto = "storage/fotosUsuarios/$dpi.$fileExtension";
                $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "../../" . $rutaFoto);
                
                if (!$subido) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al cargar la fotografia'
                    ]);
                    return;
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en la carga de fotografia'
                ]);
                return;
            }
        }

        try {
            // HASH DE LA CONTRASEÑA (del RegistroController)
            $_POST['usuario_contrasena'] = password_hash($_POST['usuario_contrasena'], PASSWORD_DEFAULT);
            
            $data = new Usuarios([
                'usuario_nom1' => $_POST['usuario_nom1'],
                'usuario_nom2' => $_POST['usuario_nom2'],
                'usuario_ape1' => $_POST['usuario_ape1'],
                'usuario_ape2' => $_POST['usuario_ape2'],
                'usuario_tel' => $_POST['usuario_tel'],
                'usuario_direc' => $_POST['usuario_direc'],
                'usuario_dpi' => $_POST['usuario_dpi'],
                'usuario_correo' => $_POST['usuario_correo'],
                'usuario_contrasena' => $_POST['usuario_contrasena'],
                'usuario_token' => $_POST['usuario_token'],
                'usuario_fecha_contra' => $_POST['usuario_fecha_contra'],
                'usuario_rol' => $_POST['usuario_rol'],
                'usuario_puesto' => $_POST['usuario_puesto'],
                'usuario_estado' => $_POST['usuario_estado'],
                'usuario_situacion' => 1
            ]);

            if ($rutaFoto) {
                $data->usuario_fotografia = $rutaFoto;
            }

            $resultado = $data->crear();

            if($resultado['resultado'] == 1){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario registrado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar al usuario',
                    'detalle' => $resultado
                ]);
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarUsuario()
    {
        getHeadersApi();
        
        try {
            // $data = Usuarios::all();

            $sql = "SELECT * FROM usuarios WHERE usuario_situacion = 1";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuarios obtenidos correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay usuarios registrados',
                    'data' => []
                ]);
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los usuarios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarUsuario()
    {
        getHeadersApi();

        $id = $_POST['usuario_id'];

        // Validación del primer nombre (obligatorio)
        $_POST['usuario_nom1'] = htmlspecialchars($_POST['usuario_nom1']);
        $cantidad_nom1 = strlen($_POST['usuario_nom1']);

        if ($cantidad_nom1 < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el primer nombre debe de ser mayor a dos'
            ]);
            return;
        }

        // Validación del segundo nombre (opcional)
        if (!empty($_POST['usuario_nom2'])) {
            $_POST['usuario_nom2'] = htmlspecialchars($_POST['usuario_nom2']);
            $cantidad_nom2 = strlen($_POST['usuario_nom2']);

            if ($cantidad_nom2 < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad de digitos que debe de contener el segundo nombre debe de ser mayor a dos'
                ]);
                return;
            }
        } else {
            $_POST['usuario_nom2'] = null;
        }

        // Validación del primer apellido (obligatorio)
        $_POST['usuario_ape1'] = htmlspecialchars($_POST['usuario_ape1']);
        $cantidad_ape1 = strlen($_POST['usuario_ape1']);

        if ($cantidad_ape1 < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el primer apellido debe de ser mayor a dos'
            ]);
            return;
        }

        // Validación del segundo apellido (opcional)
        if (!empty($_POST['usuario_ape2'])) {
            $_POST['usuario_ape2'] = htmlspecialchars($_POST['usuario_ape2']);
            $cantidad_ape2 = strlen($_POST['usuario_ape2']);

            if ($cantidad_ape2 < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad de digitos que debe de contener el segundo apellido debe de ser mayor a dos'
                ]);
                return;
            }
        } else {
            $_POST['usuario_ape2'] = null;
        }

        // Validación del teléfono
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_VALIDATE_INT);

        if (strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos de telefono debe de ser igual a 8'
            ]);
            return;
        }

        // Validación de la dirección
        $_POST['usuario_direc'] = htmlspecialchars($_POST['usuario_direc']);
        $cantidad_direc = strlen($_POST['usuario_direc']);

        if ($cantidad_direc < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La direccion debe de contener al menos 5 caracteres'
            ]);
            return;
        }

        // Validación del DPI
        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_NUMBER_INT);

        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos del DPI debe de ser igual a 13'
            ]);
            return;
        }

        // Validación del correo electrónico
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico ingresado es invalido'
            ]);
            return;
        }

        // Verificar si el correo ya existe (excluyendo el usuario actual)
        $usuarioExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_correo = '{$_POST['usuario_correo']}' AND usuario_id != $id");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico ya esta registrado por otro usuario'
            ]);
            return;
        }

        // Verificar si el DPI ya existe (excluyendo el usuario actual)
        $dpiExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_dpi = '{$_POST['usuario_dpi']}' AND usuario_id != $id");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya esta registrado por otro usuario'
            ]);
            return;
        }

        // Validación del rol
        $_POST['usuario_rol'] = htmlspecialchars($_POST['usuario_rol']);
        $rol = $_POST['usuario_rol'];

        if ($rol != "ADMINISTRADOR" && $rol != "EMPLEADO") {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El rol solo puede ser "ADMINISTRADOR" o "EMPLEADO"'
            ]);
            return;
        }

        // Validación del puesto
        $_POST['usuario_puesto'] = htmlspecialchars($_POST['usuario_puesto']);
        $puesto = $_POST['usuario_puesto'];

        if ($puesto != "GERENTE_GENERAL" && $puesto != "GERENTE_VENTAS" && $puesto != "GERENTE_TECNICO" && 
            $puesto != "SUPERVISOR" && $puesto != "VENDEDOR" && $puesto != "TECNICO" && 
            $puesto != "CAJERO" && $puesto != "RECEPCIONISTA") {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El puesto seleccionado no es valido'
            ]);
            return;
        }

        // Validación del estado
        $_POST['usuario_estado'] = htmlspecialchars($_POST['usuario_estado']);
        $estado = $_POST['usuario_estado'];

        if ($estado != "ACTIVO" && $estado != "INACTIVO" && $estado != "SUSPENDIDO") {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El estado solo puede ser "ACTIVO", "INACTIVO" o "SUSPENDIDO"'
            ]);
            return;
        }

        // Validación de fecha de contratación (opcional)
        if (!empty($_POST['usuario_fecha_contra'])) {
            $_POST['usuario_fecha_contra'] = date('Y-m-d H:i:s', strtotime($_POST['usuario_fecha_contra']));
        } else {
            $_POST['usuario_fecha_contra'] = null;
        }

        // Manejo de fotografía (opcional en modificación)
        $rutaFoto = null;
        $actualizarFoto = false;
        
        if (isset($_FILES['usuario_fotografia']) && $_FILES['usuario_fotografia']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['usuario_fotografia'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];
            
            if (!in_array($fileExtension, $allowed)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Solo puede cargar archivos JPG, PNG o JPEG'
                ]);
                return;
            }
            
            if ($file['size'] >= 2000000) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La imagen debe pesar menos de 2MB'
                ]);
                return;
            }
            
            if ($file['error'] === 0) {
                $rutaFoto = "storage/fotosUsuarios/{$_POST['usuario_dpi']}.$fileExtension";
                $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "../../" . $rutaFoto);
                
                if (!$subido) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al cargar la fotografia'
                    ]);
                    return;
                }
                $actualizarFoto = true;
            }
        }

        try {
            $data = Usuarios::find($id);
            
            $datosActualizar = [
                'usuario_nom1' => $_POST['usuario_nom1'],
                'usuario_nom2' => $_POST['usuario_nom2'],
                'usuario_ape1' => $_POST['usuario_ape1'],
                'usuario_ape2' => $_POST['usuario_ape2'],
                'usuario_tel' => $_POST['usuario_tel'],
                'usuario_direc' => $_POST['usuario_direc'],
                'usuario_dpi' => $_POST['usuario_dpi'],
                'usuario_correo' => $_POST['usuario_correo'],
                'usuario_rol' => $_POST['usuario_rol'],
                'usuario_puesto' => $_POST['usuario_puesto'],
                'usuario_estado' => $_POST['usuario_estado'],
                'usuario_fecha_contra' => $_POST['usuario_fecha_contra'],
                'usuario_situacion' => 1
            ];
            
            // Solo actualizar fotografía si se subió una nueva
            if ($actualizarFoto) {
                $datosActualizar['usuario_fotografia'] = $rutaFoto;
            }
            
            $data->sincronizar($datosActualizar);
            $data->actualizar();
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La informacion del usuario ha sido modificada exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarUsuario()
    {
        try {
            $id = filter_var($_POST['usuario_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE usuarios SET usuario_situacion = 0 WHERE usuario_id = $id";
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Exito al eliminar'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

}