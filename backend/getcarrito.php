<?php
// getcarrito.php

header('Content-Type: application/json');

// Incluir archivo de conexión a la base de datos
include 'database.php'; // Asegúrate de que este archivo contiene la conexión PDO en $conn
// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Verificar si el usuario está autenticado (puedes agregar validación si es necesario)
$usuarioId = $_GET['usuarioId'] ?? null; // Asumiendo que el ID del usuario se pasa por GET

if (!$usuarioId) {
    echo json_encode(['success' => false, 'message' => 'Usuario no especificado.']);
    exit;
}

try {
    // Obtener el carrito del usuario, incluyendo det_id
    $query = "
        SELECT dc.det_id, c.car_id, c.car_total, p.pro_nombre, p.pro_precio, dc.det_cantidad, dc.det_subtotal
        FROM carrito c
        JOIN detallecarrito dc ON c.car_id = dc.det_car_id
        JOIN producto p ON dc.det_pro_id = p.pro_id
        WHERE c.car_usu_id = :usuarioId
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener los productos del carrito
    $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($carrito)) {
        echo json_encode(['success' => false, 'message' => 'Carrito vacío o no encontrado.']);
    } else {
        // Responder con los productos en el carrito y el total
        $carritoTotal = $carrito[0]['car_total']; // El total del carrito es el mismo para todos los productos
        echo json_encode([
            'success' => true,
            'carritoTotal' => $carritoTotal,
            'productos' => $carrito
        ]);
    }

} catch (Exception $e) {
    // Respuesta de error en caso de fallo
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
