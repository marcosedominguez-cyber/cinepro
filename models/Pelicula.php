<?php

class Pelicula
{
    private PDO $conn;
    private string $table = "pelicula";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listar(): array
    {
        $sql = "SELECT ID_Pelicula, Titulo, Duracion, Sinopsis, Imagen
                FROM {$this->table}
                ORDER BY ID_Pelicula DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $peliculas = $stmt->fetchAll();

        foreach ($peliculas as &$pelicula) {
            $sqlGeneros = "SELECT g.Nombre_Genero
                           FROM pertenece_genero pg
                           INNER JOIN genero_pelicula g ON g.ID_Genero = pg.ID_Genero
                           WHERE pg.ID_Pelicula = :id_pelicula
                           ORDER BY g.Nombre_Genero ASC";
            $stmtGeneros = $this->conn->prepare($sqlGeneros);
            $stmtGeneros->execute([
                ':id_pelicula' => $pelicula['ID_Pelicula']
            ]);

            $generos = $stmtGeneros->fetchAll(PDO::FETCH_COLUMN);
            $pelicula['Generos'] = $generos;
        }

        return $peliculas;
    }

    public function crear(string $titulo, int $duracion, string $sinopsis = '', ?string $imagen = null): int
    {
        $sql = "INSERT INTO {$this->table} (Titulo, Duracion, Sinopsis, Imagen)
                VALUES (:titulo, :duracion, :sinopsis, :imagen)";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':titulo' => $titulo,
            ':duracion' => $duracion,
            ':sinopsis' => $sinopsis,
            ':imagen' => $imagen
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT ID_Pelicula, Titulo, Duracion, Sinopsis, Imagen
                FROM {$this->table}
                WHERE ID_Pelicula = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        return $data ?: null;
    }

    public function eliminar(int $id): bool
    {
        try {
            $this->conn->beginTransaction();

            $sqlTickets = "DELETE tc
                           FROM ticket_compra tc
                           INNER JOIN funcion f ON f.ID_Funcion = tc.ID_Funcion
                           WHERE f.ID_Pelicula = :id";
            $stmtTickets = $this->conn->prepare($sqlTickets);
            $stmtTickets->execute([':id' => $id]);

            $sqlFunciones = "DELETE FROM funcion
                             WHERE ID_Pelicula = :id";
            $stmtFunciones = $this->conn->prepare($sqlFunciones);
            $stmtFunciones->execute([':id' => $id]);

            $sqlRelacion = "DELETE FROM pertenece_genero
                            WHERE ID_Pelicula = :id";
            $stmtRelacion = $this->conn->prepare($sqlRelacion);
            $stmtRelacion->execute([':id' => $id]);

            $sqlPelicula = "DELETE FROM {$this->table}
                            WHERE ID_Pelicula = :id";
            $stmtPelicula = $this->conn->prepare($sqlPelicula);
            $stmtPelicula->execute([':id' => $id]);

            $this->conn->commit();
            return true;

        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}