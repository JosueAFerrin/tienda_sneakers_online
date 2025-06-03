<?php
use PHPUnit\Framework\TestCase;
use Grupo05\Tenis\Auth;

require_once __DIR__ . '/../Tennis/includes/db_connect.php';
require_once __DIR__ . '/../src/Auth.php';

class RegisterTest extends TestCase
{
    private $conn;
    private $auth;

    protected function setUp(): void
    {
        $this->conn = getConnection();
        $this->auth = new Auth($this->conn);
    }

    // Test de registro exitoso
    public function testRegisterSuccess()
    {
        $username = 'testuser_' . rand(1000, 9999);
        $email = $username . '@test.com';
        $password = 'Test123';

        $result = $this->auth->register($username, $password, $email);
        $this->assertTrue($result);
    }

    // Test de registro con usuario ya existente
    public function testRegisterDuplicate()
    {
        $username = 'usuario_existente';
        $email = 'correo_existente@test.com';
        $password = 'Test123';

        // Primer registro
        $this->auth->register($username, $password, $email);
        // Segundo intento
        $result = $this->auth->register($username, $password, $email);

        $this->assertFalse($result);
        $this->assertEquals("El nombre de usuario o el correo electrónico ya existen.", $this->auth->error);
    }

    // Test de registro con campos vacíos
    public function testRegisterEmptyFields()
    {
        $result = $this->auth->register('', '', '');
        $this->assertFalse($result);
        $this->assertEquals("Todos los campos son obligatorios.", $this->auth->error);
    }
}
