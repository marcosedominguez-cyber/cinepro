<?php
session_start();

require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/VentaController.php';

$accion = $_GET['accion'] ?? 'home';

switch ($accion) {
    case 'home':
        (new HomeController())->home();
        break;

    case 'cartelera':
        (new HomeController())->cartelera();
        break;

    case 'compra':
        (new VentaController())->formulario();
        break;

    case 'procesar_compra':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new VentaController())->procesarCompra();
        } else {
            header('Location: index.php?accion=compra');
        }
        break;

    case 'admin':
        (new AdminController())->panel();
        break;

    case 'guardar_pelicula':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AdminController())->guardarPelicula();
        } else {
            header('Location: index.php?accion=admin');
        }
        break;

    case 'guardar_sala':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AdminController())->guardarSala();
        } else {
            header('Location: index.php?accion=admin');
        }
        break;

    case 'guardar_funcion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AdminController())->guardarFuncion();
        } else {
            header('Location: index.php?accion=admin');
        }
        break;

    case 'guardar_asientos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AdminController())->guardarAsientos();
        } else {
            header('Location: index.php?accion=admin');
        }
        break;

    default:
        (new HomeController())->home();
        break;
}