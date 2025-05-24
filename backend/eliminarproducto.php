<?php
// eliminarProducto.php

header('Content-Type: application/json');
include 'database.php'; // Asegúrate de que este archivo contiene la conexión PDO en $conn

$database = new Database();
$conn = $database->getConnection();

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$usuarioId = $data['usuarioId'] ?? null;
$productoId = $data['productoId'] ?? null;

// Verificar que se recibieron los parámetros
if (!$usuarioId || !$productoId) {
    echo json_encode(['success' => false, 'message' => 'Usuario o producto no especificado.']);
    exit;
}

try {
    // Lógica para eliminar el producto del carrito
    $query = "DELETE FROM detallecarrito WHERE det_car_id = (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId) AND det_pro_id = :productoId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
    
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el producto.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
