<?php

class CompraController
{
    private PDO $db;
    private Funcion $funcionModel;
    private Asiento $asientoModel;
    private Compra $compraModel;
    private Ticket $ticketModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->funcionModel = new Funcion($db);
        $this->asientoModel = new Asiento($db);
        $this->compraModel = new Compra($db);
        $this->ticketModel = new Ticket($db);
    }

    public function index(): void
    {
        $funciones = $this->funcionModel->listarConPeliculaYSala();
        $funcionSeleccionada = null;
        $asientosDisponibles = [];

        $idFuncion = (int)($_GET['id_funcion'] ?? 0);

        if ($idFuncion > 0) {
            $funcionSeleccionada = $this->funcionModel->obtenerPorId($idFuncion);

            if ($funcionSeleccionada) {
                $asientosDisponibles = $this->asientoModel->obtenerDisponiblesPorFuncion($idFuncion);
            }
        }

        require __DIR__ . '/../views/compra.php';
    }

    public function procesarCompra(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=compra');
            exit;
        }

        if (!isset($_SESSION['id_cliente'])) {
            header('Location: index.php?accion=login');
            exit;
        }

        $idCliente = (int)$_SESSION['id_cliente'];
        $idFuncion = (int)($_POST['id_funcion'] ?? 0);
        $idAsiento = (int)($_POST['id_asiento'] ?? 0);
        $metodoPago = trim($_POST['metodo_pago'] ?? 'Efectivo');

        $funcion = $this->funcionModel->obtenerPorId($idFuncion);

        if (!$funcion || $idAsiento <= 0) {
            $_SESSION['error_compra'] = 'Datos de compra inválidos.';
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;
        }

        $this->db->beginTransaction();

        try {
            $idCompra = $this->compraModel->crear($idCliente);

            $ok = $this->ticketModel->crear(
                $idFuncion,
                $idCompra,
                $idAsiento,
                $metodoPago,
                (float)$funcion['Precio_Base']
            );

            if (!$ok) {
                throw new Exception('El asiento ya fue vendido.');
            }

            $this->db->commit();

            $_SESSION['success_compra'] = 'Compra realizada correctamente.';
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;

        } catch (Throwable $e) {
            $this->db->rollBack();

            $_SESSION['error_compra'] = 'No se pudo guardar la compra: ' . $e->getMessage();
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;
        }
    }
}