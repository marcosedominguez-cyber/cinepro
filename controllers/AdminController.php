<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pelicula.php';
require_once __DIR__ . '/../models/Sala.php';
require_once __DIR__ . '/../models/Funcion.php';
require_once __DIR__ . '/../models/Asiento.php';
require_once __DIR__ . '/../models/Ticket.php';

class AdminController
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->conectar();
    }

    public function panel(): void
    {
        $peliculaModel = new Pelicula($this->db);
        $salaModel = new Sala($this->db);
        $funcionModel = new Funcion($this->db);
        $ticketModel = new Ticket($this->db);

        $peliculas = $peliculaModel->listar();
        $salas = $salaModel->listar();
        $funciones = $funcionModel->listar();
        $tickets = $ticketModel->listarVendidos();

        require __DIR__ . '/../views/admin.php';
    }

    public function guardarPelicula(): void
    {
        $titulo = trim($_POST['titulo'] ?? '');
        $duracion = (int)($_POST['duracion'] ?? 0);
        $sinopsis = trim($_POST['sinopsis'] ?? '');

        if ($titulo === '' || $duracion <= 0) {
            $_SESSION['error'] = 'Datos inválidos para la película.';
            header('Location: index.php?accion=admin');
            exit;
        }

        $peliculaModel = new Pelicula($this->db);
        $ok = $peliculaModel->crear($titulo, $duracion, $sinopsis);

        $_SESSION[$ok ? 'ok' : 'error'] = $ok
            ? 'Película registrada correctamente.'
            : 'No se pudo registrar la película.';

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarSala(): void
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $capacidad = (int)($_POST['capacidad'] ?? 0);

        if ($nombre === '' || $capacidad <= 0) {
            $_SESSION['error'] = 'Datos inválidos para la sala.';
            header('Location: index.php?accion=admin');
            exit;
        }

        $salaModel = new Sala($this->db);
        $ok = $salaModel->crear($nombre, $capacidad);

        $_SESSION[$ok ? 'ok' : 'error'] = $ok
            ? 'Sala registrada correctamente.'
            : 'No se pudo registrar la sala.';

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarFuncion(): void
    {
        $fecha = trim($_POST['fecha_funcion'] ?? '');
        $hora = trim($_POST['hora_funcion'] ?? '');
        $precio = (float)($_POST['precio_base'] ?? 0);
        $idPelicula = (int)($_POST['id_pelicula'] ?? 0);
        $idSala = (int)($_POST['id_sala'] ?? 0);

        if ($fecha === '' || $hora === '' || $precio <= 0 || $idPelicula <= 0 || $idSala <= 0) {
            $_SESSION['error'] = 'Datos inválidos para la función.';
            header('Location: index.php?accion=admin');
            exit;
        }

        $funcionModel = new Funcion($this->db);
        $ok = $funcionModel->crear($fecha, $hora, $precio, $idPelicula, $idSala);

        $_SESSION[$ok ? 'ok' : 'error'] = $ok
            ? 'Función registrada correctamente.'
            : 'No se pudo registrar la función.';

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarAsientos(): void
    {
        $idSala = (int)($_POST['id_sala'] ?? 0);
        $cantidadFilas = (int)($_POST['cantidad_filas'] ?? 0);
        $asientosPorFila = (int)($_POST['asientos_por_fila'] ?? 0);

        if ($idSala <= 0 || $cantidadFilas <= 0 || $asientosPorFila <= 0) {
            $_SESSION['error'] = 'Datos inválidos para crear asientos.';
            header('Location: index.php?accion=admin');
            exit;
        }

        $asientoModel = new Asiento($this->db);
        $resultado = $asientoModel->crearMasivo($idSala, $cantidadFilas, $asientosPorFila);

        $_SESSION[$resultado['ok'] ? 'ok' : 'error'] = $resultado['mensaje'];

        header('Location: index.php?accion=admin');
        exit;
    }
}