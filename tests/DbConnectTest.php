<?php
use PHPUnit\Framework\TestCase;

class DbConnectTest extends TestCase
{   
    // Test de conexión a la base de datos
    public function testDatabaseConnection()
    {
        require_once __DIR__ . '/../Tennis/includes/db_connect.php';
        $conn = getConnection();
        
        $this->assertNotNull($conn, 'La conexión a la base de datos debería establecerse correctamente.');
        $this->assertInstanceOf(mysqli::class, $conn);
    }
}
