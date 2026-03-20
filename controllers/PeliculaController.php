<?php

class PeliculaController
{
    private PDO $db;
    private Pelicula $peliculaModel;
    private Funcion $funcionModel;
    private Genero $generoModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->peliculaModel = new Pelicula($db);
        $this->funcionModel = new Funcion($db);
        $this->generoModel = new Genero($db);
    }

    private function verificarAdmin(): void
    {
        if (!isset($_SESSION['id_admin'])) {
            $_SESSION['error_admin_login'] = 'Debes iniciar sesión como administrador.';
            header('Location: index.php?accion=login_admin');
            exit;
        }
    }

    public function cartelera(): void
    {
        $peliculas = $this->peliculaModel->listar();
        $funciones = $this->funcionModel->listarConPeliculaYSala();

        require __DIR__ . '/../views/cartelera.php';
    }

    public function guardar(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $duracion = (int)($_POST['duracion'] ?? 0);
            $sinopsis = trim($_POST['sinopsis'] ?? '');
            $idsGeneros = $_POST['generos'] ?? [];
            $nuevoGenero = trim($_POST['nuevo_genero'] ?? '');
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
                $this->db->beginTransaction();

                try {
                    $idPelicula = $this->peliculaModel->crear($titulo, $duracion, $sinopsis, $rutaImagen);

                    if ($nuevoGenero !== '') {
                        $generoExistente = $this->generoModel->obtenerPorNombre($nuevoGenero);

                        if ($generoExistente) {
                            $idsGeneros[] = $generoExistente['ID_Genero'];
                        } else {
                            $this->generoModel->crear($nuevoGenero);
                            $nuevo = $this->generoModel->obtenerPorNombre($nuevoGenero);
                            if ($nuevo) {
                                $idsGeneros[] = $nuevo['ID_Genero'];
                            }
                        }
                    }

                    $idsGeneros = array_unique(array_map('intval', $idsGeneros));
                    $this->generoModel->asignarAGenero($idPelicula, $idsGeneros);

                    $this->db->commit();
                    $_SESSION['success_admin'] = 'Película guardada correctamente.';
                } catch (Throwable $e) {
                    $this->db->rollBack();
                    $_SESSION['error_admin'] = 'No se pudo guardar la película.';
                }
            } else {
                $_SESSION['error_admin'] = 'Datos inválidos para guardar la película.';
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id > 0) {
            try {
                $pelicula = $this->peliculaModel->obtenerPorId($id);

                if ($pelicula) {
                    if (!empty($pelicula['Imagen'])) {
                        $rutaFisica = __DIR__ . '/../' . $pelicula['Imagen'];
                        if (file_exists($rutaFisica)) {
                            unlink($rutaFisica);
                        }
                    }

                    $this->peliculaModel->eliminar($id);
                    $_SESSION['success_admin'] = 'Película eliminada correctamente.';
                }
            } catch (Throwable $e) {
                $_SESSION['error_admin'] = 'No se pudo eliminar la película.';
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }
}