<?php
// Incluir cabecera, menú y clases necesarias
include '../includes/head.php';
include '../includes/menu.php';
require_once '../includes/db_connect.php';
require_once '../../src/Auth.php';

use Grupo05\Tenis\Auth;

$conn = getConnection();
$auth = new Auth($conn);
$error = '';

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    // Usar la clase Auth para registrar
    if ($auth->register($username, $password, $email)) {
        header("Location: index.php"); // Registro exitoso
        exit();
    } else {
        $error = $auth->error; // Mensaje de error desde la clase Auth
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

<?php include '../includes/footer.php'; ?>
