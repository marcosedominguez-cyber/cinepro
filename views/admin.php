<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="mb-4">Panel administrador</h2>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA -->
    <div class="col-lg-6">

        <!-- AGREGAR PELÍCULA -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Agregar película</h5>

                <form method="POST" action="index.php?accion=guardar_pelicula" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Duración</label>
                        <input type="number" name="duracion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sinopsis</label>
                        <textarea name="sinopsis" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- NUEVO: IMAGEN -->
                    <div class="mb-3">
                        <label class="form-label">Imagen / Poster</label>
                        <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <button class="btn btn-primary w-100">Guardar película</button>
                </form>
            </div>
        </div>

        <!-- AGREGAR SALA -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Agregar sala</h5>

                <form method="POST" action="index.php?accion=guardar_sala">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Capacidad</label>
                        <input type="number" name="capacidad" class="form-control" required>
                    </div>

                    <button class="btn btn-dark w-100">Guardar sala</button>
                </form>
            </div>
        </div>

        <!-- CREAR ASIENTOS -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">Crear asientos</h5>

                <form method="POST" action="index.php?accion=guardar_asientos">

                    <div class="mb-3">
                        <label class="form-label">Sala</label>
                        <select name="id_sala" class="form-select" required>
                            <option value="">Seleccione una sala</option>
                            <?php foreach ($salas as $sala): ?>
                                <option value="<?= (int)$sala['ID_Sala'] ?>">
                                    <?= htmlspecialchars($sala['Nombre']) ?> | Capacidad: <?= htmlspecialchars((string)$sala['Capacidad']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad de filas</label>
                        <input type="number" name="cantidad_filas" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Asientos por fila</label>
                        <input type="number" name="asientos_por_fila" class="form-control" required>
                    </div>

                    <button class="btn btn-success w-100">Crear asientos</button>
                </form>
            </div>
        </div>

    </div>

    <!-- COLUMNA DERECHA -->
    <div class="col-lg-6">

        <!-- AGREGAR FUNCIÓN -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Agregar función</h5>

                <form method="POST" action="index.php?accion=guardar_funcion">

                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha_funcion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hora</label>
                        <input type="time" name="hora_funcion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio base</label>
                        <input type="number" step="0.01" name="precio_base" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Película</label>
                        <select name="id_pelicula" class="form-select" required>
                            <option value="">Seleccione una película</option>
                            <?php foreach ($peliculas as $pelicula): ?>
                                <option value="<?= (int)$pelicula['ID_Pelicula'] ?>">
                                    <?= htmlspecialchars($pelicula['Titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sala</label>
                        <select name="id_sala" class="form-select" required>
                            <option value="">Seleccione una sala</option>
                            <?php foreach ($salas as $sala): ?>
                                <option value="<?= (int)$sala['ID_Sala'] ?>">
                                    <?= htmlspecialchars($sala['Nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-warning w-100">Guardar función</button>
                </form>
            </div>
        </div>

        <!-- TICKETS VENDIDOS -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">Tickets vendidos</h5>

                <?php if (count($tickets) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Película</th>
                                    <th>Cliente</th>
                                    <th>Función</th>
                                    <th>Asiento</th>
                                    <th>Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars((string)$ticket['ID_Ticket']) ?></td>
                                        <td><?= htmlspecialchars($ticket['Titulo']) ?></td>
                                        <td><?= htmlspecialchars($ticket['Nombre_Cliente'] . ' ' . $ticket['Apellido_Cliente']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($ticket['Fecha_Funcion']) ?><br>
                                            <?= htmlspecialchars($ticket['Hora_Funcion']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($ticket['Fila'] . $ticket['Numero_Asiento']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($ticket['Metodo_Pago']) ?><br>
                                            Bs <?= htmlspecialchars((string)$ticket['Pago_Total']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-muted">Aún no hay tickets vendidos.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>