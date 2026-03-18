<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Funcion.php';
require_once __DIR__ . '/../models/Asiento.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Ticket.php';

class VentaController
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->conectar();
    }

    public function formulario(): void
    {
        $funcionModel = new Funcion($this->db);
        $asientoModel = new Asiento($this->db);

        $funciones = $funcionModel->listar();

        $idFuncion = isset($_GET['id_funcion']) ? (int)$_GET['id_funcion'] : 0;
        $funcionSeleccionada = null;
        $asientosDisponibles = [];

        if ($idFuncion > 0) {
            $funcionSeleccionada = $funcionModel->obtenerPorId($idFuncion);

            if ($funcionSeleccionada) {
                $asientosDisponibles = $asientoModel->listarDisponiblesPorFuncion($idFuncion);
            }
        }

        require __DIR__ . '/../views/compra.php';
    }

    public function procesarCompra(): void
    {
        $idFuncion = (int)($_POST['id_funcion'] ?? 0);
        $idAsiento = (int)($_POST['id_asiento'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $metodoPago = trim($_POST['metodo_pago'] ?? '');

        if (
            $idFuncion <= 0 ||
            $idAsiento <= 0 ||
            $nombre === '' ||
            $apellido === '' ||
            $correo === ''
        ) {
            $_SESSION['error'] = 'Todos los campos obligatorios deben completarse.';
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;
        }

        $clienteModel = new Cliente($this->db);
        $ticketModel = new Ticket($this->db);

        $idCliente = $clienteModel->obtenerOCrear($nombre, $apellido, $numero, $correo);
        $resultado = $ticketModel->comprar($idCliente, $idFuncion, $idAsiento, $metodoPago);

        $_SESSION[$resultado['ok'] ? 'ok' : 'error'] = $resultado['mensaje'];

        if ($resultado['ok']) {
            $_SESSION['detalle_compra'] = $resultado;
        }

        header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
        exit;
    }
}