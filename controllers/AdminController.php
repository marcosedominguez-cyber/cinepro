<?php

class AdminController
{
    private PDO $db;
    private Pelicula $peliculaModel;
    private Sala $salaModel;
    private Funcion $funcionModel;
    private Ticket $ticketModel;
    private Asiento $asientoModel;
    private Genero $generoModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->peliculaModel = new Pelicula($db);
        $this->salaModel = new Sala($db);
        $this->funcionModel = new Funcion($db);
        $this->ticketModel = new Ticket($db);
        $this->asientoModel = new Asiento($db);
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

    public function index(): void
    {
        $this->verificarAdmin();

        $peliculas = $this->peliculaModel->listar();
        $salas = $this->salaModel->listar();
        $tickets = $this->ticketModel->listarVendidos();
        $generos = $this->generoModel->listar();

        require __DIR__ . '/../views/admin.php';
    }

    public function guardarSala(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $capacidad = (int)($_POST['capacidad'] ?? 0);

            if ($nombre !== '' && $capacidad > 0) {
                $ok = $this->salaModel->crear($nombre, $capacidad);

                if ($ok) {
                    $_SESSION['success_admin'] = 'Sala guardada correctamente.';
                } else {
                    $_SESSION['error_admin'] = 'No se pudo guardar la sala.';
                }
            } else {
                $_SESSION['error_admin'] = 'Datos inválidos para crear la sala.';
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarAsientos(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idSala = (int)($_POST['id_sala'] ?? 0);
            $cantidadFilas = (int)($_POST['cantidad_filas'] ?? 0);
            $asientosPorFila = (int)($_POST['asientos_por_fila'] ?? 0);

            if ($idSala > 0 && $cantidadFilas > 0 && $asientosPorFila > 0) {
                $ok = $this->asientoModel->crearPorSala($idSala, $cantidadFilas, $asientosPorFila);

                if ($ok) {
                    $_SESSION['success_admin'] = 'Asientos creados correctamente.';
                } else {
                    $_SESSION['error_admin'] = 'No se pudieron crear los asientos. Verifica la capacidad de la sala o si ya tiene asientos registrados.';
                }
            } else {
                $_SESSION['error_admin'] = 'Datos inválidos para crear los asientos.';
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarFuncion(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = trim($_POST['fecha_funcion'] ?? '');
            $hora = trim($_POST['hora_funcion'] ?? '');
            $precio = (float)($_POST['precio_base'] ?? 0);
            $idPelicula = (int)($_POST['id_pelicula'] ?? 0);
            $idSala = (int)($_POST['id_sala'] ?? 0);

            if ($fecha !== '' && $hora !== '' && $precio > 0 && $idPelicula > 0 && $idSala > 0) {
                $ok = $this->funcionModel->crear($fecha, $hora, $precio, $idPelicula, $idSala);

                if ($ok) {
                    $_SESSION['success_admin'] = 'Función guardada correctamente.';
                } else {
                    $_SESSION['error_admin'] = 'No se pudo guardar la función porque se cruza con otra en la misma sala. Se considera la duración de la película más 15 minutos de limpieza.';
                }
            } else {
                $_SESSION['error_admin'] = 'Datos incompletos para crear la función.';
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }
}