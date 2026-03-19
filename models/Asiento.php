<?php

class Asiento
{
    private PDO $conn;
    private string $table = "asiento";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crearPorSala(int $idSala, int $cantidadFilas, int $asientosPorFila): bool
    {
        $filas = range('A', 'Z');
        $totalNecesario = $cantidadFilas * $asientosPorFila;

        $sqlSala = "SELECT Capacidad FROM sala WHERE ID_Sala = :id_sala";
        $stmtSala = $this->conn->prepare($sqlSala);
        $stmtSala->execute([':id_sala' => $idSala]);
        $sala = $stmtSala->fetch();

        if (!$sala) {
            return false;
        }

        if ($totalNecesario > (int)$sala['Capacidad']) {
            return false;
        }

        $sqlCheck = "SELECT COUNT(*) AS total FROM {$this->table} WHERE ID_Sala = :id_sala";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([':id_sala' => $idSala]);
        $existentes = (int)$stmtCheck->fetch()['total'];

        if ($existentes > 0) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (Fila, Numero_Asiento, ID_Sala)
                VALUES (:fila, :numero, :id_sala)";
        $stmt = $this->conn->prepare($sql);

        for ($i = 0; $i < $cantidadFilas; $i++) {
            $fila = $filas[$i];
            for ($j = 1; $j <= $asientosPorFila; $j++) {
                $stmt->execute([
                    ':fila' => $fila,
                    ':numero' => $j,
                    ':id_sala' => $idSala
                ]);
            }
        }

        return true;
    }

    public function obtenerDisponiblesPorFuncion(int $idFuncion): array
    {
        $sql = "SELECT a.ID_Asiento, a.Fila, a.Numero_Asiento
                FROM asiento a
                INNER JOIN funcion f ON f.ID_Sala = a.ID_Sala
                WHERE f.ID_Funcion = :id_funcion
                AND a.ID_Asiento NOT IN (
                    SELECT t.ID_Asiento
                    FROM ticket_compra t
                    WHERE t.ID_Funcion = :id_funcion
                )
                ORDER BY a.Fila, a.Numero_Asiento";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_funcion' => $idFuncion]);
        return $stmt->fetchAll();
    }

    public function obtenerEstadoAsientosPorFuncion(int $idFuncion): array
    {
        $sql = "SELECT a.ID_Asiento, a.Fila, a.Numero_Asiento,
                       CASE WHEN t.ID_Asiento IS NOT NULL THEN 1 ELSE 0 END AS Ocupado
                FROM asiento a
                INNER JOIN funcion f ON f.ID_Sala = a.ID_Sala
                LEFT JOIN ticket_compra t ON t.ID_Asiento = a.ID_Asiento AND t.ID_Funcion = :id_funcion
                WHERE f.ID_Funcion = :id_funcion
                ORDER BY a.Fila, CAST(a.Numero_Asiento AS UNSIGNED)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_funcion' => $idFuncion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}