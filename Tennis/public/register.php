<?php
include '../includes/head.php';
include '../includes/menu.php';
include '../includes/db_connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    // Validación básica
    if (!empty($username) && !empty($password) && !empty($email)) {
        // Verificar si el nombre de usuario o el correo electrónico ya existen
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "El nombre de usuario o el correo electrónico ya existen.";
        } else {
            // Hashear la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Preparar la declaración SQL
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                // Registro exitoso, redirigir a la página de inicio
                header("Location: index.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>
<style>
    .register-card {
        max-width: 450px;
        margin: 60px auto 0 auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(79,140,255,0.10);
        padding: 32px 28px;
    }
    .register-card h2 {
        color: #4f8cff;
        font-weight: bold;
        margin-bottom: 24px;
        text-align: center;
    }
    .register-card .btn-primary {
        width: 100%;
        border-radius: 20px;
        font-weight: bold;
    }
</style>
<div class="container">
    <div class="register-card">
        <h2>Registro</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nombre de usuario:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Registrar</button>
        </form>
    </div>
</div>
<?php
include '../includes/footer.php';
?>