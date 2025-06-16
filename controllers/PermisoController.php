<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Marcas;

//C:\docker\app03_jemg\views\marcas\index.php
class MarcaController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('marcas/index', []);
    }


}