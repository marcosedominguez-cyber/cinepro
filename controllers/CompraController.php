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
        $mapaAsientos = []; 

        $idFuncion = (int)($_GET['id_funcion'] ?? 0);

        if ($idFuncion > 0) {
            $funcionSeleccionada = $this->funcionModel->obtenerPorId($idFuncion);

            if ($funcionSeleccionada) {
                $asientosRaw = $this->asientoModel->obtenerEstadoAsientosPorFuncion($idFuncion);
                
                foreach ($asientosRaw as $asiento) {
                    $mapaAsientos[$asiento['Fila']][] = $asiento;
                }
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
        // AHORA RECIBIMOS UN ARRAY DE ASIENTOS
        $idAsientos = $_POST['id_asientos'] ?? []; 
        $metodoPago = trim($_POST['metodo_pago'] ?? 'Efectivo');

        $funcion = $this->funcionModel->obtenerPorId($idFuncion);

        // Validamos que sea un array y no esté vacío
        if (!$funcion || empty($idAsientos) || !is_array($idAsientos)) {
            $_SESSION['error_compra'] = 'Debes seleccionar al menos un asiento.';
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;
        }

        $this->db->beginTransaction();

        try {
            // Se crea UNA SOLA COMPRA global
            $idCompra = $this->compraModel->crear($idCliente);

            // CICLO: Guardamos un TICKET por cada asiento seleccionado
            foreach ($idAsientos as $idAsiento) {
                $idAsientoInt = (int)$idAsiento;
                if ($idAsientoInt <= 0) continue;

                $ok = $this->ticketModel->crear(
                    $idFuncion,
                    $idCompra,
                    $idAsientoInt,
                    $metodoPago,
                    (float)$funcion['Precio_Base']
                );

                if (!$ok) {
                    throw new Exception('Alguien más ya compró uno de los asientos seleccionados.');
                }
            }

            $this->db->commit();

            // Mostramos cuántos tickets compró
            $cantidad = count($idAsientos);
            $_SESSION['success_compra'] = "¡Éxito! Compra de $cantidad ticket(s) realizada correctamente.";
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;

        } catch (Throwable $e) {
            $this->db->rollBack();

            $_SESSION['error_compra'] = 'No se pudo procesar la compra: ' . $e->getMessage();
            header('Location: index.php?accion=compra&id_funcion=' . $idFuncion);
            exit;
        }
    }
}