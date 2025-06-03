<?php
include '../includes/head.php';
include '../includes/menu.php';
include '../includes/db_connect.php';
$conn = getConnection();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$correo_usuario = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$mensaje = '';
$mostrar_exito = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $metodo_pago = $_POST['metodo_pago'];

    if (empty($nombre) || empty($correo) || empty($direccion) || empty($metodo_pago)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios.</div>";
    } else {
        if ($metodo_pago == 'tarjeta') {
            $num_tarjeta = $_POST['num_tarjeta'];
            $expiracion = $_POST['expiracion'];
            $cvv = $_POST['cvv'];
            if (!preg_match('/^[0-9]{16}$/', $num_tarjeta) ||
                !preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiracion) ||
                !preg_match('/^[0-9]{3,4}$/', $cvv)) {
                $mensaje = "<div class='alert alert-danger'>Datos de tarjeta inválidos.</div>";
            }
        } elseif ($metodo_pago == 'paypal') {
            $paypal_email = $_POST['paypal_email'];
            if (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
                $mensaje = "<div class='alert alert-danger'>Correo de PayPal inválido.</div>";
            }
        }
    }

    // Guardar pedido en la base de datos
    if (empty($mensaje)) {
        // Calcular total
        $total = 0;
        $cart_items = [];
        if (!empty($_SESSION['cart'])) {
            $ids = implode(',', array_keys($_SESSION['cart']));
            $query = "SELECT * FROM products WHERE id IN ($ids)";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $row['quantity'] = $_SESSION['cart'][$row['id']];
                $cart_items[] = $row;
                $total += $row['price'] * $row['quantity'];
            }
        }

        // Insertar en orders
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
        $stmt->bind_param("id", $_SESSION['user_id'], $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insertar en order_items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }
        $stmt->close();

        // Eliminar productos del carrito en la base de datos
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        // Limpiar carrito de la sesión
        unset($_SESSION['cart']);

        $mostrar_exito = true;
    }
}
?>

<div class="container">
    <h2>Finalizar compra</h2>
    <?php if (!empty($mensaje)) echo $mensaje; ?>
    <?php if ($mostrar_exito): ?>
        <div id="modal-espera" class="modal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9999;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div id="espera-contenido">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <h4>Comprobando tarjeta...</h4>
                    </div>
                    <div id="exito-contenido" style="display:none;">
                        <h4>¡Pago completado!</h4>
                        <p>¡Gracias, <?php echo htmlspecialchars($nombre); ?>! Tu pedido ha sido registrado.</p>
                        <p>Enviaremos una confirmación a <?php echo htmlspecialchars($correo); ?>.</p>
                        <a href="index.php" class="btn btn-success mt-3">Volver al inicio</a>
                    </div>
                </div>
            </div>
        </div>
        <script>
        setTimeout(function() {
            document.getElementById('espera-contenido').style.display = 'none';
            document.getElementById('exito-contenido').style.display = 'block';
        }, 5000);
        </script>
    <?php else: ?>
    <form method="POST" action="checkout.php" id="checkoutForm" autocomplete="off">
        <div class="form-group">
            <label for="nombre">Nombre completo:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="correo">Correo electrónico:</label>
            <input type="email" class="form-control" id="correo" name="correo" required value="<?php echo htmlspecialchars($correo_usuario); ?>">
        </div>
        <div class="form-group">
            <label for="direccion">Dirección de envío:</label>
            <textarea class="form-control" id="direccion" name="direccion" required></textarea>
        </div>
        <div class="form-group">
            <label for="metodo_pago">Método de pago:</label>
            <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                <option value="">Selecciona un método</option>
                <option value="tarjeta">Tarjeta de crédito/débito</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>
        <div id="pago_tarjeta" style="display:none;">
            <div class="form-group">
                <label for="num_tarjeta">Número de tarjeta (16 dígitos):</label>
                <input type="text" class="form-control" id="num_tarjeta" name="num_tarjeta" maxlength="16" pattern="[0-9]{16}">
            </div>
            <div class="form-group">
                <label for="expiracion">Fecha de expiración (MM/AA):</label>
                <input type="text" class="form-control" id="expiracion" name="expiracion" placeholder="MM/AA" maxlength="5" pattern="(0[1-9]|1[0-2])\/[0-9]{2}">
            </div>
            <div class="form-group">
                <label for="cvv">CVV (3 o 4 dígitos):</label>
                <input type="text" class="form-control" id="cvv" name="cvv" maxlength="4" pattern="[0-9]{3,4}">
            </div>
        </div>
        <div id="pago_paypal" style="display:none;">
            <div class="form-group">
                <label for="paypal_email">Correo de PayPal:</label>
                <input type="email" class="form-control" id="paypal_email" name="paypal_email">
            </div>
        </div>
        <button type="submit" class="btn btn-success">Completar compra</button>
    </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('metodo_pago').addEventListener('change', function() {
    var tarjeta = document.getElementById('pago_tarjeta');
    var paypal = document.getElementById('pago_paypal');
    tarjeta.style.display = this.value === 'tarjeta' ? 'block' : 'none';
    paypal.style.display = this.value === 'paypal' ? 'block' : 'none';
    if(this.value !== 'tarjeta') {
        document.getElementById('num_tarjeta').value = '';
        document.getElementById('expiracion').value = '';
        document.getElementById('cvv').value = '';
    }
    if(this.value !== 'paypal') {
        document.getElementById('paypal_email').value = '';
    }
});

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    var metodo = document.getElementById('metodo_pago').value;
    if(metodo === 'tarjeta') {
        var num = document.getElementById('num_tarjeta').value;
        var exp = document.getElementById('expiracion').value;
        var cvv = document.getElementById('cvv').value;
        if(!/^[0-9]{16}$/.test(num) || !/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(exp) || !/^[0-9]{3,4}$/.test(cvv)) {
            alert('Por favor ingresa datos válidos de tarjeta.');
            e.preventDefault();
        }
    }
    if(metodo === 'paypal') {
        var email = document.getElementById('paypal_email').value;
        if(!/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/.test(email)) {
            alert('Por favor ingresa un correo válido de PayPal.');
            e.preventDefault();
        }
    }
});
</script>

<?php
include '../includes/footer.php';
?>