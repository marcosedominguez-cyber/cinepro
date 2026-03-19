<?php

class Pago
{
    private PDO $conn;
    private string $table = "compra";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crear(string $metodoPago, float $monto): int
    {
        $sql = "INSERT INTO {$this->table} (Metodo_Pago, Pago_Total, Fecha_Pago)
                VALUES (:metodo, :monto, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':metodo' => $metodoPago,
            ':monto' => $monto
        ]);

        return (int)$this->conn->lastInsertId();
    }
}