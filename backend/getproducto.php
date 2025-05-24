<?php
header('Content-Type: application/json');
require_once 'database.php'; // Incluye tu archivo de conexión a la base de datos

// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Consulta para obtener los productos junto con la cantidad desde la tabla inventario
$sql = "SELECT p.pro_id, p.pro_nombre, p.pro_descripcion, p.pro_precio, i.inv_cantidad
        FROM producto p
        JOIN inventario i ON p.pro_id = i.inv_pro_id";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Obtener los resultados
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica si se encontraron productos
if ($productos) {
    // Si hay productos, devolverlos en formato JSON
    echo json_encode([
        'success' => true,
        'productos' => $productos
    ]);
} else {
    // Si no se encontraron productos, devolver un mensaje
    echo json_encode([
        'success' => false,
        'message' => 'No se encontraron productos'
    ]);
}

// Cerrar conexión
$stmt = null;
$conn = null;
?>
