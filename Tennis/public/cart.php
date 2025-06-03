<?php
include '../includes/head.php';
include '../includes/menu.php';
include '../includes/db_connect.php';
$conn = getConnection();
session_start();

// Sincronizar carrito de la base de datos al iniciar sesión
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    if (empty($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        $cart_query = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
        $cart_query->bind_param("i", $user_id);
        $cart_query->execute();
        $cart_result = $cart_query->get_result();
        while ($row = $cart_result->fetch_assoc()) {
            $_SESSION['cart'][$row['product_id']] = $row['quantity'];
        }
        $cart_query->close();
    }
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Agregar producto al carrito
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Guardar en la base de datos si está logueado
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        // Verificar si ya existe
        $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Actualizar cantidad
            $stmt_update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt_update->bind_param("iii", $_SESSION['cart'][$product_id], $user_id, $product_id);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            // Insertar nuevo
            $stmt_insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("iii", $user_id, $product_id, $_SESSION['cart'][$product_id]);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
        $stmt->close();
    }
}

// Actualizar cantidades
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$product_id]);
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

// Eliminar producto del carrito
if (isset($_POST['remove_product'])) {
    $remove_id = $_POST['remove_product'];
    unset($_SESSION['cart'][$remove_id]);
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $remove_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener productos del carrito
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cart_items[] = $row;
    }
}

// Calcular total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="container min-vh-100 d-flex flex-column">
    <h1>Carrito de Compras</h1>
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">Tu carrito está vacío.</div>
    <?php endif; ?>
    <form method="post" action="" class="flex-grow-1" id="cartForm">
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>
                            <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control cantidad-input" style="width:80px;">
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <button type="submit" name="remove_product" value="<?php echo $item['id']; ?>" class="btn btn-danger">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if (!empty($cart_items)): ?>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th colspan="2">$<?php echo number_format($total, 2); ?></th>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
        <?php if (!empty($cart_items)): ?>
            <div class="d-flex justify-content-end align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="checkout.php" class="btn btn-success mr-2">Proceder al pago</a>
                <?php else: ?>
                    <div class="alert alert-info mb-0 mr-2">
                        Por favor <a href="login.php?redirect=cart.php">inicia sesión</a> para continuar con el pago.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <input type="hidden" name="update_cart" value="1">
    </form>
</div>

<script>
document.querySelectorAll('.cantidad-input').forEach(function(input) {
    input.addEventListener('change', function() {
        document.getElementById('cartForm').submit();
    });
});
</script>

<?php
include '../includes/footer.php';
?>