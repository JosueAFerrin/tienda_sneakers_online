<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../includes/head.php'; ?>
    <title>Inicio - Smash Tenis Store</title>
</head>
<body>
    <?php include '../includes/menu.php'; ?>
    <?php
    include '../includes/db_connect.php';
    // Obtener solo los 3 primeros productos como destacados
    $query = "SELECT * FROM products ORDER BY id ASC LIMIT 3";
    $result = $conn->query($query);
    ?>

    <div class="store-header">
        <h1>Smash Tenis Store</h1>
        <p>¡Bienvenido a la tienda líder en tenis deportivos! Encuentra los mejores modelos, ofertas exclusivas y la mejor atención para potenciar tu juego.</p>
    </div>

    <div class="container my-5">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <img src="../assets/img/balen.jpg" alt="Tienda de tenis deportivos" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h2 class="mb-3" style="color:#4f8cff;">¡Equípate como un profesional!</h2>
                <p>En <strong>Smash Tennis Store</strong> te ofrecemos tenis deportivos de las mejores marcas, asesoría personalizada y envíos rápidos a todo el país. ¡Haz tu pedido hoy y vive la experiencia de jugar con lo mejor!</p>
                <ul>
                    <li>Envíos en 48h</li>
                    <li>Descuentos para miembros</li>
                    <li>Productos originales garantizados</li>
                    <li>Atención personalizada</li>
                </ul>
                <a href="products.php" class="btn btn-buy mt-2">Ver catálogo completo</a>
            </div>
        </div>

        <h2 class="mb-4" style="color:#4f8cff;">Productos Destacados</h2>
        <div class="featured-products mb-5">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-buy">Ver más</a>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="row text-center mb-5">
            <div class="col-md-4">
                <img src="../assets/img/envio_rapido.png" alt="Envío rápido" style="width:60px;">
                <h5 class="mt-2">Envío rápido</h5>
                <p>Recibe tus tenis en menos de 48 horas en cualquier parte del país.</p>
            </div>
            <div class="col-md-4">
                <img src="../assets/img/calidad.png" alt="Marcas originales" style="width:60px;">
                <h5 class="mt-2">Marcas originales</h5>
                <p>Trabajamos solo con marcas reconocidas y productos 100% originales.</p>
            </div>
            <div class="col-md-4">
                <img src="../assets/img/suport.png" alt="Soporte" style="width:60px;">
                <h5 class="mt-2">Soporte experto</h5>
                <p>¿Dudas? Nuestro equipo te asesora para elegir el mejor tenis para ti.</p>
            </div>
        </div>

        <div class="bg-light rounded p-4 mb-5">
            <h3 class="mb-3" style="color:#4f8cff;">Testimonios de clientes</h3>
            <div class="row">
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p class="mb-0">"Excelente atención y los tenis llegaron rapidísimo. ¡Muy recomendados!"</p>
                        <footer class="blockquote-footer">Carlos M.</footer>
                    </blockquote>
                </div>
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p class="mb-0">"La calidad de los productos es increíble. Volveré a comprar."</p>
                        <footer class="blockquote-footer">Lucía G.</footer>
                    </blockquote>
                </div>
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p class="mb-0">"Me ayudaron a elegir el mejor modelo para mi juego. ¡Gracias Smash Tennis!"</p>
                        <footer class="blockquote-footer">Andrés R.</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>