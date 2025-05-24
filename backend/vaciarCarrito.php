<?php
// vaciarCarrito.php

header('Content-Type: application/json');

// Incluir archivo de conexión a la base de datos
include 'database.php'; // Asegúrate de que este archivo contiene la conexión PDO en $conn
// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Obtener el ID del usuario
$usuarioId = json_decode(file_get_contents('php://input'))->usuarioId ?? null;

if (!$usuarioId) {
    echo json_encode(['success' => false, 'message' => 'Usuario no especificado.']);
    exit;
}

try {
    // Obtener el carrito del usuario
    $query = "SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto'"; // Suponiendo que el carrito está 'abierto'
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();

    $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carrito) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el carrito para este usuario.']);
        exit;
    }

    // Eliminar todos los productos del carrito en la tabla detallecarrito
    $query = "DELETE FROM detallecarrito WHERE det_car_id = :carritoId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':carritoId', $carrito['car_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Opcionalmente, puedes eliminar el carrito o actualizar su estado
    $query = "UPDATE carrito SET car_total = 0 WHERE car_id = :carritoId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':carritoId', $carrito['car_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Responder con éxito
    echo json_encode(['success' => true, 'message' => 'Carrito vacío con éxito.']);

} catch (Exception $e) {
    // Respuesta de error en caso de fallo
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
