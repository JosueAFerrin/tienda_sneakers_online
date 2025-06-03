<?php
namespace Grupo05\Tenis;

class Auth
{
    private $conn;
    public $error = '';

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function login(string $username, string $password)
    {
        $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->error = 'Error en la consulta.';
            return false;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            } else {
                $this->error = 'Contraseña incorrecta.';
            }
        } else {
            $this->error = 'No existe un usuario con ese nombre.';
        }

        return false;
    }

    public function register(string $username, string $password, string $email)
    {
        if (empty($username) || empty($password) || empty($email)) {
            $this->error = "Todos los campos son obligatorios.";
            return false;
        }

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $this->error = "El nombre de usuario o el correo electrónico ya existen.";
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            return true;
        } else {
            $this->error = "Error al registrar: " . $stmt->error;
            return false;
        }
    }
}
