<?php

class Compra
{
    private PDO $conn;
    private string $table = "compra";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crear(int $idCliente): int
    {
        $sql = "INSERT INTO {$this->table} (ID_Cliente)
                VALUES (:id_cliente)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_cliente' => $idCliente
        ]);

        return (int)$this->conn->lastInsertId();
    }
}