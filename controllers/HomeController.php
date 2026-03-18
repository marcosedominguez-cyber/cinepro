<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pelicula.php';
require_once __DIR__ . '/../models/Funcion.php';

class HomeController
{
    public function home(): void
    {
        require __DIR__ . '/../views/home.php';
    }

    public function cartelera(): void
    {
        $database = new Database();
        $db = $database->conectar();

        $peliculaModel = new Pelicula($db);
        $funcionModel = new Funcion($db);

        $peliculas = $peliculaModel->listar();
        $funciones = $funcionModel->listar();

        require __DIR__ . '/../views/cartelera.php';
    }
}