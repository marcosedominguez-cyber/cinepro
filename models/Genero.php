<?php

class Genero
{
    private PDO $conn;
    private string $table = "genero_pelicula";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listar(): array
    {
        $sql = "SELECT ID_Genero, Nombre_Genero
                FROM {$this->table}
                ORDER BY Nombre_Genero ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function crear(string $nombre): bool
    {
        $sql = "INSERT INTO {$this->table} (Nombre_Genero)
                VALUES (:nombre)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nombre' => $nombre
        ]);
    }

    public function obtenerPorNombre(string $nombre): ?array
    {
        $sql = "SELECT ID_Genero, Nombre_Genero
                FROM {$this->table}
                WHERE Nombre_Genero = :nombre
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre
        ]);

        $data = $stmt->fetch();
        return $data ?: null;
    }

    public function asignarAGenero(int $idPelicula, array $idsGeneros): void
    {
        if (empty($idsGeneros)) {
            return;
        }

        $sql = "INSERT INTO pertenece_genero (ID_Pelicula, ID_Genero)
                VALUES (:id_pelicula, :id_genero)";
        $stmt = $this->conn->prepare($sql);

        foreach ($idsGeneros as $idGenero) {
            $stmt->execute([
                ':id_pelicula' => $idPelicula,
                ':id_genero' => (int)$idGenero
            ]);
        }
    }

    public function obtenerGenerosDePelicula(int $idPelicula): array
    {
        $sql = "SELECT g.Nombre_Genero
                FROM pertenece_genero pg
                INNER JOIN genero_pelicula g ON g.ID_Genero = pg.ID_Genero
                WHERE pg.ID_Pelicula = :id_pelicula
                ORDER BY g.Nombre_Genero ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_pelicula' => $idPelicula
        ]);

        return $stmt->fetchAll();
    }
}