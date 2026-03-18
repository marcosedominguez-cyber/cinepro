<?php
require_once __DIR__ . '/../config/database.php';

class Pelicula
{
    private PDO $conn;
    private string $table = "Pelicula";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listar(): array
    {
        $sql = "SELECT ID_Pelicula, Titulo, Duracion, Sinopsis
                FROM {$this->table}
                ORDER BY Titulo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function crear(string $titulo, int $duracion, string $sinopsis): bool
    {
        $sql = "INSERT INTO {$this->table} (Titulo, Duracion, Sinopsis)
                VALUES (:titulo, :duracion, :sinopsis)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':titulo' => $titulo,
            ':duracion' => $duracion,
            ':sinopsis' => $sinopsis
        ]);
    }
}