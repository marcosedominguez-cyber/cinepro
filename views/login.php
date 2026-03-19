<?php require __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Iniciar Sesión</h3>

                <form method="POST" action="index.php?accion=autenticar">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted">¿No tienes cuenta?</span>
                    <a href="index.php?accion=registro" class="text-decoration-none">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>