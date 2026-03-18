<?php
require_once __DIR__ . '/../config/database.php';

class Cliente
{
    private PDO $conn;
    private string $table = "Cliente";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $sql = "SELECT ID_Cliente, Nombre_Cliente, Apellido_Cliente, Numero, Correo, Contrasena
                FROM {$this->table}
                WHERE Correo = :correo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $cliente = $stmt->fetch();

        return $cliente ?: null;
    }

    public function crear(string $nombre, string $apellido, string $numero, string $correo): int
    {
        $passwordTemporal = password_hash('Temporal123', PASSWORD_BCRYPT);

        $sql = "INSERT INTO {$this->table}
                (Nombre_Cliente, Apellido_Cliente, Numero, Correo, Contrasena)
                VALUES (:nombre, :apellido, :numero, :correo, :contrasena)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':numero' => $numero,
            ':correo' => $correo,
            ':contrasena' => $passwordTemporal
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function obtenerOCrear(string $nombre, string $apellido, string $numero, string $correo): int
    {
        $cliente = $this->buscarPorCorreo($correo);

        if ($cliente) {
            return (int)$cliente['ID_Cliente'];
        }

        return $this->crear($nombre, $apellido, $numero, $correo);
    }
}