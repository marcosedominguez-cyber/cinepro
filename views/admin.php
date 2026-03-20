<?php
if (!isset($_SESSION['id_admin'])) {
    header('Location: index.php?accion=login_admin');
    exit;
}
?>

<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="mb-4">Panel administrador</h2>

<?php if (isset($_SESSION['success_admin'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_admin']) ?>
    </div>
    <?php unset($_SESSION['success_admin']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_admin'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error_admin']) ?>
    </div>
    <?php unset($_SESSION['error_admin']); ?>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">

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

                    <div class="mb-3">
                        <label class="form-label">Géneros existentes</label>
                        <select name="generos[]" class="form-select" multiple>
                            <?php foreach ($generos as $genero): ?>
                                <option value="<?= (int)$genero['ID_Genero'] ?>">
                                    <?= htmlspecialchars($genero['Nombre_Genero']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Puedes seleccionar uno o varios.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nuevo género</label>
                        <input type="text" name="nuevo_genero" class="form-control" placeholder="Ej. Ciencia ficción">
                        <small class="text-muted">Si escribes uno nuevo, se creará automáticamente.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagen / Poster</label>
                        <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <button class="btn btn-primary w-100">Guardar película</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Películas registradas</h5>

                <?php if (!empty($peliculas)): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($peliculas as $pelicula): ?>
                            <div class="border rounded p-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <div style="width: 90px; flex-shrink: 0;">
                                        <?php if (!empty($pelicula['Imagen'])): ?>
                                            <img 
                                                src="<?= htmlspecialchars($pelicula['Imagen']) ?>" 
                                                alt="<?= htmlspecialchars($pelicula['Titulo']) ?>"
                                                class="img-fluid rounded"
                                                style="height: 120px; width: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                                 style="height: 120px;">
                                                Sin imagen
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($pelicula['Titulo']) ?></h6>
                                        <p class="mb-1 text-muted">Duración: <?= htmlspecialchars((string)$pelicula['Duracion']) ?> min</p>

                                        <?php if (!empty($pelicula['Generos'])): ?>
                                            <p class="mb-1">
                                                <strong>Géneros:</strong>
                                                <?= htmlspecialchars(implode(', ', $pelicula['Generos'])) ?>
                                            </p>
                                        <?php endif; ?>

                                        <p class="mb-2 small"><?= htmlspecialchars($pelicula['Sinopsis'] ?? '') ?></p>

                                        <a 
                                            href="index.php?accion=eliminar_pelicula&id=<?= (int)$pelicula['ID_Pelicula'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta película? Esto también eliminará sus funciones y tickets relacionados.');">
                                            Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-muted">No hay películas registradas.</p>
                <?php endif; ?>
            </div>
        </div>

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

    <div class="col-lg-6">

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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Historial de funciones creadas</h5>

                <?php if (!empty($funciones)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Película</th>
                                    <th>Sala</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Precio</th>
                                    <th>Admin que la creó</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($funciones as $funcion): ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars((string)$funcion['ID_Funcion']) ?></td>
                                        <td><?= htmlspecialchars($funcion['Titulo']) ?></td>
                                        <td><?= htmlspecialchars($funcion['Sala']) ?></td>
                                        <td><?= htmlspecialchars($funcion['Fecha_Funcion']) ?></td>
                                        <td><?= htmlspecialchars($funcion['Hora_Funcion']) ?></td>
                                        <td>Bs <?= htmlspecialchars((string)$funcion['Precio_Base']) ?></td>
                                        <td><?= htmlspecialchars($funcion['Admin_Creador'] ?? 'No registrado') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-muted">Aún no hay funciones registradas.</p>
                <?php endif; ?>
            </div>
        </div>

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