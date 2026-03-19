<?php

class PeliculaController
{
    private PDO $db;
    private Pelicula $peliculaModel;
    private Funcion $funcionModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->peliculaModel = new Pelicula($db);
        $this->funcionModel = new Funcion($db);
    }

    public function cartelera(): void
    {
        $peliculas = $this->peliculaModel->listar();
        $funciones = $this->funcionModel->listarConPeliculaYSala();

        require __DIR__ . '/../views/cartelera.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $duracion = (int)($_POST['duracion'] ?? 0);
            $sinopsis = trim($_POST['sinopsis'] ?? '');
            $rutaImagen = null;

            if (!empty($_FILES['imagen']['name'])) {
                $directorio = __DIR__ . '/../public/img/peliculas/';

                if (!is_dir($directorio)) {
                    mkdir($directorio, 0777, true);
                }

                $nombreOriginal = $_FILES['imagen']['name'];
                $tmp = $_FILES['imagen']['tmp_name'];
                $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($extension, $extPermitidas, true)) {
                    $nombreNuevo = uniqid('pelicula_', true) . '.' . $extension;
                    $rutaCompleta = $directorio . $nombreNuevo;

                    if (move_uploaded_file($tmp, $rutaCompleta)) {
                        $rutaImagen = 'public/img/peliculas/' . $nombreNuevo;
                    }
                }
            }

            if ($titulo !== '' && $duracion > 0) {
                $this->peliculaModel->crear($titulo, $duracion, $sinopsis, $rutaImagen);
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }
}