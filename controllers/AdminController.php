<?php

class AdminController
{
    private PDO $db;
    private Pelicula $peliculaModel;
    private Sala $salaModel;
    private Funcion $funcionModel;
    private Ticket $ticketModel;
    private Asiento $asientoModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->peliculaModel = new Pelicula($db);
        $this->salaModel = new Sala($db);
        $this->funcionModel = new Funcion($db);
        $this->ticketModel = new Ticket($db);
        $this->asientoModel = new Asiento($db);
    }

    public function index(): void
    {
        $peliculas = $this->peliculaModel->listar();
        $salas = $this->salaModel->listar();
        $tickets = $this->ticketModel->listarVendidos();

        require __DIR__ . '/../views/admin.php';
    }

    public function guardarSala(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $capacidad = (int)($_POST['capacidad'] ?? 0);

            if ($nombre !== '' && $capacidad > 0) {
                $this->salaModel->crear($nombre, $capacidad);
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarAsientos(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idSala = (int)($_POST['id_sala'] ?? 0);
            $cantidadFilas = (int)($_POST['cantidad_filas'] ?? 0);
            $asientosPorFila = (int)($_POST['asientos_por_fila'] ?? 0);

            if ($idSala > 0 && $cantidadFilas > 0 && $asientosPorFila > 0) {
                $this->asientoModel->crearPorSala($idSala, $cantidadFilas, $asientosPorFila);
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }

    public function guardarFuncion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha_funcion'] ?? '';
            $hora = $_POST['hora_funcion'] ?? '';
            $precio = (float)($_POST['precio_base'] ?? 0);
            $idPelicula = (int)($_POST['id_pelicula'] ?? 0);
            $idSala = (int)($_POST['id_sala'] ?? 0);

            if ($fecha !== '' && $hora !== '' && $precio > 0 && $idPelicula > 0 && $idSala > 0) {
                $this->funcionModel->crear($fecha, $hora, $precio, $idPelicula, $idSala);
            }
        }

        header('Location: index.php?accion=admin');
        exit;
    }
}