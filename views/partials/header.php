<?php
$accionActual = $_GET['accion'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand brand-cine" href="index.php">
            <img src="public/img/logo.png" alt="Logo Cine" class="logo-cine">
            <span>Cine</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'home' ? 'active' : '' ?>" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'cartelera' ? 'active' : '' ?>" href="index.php?accion=cartelera">Cartelera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'compra' ? 'active' : '' ?>" href="index.php?accion=compra">Compra</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'login' ? 'active' : '' ?>" href="index.php?accion=login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'admin' ? 'active' : '' ?>" href="index.php?accion=admin">Admin</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 text-white">
                <?php if (isset($_SESSION['id_cliente'])): ?>
                    <span class="small">Hola, <?= htmlspecialchars($_SESSION['nombre_cliente']) ?></span>
                    <a href="index.php?accion=logout" class="btn btn-outline-light btn-sm">Salir</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">