<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container">
    <div class="hero-box shadow-sm text-center">
        <div>
            <h1 class="display-4 mb-3">CINE <span style="color: #db0000;">PYTHON</span></h1>
            <p class="lead mb-5">
                La mejor experiencia cinematográfica. <br>
                Administra cartelera, funciones y asientos con total facilidad.
            </p>
            
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="index.php?accion=cartelera" class="btn btn-primary-cinema btn-lg shadow-sm">
                    Ver cartelera
                </a>
                <a href="index.php?accion=compra" class="btn btn-dark-cinema btn-lg shadow-sm">
                    Comprar ticket
                </a>
                <a href="index.php?accion=admin" class="btn btn-outline-cinema btn-lg">
                    Panel admin
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>