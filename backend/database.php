<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Database {
    private $host = 'localhost:8889'; // Cambia esto si es necesario
    private $db_name = 'aplicacionesproyecto'; // Cambia esto por el nombre de tu base de datos
    private $username = 'root'; // Cambia esto por tu usuario
    private $password = 'root'; // Cambia esto por tu contraseña
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
