<?php

class AuthController
{
    private Cliente $clienteModel;

    public function __construct(PDO $db)
    {
        $this->clienteModel = new Cliente($db);
    }

    public function login(): void
    {
        require __DIR__ . '/../views/login.php';
    }

    public function registro(): void
    {
        require __DIR__ . '/../views/registro.php';
    }

    public function registrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=registro');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        if ($nombre === '' || $apellido === '' || $correo === '' || $numero === '' || strlen($contrasena) < 6) {
            header('Location: index.php?accion=registro');
            exit;
        }

        $clienteExistente = $this->clienteModel->buscarPorCorreo($correo);

        if ($clienteExistente) {
            header('Location: index.php?accion=login');
            exit;
        }

        $idCliente = $this->clienteModel->crear(
            $nombre,
            $apellido,
            $correo,
            $contrasena,
            $numero
        );

        $_SESSION['id_cliente'] = $idCliente;
        $_SESSION['nombre_cliente'] = $nombre . ' ' . $apellido;

        header('Location: index.php?accion=compra');
        exit;
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=login');
            exit;
        }

        $correo = trim($_POST['correo'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        $cliente = $this->clienteModel->autenticar($correo, $contrasena);

        if ($cliente) {
            $_SESSION['id_cliente'] = $cliente['ID_Cliente'];
            $_SESSION['nombre_cliente'] = $cliente['Nombre_Cliente'] . ' ' . $cliente['Apellido_Cliente'];

            header('Location: index.php?accion=compra');
            exit;
        }

        header('Location: index.php?accion=login');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}