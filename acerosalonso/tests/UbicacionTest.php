<?php

use PHPUnit\Framework\TestCase;

// Incluir las funciones a probar
require_once __DIR__ . '/../Ubicacion/funciones_ubicaciones.php';

class UbicacionTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        // Crea una conexión de prueba (usa DB de pruebas)
        $this->conn = new mysqli("localhost", "root", "", "test");
    }
    public function testInsertarUbicacionRetornaTrue()
    {
        $descripcion = "Zona de prueba";
        $imagen = "imagen_prueba.jpg";
        $url = "https://c.tenor.com/7Wr359XtEtEAAAAd/tenor.gif";
        $resultado = insertarUbicacion($this->conn, $descripcion, $imagen, $url);
        $this->assertTrue($resultado);
    }
}