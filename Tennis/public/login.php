<?php
session_start();

require_once '../includes/db_connect.php';
require_once '../../src/Auth.php';

use Grupo05\Tenis\Auth;

$error = '';

$conn = getConnection();
if (!$conn) {
    die('Error de conexión a la base de datos');
}

$auth = new Auth($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $auth->login($username, $password);

    if ($user) {
        // Login correcto: guarda datos en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // Redirección, si hay parámetro redirect, úsalo
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header("Location: $redirect");
        exit();
    } else {
        // Error de login
        $error = $auth->error;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../includes/head.php'; ?>
    <title>Iniciar Sesión</title>
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        }
        .login-card {
            max-width: 400px;
            margin: 60px auto 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(79,140,255,0.10);
            padding: 32px 28px;
        }
        .login-card h2 {
            color: #4f8cff;
            font-weight: bold;
            margin-bottom: 24px;
            text-align: center;
        }
        .login-card .btn-primary {
            width: 100%;
            border-radius: 20px;
            font-weight: bold;
        }
        .login-card p {
            text-align: center;
            margin-top: 18px;
        }
    </style>
</head>
<body>
    <?php include '../includes/menu.php'; ?>
    <div class="container">
        <div class="login-card">
            <h2>Iniciar Sesión</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . htmlspecialchars($_GET['redirect']) : ''; ?>">
                <div class="form-group">
                    <label for="username">Nombre de usuario:</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Iniciar Sesión</button>
            </form>
            <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>.</p>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
