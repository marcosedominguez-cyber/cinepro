<?php require __DIR__ . '/partials/header.php'; ?>

<div class="hero-box shadow text-center">
    <img src="public/img/logo.png" alt="Logo Cine" class="logo-home">
    <div>
        <h1 class="display-5 fw-bold mb-3">Sistema de Cine</h1>
        <p class="lead mb-4">
            Administra cartelera, funciones, asientos y compras desde una interfaz web agradable.
        </p>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="index.php?accion=cartelera" class="btn btn-primary btn-lg">Ver cartelera</a>
            <a href="index.php?accion=compra" class="btn btn-success btn-lg">Comprar ticket</a>
            <a href="index.php?accion=admin" class="btn btn-dark btn-lg">Panel admin</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>