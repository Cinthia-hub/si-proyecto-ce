<?php    
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Configura tus datos de conexión
    $host = '127.0.0.1';
    $username = 'root';
    $password = 'root';
    $sqlFile = __DIR__ . '/../sql/sintaxis.sql';

    // Crear base de datos y tablas si no existen
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
            // Opcional: puedes comentar la línea de abajo si no quieres que siempre imprima eso
            // echo "Base de datos y tablas creadas exitosamente desde el archivo sql/sintaxis.sql.";
        }
    } catch (PDOException $e) {
        echo "Error de PDO al crear la base: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error general: " . $e->getMessage();
    }

    // Clase de conexión
    class Database {
        private $host = '127.0.0.1';
        private $db_name = 'aplicacionesproyecto';
        private $username = 'root';
        private $password = 'root';
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
