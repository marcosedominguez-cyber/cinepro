<?php

class Ticket
{
    private PDO $conn;
    private string $table = "ticket_compra";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crear(
        int $idFuncion,
        int $idCompra,
        int $idAsiento,
        string $metodoPago,
        float $pagoTotal
    ): bool {
        $sqlCheck = "SELECT COUNT(*) AS total
                     FROM {$this->table}
                     WHERE ID_Funcion = :id_funcion
                     AND ID_Asiento = :id_asiento";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([
            ':id_funcion' => $idFuncion,
            ':id_asiento' => $idAsiento
        ]);

        if ((int)$stmtCheck->fetch()['total'] > 0) {
            return false;
        }

        $sql = "INSERT INTO {$this->table}
                (Fecha_Pago, Metodo_Pago, Pago_Total, ID_Compra, ID_Funcion, ID_Asiento)
                VALUES (NOW(), :metodo_pago, :pago_total, :id_compra, :id_funcion, :id_asiento)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':metodo_pago' => $metodoPago,
            ':pago_total' => $pagoTotal,
            ':id_compra' => $idCompra,
            ':id_funcion' => $idFuncion,
            ':id_asiento' => $idAsiento
        ]);
    }

    public function listarVendidos(): array
    {
        $sql = "SELECT 
                    t.ID_Ticket,
                    p.Titulo,
                    c.Nombre_Cliente,
                    c.Apellido_Cliente,
                    f.Fecha_Funcion,
                    f.Hora_Funcion,
                    a.Fila,
                    a.Numero_Asiento,
                    t.Metodo_Pago,
                    t.Pago_Total
                FROM {$this->table} t
                INNER JOIN funcion f ON f.ID_Funcion = t.ID_Funcion
                INNER JOIN pelicula p ON p.ID_Pelicula = f.ID_Pelicula
                INNER JOIN asiento a ON a.ID_Asiento = t.ID_Asiento
                INNER JOIN compra co ON co.ID_Compra = t.ID_Compra
                INNER JOIN cliente c ON c.ID_Cliente = co.ID_Cliente
                ORDER BY t.ID_Ticket DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}