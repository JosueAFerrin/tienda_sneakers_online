<?php
// filepath: Tennis/includes/menu.php
session_start();
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Smash Tenis Store</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="products.php">Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">Carrito</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">Mis pedidos</a>
                </li>
                <li class="nav-item">
                    <span class="nav-link">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Iniciar Sesión</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Registrarse</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>