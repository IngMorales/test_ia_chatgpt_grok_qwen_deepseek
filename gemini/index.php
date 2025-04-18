<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas Interactivo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="hero">
        <div class="hero-content">
            <h1>Organiza tu Mundo</h1>
            <p>Crea y gestiona tus listas de tareas de forma visual e intuitiva.</p>
            <div id="list-creator-initial" class="list-creator-initial animated-entry">
                <h2>Empieza creando tu primera lista:</h2>
                <form id="add-list-form-initial">
                    <input type="text" id="new-list-name-initial" placeholder="Nombre de la nueva lista" required>
                    <input type="date" id="new-list-due-date-initial" title="Fecha de Vencimiento (Opcional)">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Crear Lista</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container">
        <section id="lists-section">
            <h2>Mis Listas</h2>
             <form id="add-list-form-main" class="add-list-form-main" style="display: none;">
                 <input type="text" id="new-list-name-main" placeholder="Nombre de la nueva lista" required>
                 <input type="date" id="new-list-due-date-main" title="Fecha de Vencimiento (Opcional)">
                 <button type="submit" class="btn btn-secondary"><i class="fas fa-plus"></i> Añadir Otra Lista</button>
             </form>
            <div id="lists-container">
                </div>
        </section>

        <section id="status-overview">
            <h2>Resumen de Estado</h2>
            <div class="overview-grid">
                <div class="overview-card pending">
                    <h3><i class="fas fa-hourglass-half"></i> Pendientes / Próximas</h3>
                    <p id="pending-count">0</p>
                </div>
                <div class="overview-card completed">
                    <h3><i class="fas fa-check-circle"></i> Completadas</h3>
                    <p id="completed-count">0</p>
                </div>
                <div class="overview-card overdue">
                    <h3><i class="fas fa-exclamation-triangle"></i> Vencidas</h3>
                    <p id="overdue-count">0</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gestor de Tareas Creativo. Creado con fines demostrativos.</p>
        <?php
            // En esta versión con localStorage, PHP no tiene un rol activo más allá de servir la página.
            // En una versión completa, PHP manejaría la base de datos, autenticación, etc.
        ?>
    </footer>

    <script src="script.js"></script>
</body>
</html>