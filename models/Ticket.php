<?php
require_once __DIR__ . '/../config/database.php';

class Ticket
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function comprar(int $idCliente, int $idFuncion, int $idAsiento, string $metodoPago): array
    {
        if (!in_array($metodoPago, ['Efectivo', 'Tarjeta'], true)) {
            return ['ok' => false, 'mensaje' => 'Método de pago inválido.'];
        }

        $sqlFuncion = "SELECT ID_Funcion, Precio_Base, ID_Sala
                       FROM Funcion
                       WHERE ID_Funcion = :id_funcion";
        $stmtFuncion = $this->conn->prepare($sqlFuncion);
        $stmtFuncion->execute([':id_funcion' => $idFuncion]);
        $funcion = $stmtFuncion->fetch();

        if (!$funcion) {
            return ['ok' => false, 'mensaje' => 'La función no existe.'];
        }

        $precioOficial = (float)$funcion['Precio_Base'];
        $idSala = (int)$funcion['ID_Sala'];

        $sqlAsiento = "SELECT ID_Asiento
                       FROM Asiento
                       WHERE ID_Asiento = :id_asiento AND ID_Sala = :id_sala";
        $stmtAsiento = $this->conn->prepare($sqlAsiento);
        $stmtAsiento->execute([
            ':id_asiento' => $idAsiento,
            ':id_sala' => $idSala
        ]);
        $asiento = $stmtAsiento->fetch();

        if (!$asiento) {
            return ['ok' => false, 'mensaje' => 'El asiento no pertenece a la sala de esta función.'];
        }

        $sqlOcupado = "SELECT ID_Ticket
                       FROM Ticket_Compra
                       WHERE ID_Funcion = :id_funcion AND ID_Asiento = :id_asiento";
        $stmtOcupado = $this->conn->prepare($sqlOcupado);
        $stmtOcupado->execute([
            ':id_funcion' => $idFuncion,
            ':id_asiento' => $idAsiento
        ]);
        $ocupado = $stmtOcupado->fetch();

        if ($ocupado) {
            return ['ok' => false, 'mensaje' => 'Ese asiento ya fue vendido para esa función.'];
        }

        try {
            $this->conn->beginTransaction();

            $sqlCompra = "INSERT INTO Compra (ID_Cliente, Fecha_Compra)
                          VALUES (:id_cliente, NOW())";
            $stmtCompra = $this->conn->prepare($sqlCompra);
            $stmtCompra->execute([':id_cliente' => $idCliente]);
            $idCompra = (int)$this->conn->lastInsertId();

            $sqlTicket = "INSERT INTO Ticket_Compra
                          (Fecha_Pago, Metodo_Pago, Pago_Total, ID_Compra, ID_Funcion, ID_Asiento)
                          VALUES (NOW(), :metodo_pago, :pago_total, :id_compra, :id_funcion, :id_asiento)";
            $stmtTicket = $this->conn->prepare($sqlTicket);
            $stmtTicket->execute([
                ':metodo_pago' => $metodoPago,
                ':pago_total' => $precioOficial,
                ':id_compra' => $idCompra,
                ':id_funcion' => $idFuncion,
                ':id_asiento' => $idAsiento
            ]);
            $idTicket = (int)$this->conn->lastInsertId();

            $numeroFactura = 'FAC-' . str_pad((string)$idCompra, 5, '0', STR_PAD_LEFT);

            $sqlFactura = "INSERT INTO Factura (Numero_Factura, ID_Compra)
                           VALUES (:numero_factura, :id_compra)";
            $stmtFactura = $this->conn->prepare($sqlFactura);
            $stmtFactura->execute([
                ':numero_factura' => $numeroFactura,
                ':id_compra' => $idCompra
            ]);

            $this->conn->commit();

            return [
                'ok' => true,
                'mensaje' => 'Compra realizada correctamente.',
                'ID_Compra' => $idCompra,
                'ID_Ticket' => $idTicket,
                'Numero_Factura' => $numeroFactura,
                'Metodo_Pago' => $metodoPago,
                'Pago_Total' => $precioOficial
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['ok' => false, 'mensaje' => 'Error al procesar la compra: ' . $e->getMessage()];
        }
    }

    public function listarVendidos(): array
    {
        $sql = "SELECT
                    tc.ID_Ticket,
                    tc.Fecha_Pago,
                    tc.Metodo_Pago,
                    tc.Pago_Total,
                    p.Titulo,
                    f.Fecha_Funcion,
                    f.Hora_Funcion,
                    a.Fila,
                    a.Numero_Asiento,
                    c.Nombre_Cliente,
                    c.Apellido_Cliente
                FROM Ticket_Compra tc
                INNER JOIN Compra co ON tc.ID_Compra = co.ID_Compra
                INNER JOIN Cliente c ON co.ID_Cliente = c.ID_Cliente
                INNER JOIN Funcion f ON tc.ID_Funcion = f.ID_Funcion
                INNER JOIN Pelicula p ON f.ID_Pelicula = p.ID_Pelicula
                INNER JOIN Asiento a ON tc.ID_Asiento = a.ID_Asiento
                ORDER BY tc.ID_Ticket DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}