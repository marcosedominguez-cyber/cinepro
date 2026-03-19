<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="mb-4">Cartelera</h2>

<div class="row g-4">
    <?php foreach ($peliculas as $pelicula): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 card-pelicula">
                <?php if (!empty($pelicula['Imagen'])): ?>
                    <img 
                        src="<?= htmlspecialchars($pelicula['Imagen']) ?>" 
                        class="card-img-top poster-pelicula" 
                        alt="<?= htmlspecialchars($pelicula['Titulo']) ?>">
                <?php else: ?>
                    <div class="poster-placeholder d-flex align-items-center justify-content-center">
                        <span>Sin imagen</span>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($pelicula['Titulo']) ?></h4>
                    <p class="text-muted mb-2">Duración: <?= htmlspecialchars((string)$pelicula['Duracion']) ?> min</p>
                    <p><?= htmlspecialchars($pelicula['Sinopsis'] ?? '') ?></p>

                    <h6 class="mt-4">Funciones</h6>

                    <?php
                    $hayFunciones = false;
                    foreach ($funciones as $funcion):
                        if ((int)$funcion['ID_Pelicula'] === (int)$pelicula['ID_Pelicula']):
                            $hayFunciones = true;
                    ?>
                        <div class="funcion-item">
                            <span><?= htmlspecialchars($funcion['Fecha_Funcion']) ?></span>
                            <span><?= htmlspecialchars($funcion['Hora_Funcion']) ?></span>
                            <span><?= htmlspecialchars($funcion['Sala']) ?></span>
                            <span>Bs <?= htmlspecialchars((string)$funcion['Precio_Base']) ?></span>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    ?>

                    <?php if (!$hayFunciones): ?>
                        <p class="text-muted mb-0">No hay funciones registradas.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>