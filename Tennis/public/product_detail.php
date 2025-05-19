<?php
include '../includes/head.php';
include '../includes/menu.php';
include '../includes/db_connect.php';

session_start();

// Obtener detalles del producto
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<h2>Producto no encontrado</h2>";
    exit;
}
?>

<div class="container">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid mb-3" style="max-width:300px;">
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <p>Precio: $<?php echo number_format($product['price'], 2); ?></p>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="cart.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" name="add_to_cart" class="btn btn-primary">Agregar al Carrito</button>
        </form>
    <?php else: ?>
        <div class="alert alert-info">Por favor <a href="login.php">inicia sesión</a> para comprar.</div>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>