<?php
include '../includes/head.php';
include '../includes/menu.php';
include '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=orders.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener pedidos del usuario
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<div class="container my-5">
    <h2>Mis pedidos</h2>
    <?php if ($orders_result->num_rows == 0): ?>
        <div class="alert alert-info">No has realizado ningún pedido aún.</div>
    <?php else: ?>
        <?php while ($order = $orders_result->fetch_assoc()): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Pedido #<?php echo $order['id']; ?> - Fecha: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?> - Total: $<?php echo number_format($order['total'], 2); ?>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $order_id = $order['id'];
                        $items_stmt = $conn->prepare(
                            "SELECT oi.*, p.name FROM order_items oi
                             JOIN products p ON oi.product_id = p.id
                             WHERE oi.order_id = ?"
                        );
                        $items_stmt->bind_param("i", $order_id);
                        $items_stmt->execute();
                        $items_result = $items_stmt->get_result();
                        while ($item = $items_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endwhile; $items_stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
