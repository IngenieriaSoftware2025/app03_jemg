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

        // Sanitización y validación del primer nombre (obligatorio)
        $_POST['usuario_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
        
        $cantidad_nombre = strlen($_POST['usuario_nom1']);
        
        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer nombre debe tener más de 1 caracteres'
            ]);
            exit;
        }
        
        // Sanitización del segundo nombre (opcional)
        if (!empty($_POST['usuario_nom2'])) {
            $_POST['usuario_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2']))));
            
            $cantidad_nombre2 = strlen($_POST['usuario_nom2']);
            
            if ($cantidad_nombre2 < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El segundo nombre debe tener más de 1 caracteres'
                ]);
                exit;
            }
        } else {
            $_POST['usuario_nom2'] = '';
        }
        
        // Sanitización y validación del primer apellido (obligatorio)
        $_POST['usuario_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
        $cantidad_apellido = strlen($_POST['usuario_ape1']);
        
        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer apellido debe tener más de 1 caracteres'
            ]);
            exit;
        }
        
        // Sanitización del segundo apellido (opcional)
        if (!empty($_POST['usuario_ape2'])) {
            $_POST['usuario_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2']))));
            
            $cantidad_apellido2 = strlen($_POST['usuario_ape2']);
            
            if ($cantidad_apellido2 < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El segundo apellido debe tener más de 1 caracteres'
                ]);
                exit;
            }
        } else {
            $_POST['usuario_ape2'] = '';
        }
        
        // Validación del teléfono
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener 8 números'
            ]);
            exit;
        }
        
        // Sanitización de la dirección
        $_POST['usuario_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_direc']))));
        
        // Validación del DPI
        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_VALIDATE_INT);
        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de dígitos del DPI debe ser igual a 13'
            ]);
            exit;
        }
        
        // Validación del correo electrónico
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            exit;
        }
        
        // Verificar si el correo ya existe
        $usuarioExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_correo = '{$_POST['usuario_correo']}'");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ya está registrado'
            ]);
            exit;
        }
        
        // Verificar si el DPI ya existe
        $dpiExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_dpi = '{$_POST['usuario_dpi']}'");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya está registrado'
            ]);
            exit;
        }
        
        // Validación de la contraseña
        if (strlen($_POST['usuario_contra']) < 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe tener al menos 8 caracteres'
            ]);
            exit;
        }
        
        // Validación de confirmación de contraseña
        if ($_POST['usuario_contra'] !== $_POST['confirmar_contra']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Las contraseñas no coinciden'
            ]);
            exit;
        }
        
        // Generar token único
        $_POST['usuario_token'] = uniqid();
        $dpi = $_POST['usuario_dpi'];
        $_POST['usuario_fecha_creacion'] = '';
        $_POST['usuario_fecha_contra'] = '';
        
        // Manejo de fotografía (opcional)
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
                    'codigo' => 0,
                    'mensaje' => 'Solo puede cargar archivos JPG, PNG o JPEG',
                ]);
                exit;
            }
            
            if ($fileSize >= 2000000) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La imagen debe pesar menos de 2MB',
                ]);
                exit;
            }
            
            if ($fileError === 0) {
                $rutaFoto = "storage/fotosUsuarios/$dpi.$fileExtension";
                $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "../../" . $rutaFoto);
                
                if (!$subido) {
                    http_response_code(500);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al cargar la fotografía',
                    ]);
                    exit;
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en la carga de fotografía',
                ]);
                exit;
            }
        }
        
        // Crear usuario
        try {
            // Hash de la contraseña
            $_POST['usuario_contra'] = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
            
            // Crear instancia del usuario
            $usuario = new Usuarios($_POST);
            $usuario->usuario_fotografia = $rutaFoto;
            
            $resultado = $usuario->crear();

            if($resultado['resultado'] == 1){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario registrado correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar el usuario',
                    'detalle' => $resultado
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al registrar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarUsuario()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT *
                    FROM usuarios WHERE usuario_situacion = 1";
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
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarUsuario()
    {
        getHeadersApi();

        $id = $_POST['usuario_id'];

        // Sanitización y validación del primer nombre (obligatorio)
        $_POST['usuario_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
        if (strlen($_POST['usuario_nom1']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer nombre debe tener más de 1 caracteres'
            ]);
            return;
        }

        // Sanitización del segundo nombre (opcional)
        if (!empty($_POST['usuario_nom2'])) {
            $_POST['usuario_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2']))));
            if (strlen($_POST['usuario_nom2']) < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El segundo nombre debe tener más de 1 caracteres'
                ]);
                return;
            }
        } else {
            $_POST['usuario_nom2'] = '';
        }

        // Sanitización y validación del primer apellido (obligatorio)
        $_POST['usuario_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
        if (strlen($_POST['usuario_ape1']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer apellido debe tener más de 1 caracteres'
            ]);
            return;
        }

        // Sanitización del segundo apellido (opcional)
        if (!empty($_POST['usuario_ape2'])) {
            $_POST['usuario_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2']))));
            if (strlen($_POST['usuario_ape2']) < 2) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El segundo apellido debe tener más de 1 caracteres'
                ]);
                return;
            }
        } else {
            $_POST['usuario_ape2'] = '';
        }

        // Validación de teléfono
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener 8 números'
            ]);
            return;
        }

        // Sanitización de dirección
        $_POST['usuario_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_direc']))));

        // Validación de DPI
        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe tener exactamente 13 dígitos'
            ]);
            return;
        }

        // Validación de correo
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            return;
        }

        // Verificar duplicados (excluyendo el usuario actual)
        $usuarioExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_correo = '{$_POST['usuario_correo']}' AND usuario_id != $id");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ya está registrado por otro usuario'
            ]);
            return;
        }

        $dpiExistente = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_dpi = '{$_POST['usuario_dpi']}' AND usuario_id != $id");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya está registrado por otro usuario'
            ]);
            return;
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
                    'mensaje' => 'Solo puede cargar archivos JPG, PNG o JPEG',
                ]);
                return;
            }
            
            if ($file['size'] >= 2000000) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La imagen debe pesar menos de 2MB',
                ]);
                return;
            }
            
            if ($file['error'] === 0) {
                $rutaFoto = "storage/fotosUsuarios/{$_POST['usuario_dpi']}.$fileExtension";
                $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "../../" . $rutaFoto);
                
                if (!$subido) {
                    http_response_code(500);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al cargar la fotografía',
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
                'mensaje' => 'La información del usuario ha sido modificada exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarUsuario()
    {
        getHeadersApi();
        
        try {
            $id = filter_var($_POST['usuario_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Verificar que el usuario existe antes de eliminarlo
            $usuarioExiste = self::fetchFirst("SELECT usuario_id FROM usuarios WHERE usuario_id = $id AND usuario_situacion = 1");
            if (!$usuarioExiste) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El usuario no existe o ya está eliminado'
                ]);
                return;
            }
            
            $consulta = "UPDATE usuarios SET usuario_situacion = 0 WHERE usuario_id = $id";
            $resultado = self::SQL($consulta);
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario eliminado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar usuario'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

}