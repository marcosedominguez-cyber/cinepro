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
        $nombre = trim($nombre);
        $apellido = trim($apellido);
        $correo = trim($correo);
        $password = trim($password);
        $numero = $numero !== null ? trim($numero) : null;

        if ($nombre === '') {
            throw new InvalidArgumentException('El nombre es obligatorio.');
        }

        if ($apellido === '') {
            throw new InvalidArgumentException('El apellido es obligatorio.');
        }

        if (!$this->correoValido($correo)) {
            throw new InvalidArgumentException('El correo no tiene un formato válido.');
        }

        if (strlen($password) < 6) {
            throw new InvalidArgumentException('La contraseña debe tener al menos 6 caracteres.');
        }

        if ($numero !== null && $numero !== '' && !$this->numeroValido($numero)) {
            throw new InvalidArgumentException('El número solo puede contener números y espacios.');
        }

        if ($this->buscarPorCorreo($correo)) {
            throw new InvalidArgumentException('Ya existe un cliente con ese correo.');
        }

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
            ':numero' => ($numero === '') ? null : $numero
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function autenticar(string $correo, string $contrasena): ?array
    {
        $correo = trim($correo);
        $contrasena = trim($contrasena);

        if (!$this->correoValido($correo)) {
            return null;
        }

        $cliente = $this->buscarPorCorreo($correo);

        if (!$cliente) {
            return null;
        }

        if (!password_verify($contrasena, $cliente['Contrasena'])) {
            return null;
        }

        return $cliente;
    }

    private function correoValido(string $correo): bool
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function numeroValido(string $numero): bool
    {
        return preg_match('/^[0-9 ]+$/', $numero) === 1;
    }
}