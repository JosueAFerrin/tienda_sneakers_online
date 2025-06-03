<?php
include '../includes/db_connect.php';
include '../includes/head.php';
include '../includes/menu.php';

// Obtener productos de la base de datos
$conn = getConnection();
$query = "SELECT * FROM products";
$result = $conn->query($query);
?>

<div class="container">
    <h1 class="my-4">Catálogo de Tenis Deportivos</h1>
    <div class="row">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Ver Detalles</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php
include '../includes/footer.php';
?>