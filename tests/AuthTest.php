<?php
use PHPUnit\Framework\TestCase;
use Grupo05\Tenis\Auth;

class AuthTest extends TestCase
{
    private $conn;
    private $auth;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../Tennis/includes/db_connect.php';
        $this->conn = getConnection();

        // Inserta usuario de prueba
        $passwordHash = password_hash('testpass', PASSWORD_DEFAULT);
        $this->conn->query("INSERT INTO users (username, password, email) VALUES ('testuser', '$passwordHash', 'test@example.com')");

        $this->auth = new Auth($this->conn);
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM users WHERE username = 'testuser'");
        $this->conn->close();
    }

    // Test de inicio de sesión
    public function testLoginCorrecto()
    {
        $result = $this->auth->login('testuser', 'testpass');
        $this->assertIsArray($result);
        $this->assertEquals('testuser', $result['username']);
    }

    // Test de inicio de sesión con contraseña incorrecta
    public function testLoginPasswordIncorrecta()
    {
        $result = $this->auth->login('testuser', 'wrongpass');
        $this->assertFalse($result);
        $this->assertEquals('Contraseña incorrecta.', $this->auth->error);
    }

    // Test de inicio de sesión con usuario que no existe
    public function testLoginUsuarioNoExiste()
    {
        $result = $this->auth->login('nouser', 'any');
        $this->assertFalse($result);
        $this->assertEquals('No existe un usuario con ese nombre.', $this->auth->error);
    }
}
