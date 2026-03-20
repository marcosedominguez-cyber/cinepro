<?php
$accionActual = $_GET['accion'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine Python | Sistema de Cine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="public/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom py-3">
    <div class="container">
        <a class="navbar-brand brand-cine-python" href="index.php">
            CINE <span>PYTHON</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'home' ? 'active' : '' ?>" href="index.php">INICIO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'cartelera' ? 'active' : '' ?>" href="index.php?accion=cartelera">PELÍCULAS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $accionActual === 'compra' ? 'active' : '' ?>" href="index.php?accion=compra">COMPRA</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['id_cliente'])): ?>
                    <span class="text-muted small">Hola, <strong><?= htmlspecialchars($_SESSION['nombre_cliente']) ?></strong></span>
                    <a href="index.php?accion=logout" class="btn btn-outline-danger btn-sm px-3">SALIR</a>
                <?php else: ?>
                    <a href="index.php?accion=login" class="btn btn-dark-cinema btn-sm px-4 <?= $accionActual === 'login' ? 'active' : '' ?>">LOGIN</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['id_admin'])): ?>
                    <span class="text-muted small">Admin: <strong><?= htmlspecialchars($_SESSION['nombre_admin']) ?></strong></span>
                    <a href="index.php?accion=admin" class="btn btn-outline-dark btn-sm px-4 <?= $accionActual === 'admin' ? 'active' : '' ?>">PANEL ADMIN</a>
                    <a href="index.php?accion=logout_admin" class="btn btn-warning btn-sm px-3">SALIR ADMIN</a>
                <?php else: ?>
                    <a href="index.php?accion=login_admin" class="btn btn-outline-dark btn-sm px-4 <?= $accionActual === 'login_admin' ? 'active' : '' ?>">ADMIN</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="container py-5">