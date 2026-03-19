<?php

class Cliente
{
    private PDO $conn;
    private string $table = "cliente";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE Correo = :correo
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $data = $stmt->fetch();

        return $data ?: null;
    }

    public function crear(
        string $nombre,
        string $apellido,
        string $correo,
        string $password,
        ?string $numero = null
    ): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$this->table}
                (Nombre_Cliente, Apellido_Cliente, Correo, Contrasena, Numero)
                VALUES (:nombre, :apellido, :correo, :contrasena, :numero)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':correo' => $correo,
            ':contrasena' => $hash,
            ':numero' => $numero
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function autenticar(string $correo, string $contrasena): ?array
    {
        $cliente = $this->buscarPorCorreo($correo);

        if (!$cliente) {
            return null;
        }

        if (!password_verify($contrasena, $cliente['Contrasena'])) {
            return null;
        }

        return $cliente;
    }
}