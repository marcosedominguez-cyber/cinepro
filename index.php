<?php
session_start();

require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/models/Pelicula.php';
require_once __DIR__ . '/models/Sala.php';
require_once __DIR__ . '/models/Asiento.php';
require_once __DIR__ . '/models/Funcion.php';
require_once __DIR__ . '/models/Cliente.php';
require_once __DIR__ . '/models/Pago.php';
require_once __DIR__ . '/models/Ticket.php';
require_once __DIR__ . '/models/Compra.php';
require_once __DIR__ . '/models/Genero.php';
require_once __DIR__ . '/models/Administrador.php';

require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/PeliculaController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/CompraController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminAuthController.php';

$db = Database::connect();
$accion = $_GET['accion'] ?? 'home';

switch ($accion) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'cartelera':
        (new PeliculaController($db))->cartelera();
        break;

    case 'guardar_pelicula':
        (new PeliculaController($db))->guardar();
        break;

    case 'admin':
        (new AdminController($db))->index();
        break;

    case 'guardar_sala':
        (new AdminController($db))->guardarSala();
        break;

    case 'guardar_asientos':
        (new AdminController($db))->guardarAsientos();
        break;

    case 'guardar_funcion':
        (new AdminController($db))->guardarFuncion();
        break;

    case 'compra':
        (new CompraController($db))->index();
        break;

    case 'procesar_compra':
        (new CompraController($db))->procesarCompra();
        break;

    case 'login':
        (new AuthController($db))->login();
        break;

    case 'autenticar':
        (new AuthController($db))->autenticar();
        break;

    case 'logout':
        (new AuthController($db))->logout();
        break;

    default:
        echo "Acción no válida.";
        break;

    case 'registro':
        (new AuthController($db))->registro();
        break;

    case 'registrar':
        (new AuthController($db))->registrar();
        break; 
        
    case 'eliminar_pelicula':
        (new PeliculaController($db))->eliminar();
        break;        
    case 'login_admin':
        (new AdminAuthController($db))->login();
        break;

    case 'autenticar_admin':
        (new AdminAuthController($db))->autenticar();
        break;

    case 'logout_admin':
        (new AdminAuthController($db))->logout();
        break;    
        
}