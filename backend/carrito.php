<?php
header('Content-Type: application/json');
include 'database.php';
$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? null;
$productoId = $data['productoId'] ?? null;
$cantidad = $data['cantidad'] ?? null;

// ID fijo para el carrito general
$carritoId = 1;

// CRUD
if ($action == 'agregar') {
    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT det_id, det_cantidad FROM detallecarrito WHERE det_car_id = :carritoId AND det_pro_id = :productoId");
    $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
    $stmt->execute();
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($detalle) {
        $nuevaCantidad = $detalle['det_cantidad'] + $cantidad;
        $stmt = $conn->prepare("UPDATE detallecarrito SET det_cantidad = :cantidad WHERE det_id = :detalleId");
        $stmt->bindParam(':cantidad', $nuevaCantidad, PDO::PARAM_INT);
        $stmt->bindParam(':detalleId', $detalle['det_id'], PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO detallecarrito (det_car_id, det_pro_id, det_cantidad, det_subtotal) VALUES (:carritoId, :productoId, :cantidad, (SELECT pro_precio FROM producto WHERE pro_id = :productoId) * :cantidad)");
        $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
        $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();
    }
    echo json_encode(['success' => true]);
} elseif ($action == 'eliminar') {
    $stmt = $conn->prepare("DELETE FROM detallecarrito WHERE det_car_id = :carritoId AND det_id = :detalleId");
    $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmt->bindParam(':detalleId', $productoId, PDO::PARAM_INT); // productoId es det_id aquí
    $stmt->execute();
    echo json_encode(['success' => true]);
} elseif ($action == 'actualizar') {
    $stmt = $conn->prepare("UPDATE detallecarrito SET det_cantidad = :cantidad, det_subtotal = (SELECT pro_precio FROM producto WHERE pro_id = det_pro_id) * :cantidad WHERE det_car_id = :carritoId AND det_id = :detalleId");
    $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmt->bindParam(':detalleId', $productoId, PDO::PARAM_INT); // productoId es det_id aquí
    $stmt->execute();
    echo json_encode(['success' => true]);
} elseif ($action == 'vaciar') {
    $stmt = $conn->prepare("DELETE FROM detallecarrito WHERE det_car_id = :carritoId");
    $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(['success' => true]);
} elseif ($action == 'finalizar') {
    // Aquí puedes poner la lógica de finalizar compra (vaciar carrito, registrar venta, etc.)
    $stmt = $conn->prepare("DELETE FROM detallecarrito WHERE det_car_id = :carritoId");
    $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito.']);
} elseif ($action == 'listar') {
    $stmt = $conn->prepare("SELECT dc.det_id, p.pro_nombre, p.pro_precio, dc.det_cantidad, dc.det_subtotal FROM detallecarrito dc JOIN producto p ON dc.det_pro_id = p.pro_id WHERE dc.det_car_id = :carritoId");
    $stmt->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'productos' => $productos]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
}
?>