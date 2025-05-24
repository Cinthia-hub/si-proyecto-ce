<?php
header('Content-Type: application/json');

// Conectar a la base de datos
include 'database.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Obtener los datos del frontend
    $inputData = json_decode(file_get_contents('php://input'), true);
    $usuarioId = $inputData['usuarioId']; // ID del usuario
    $productoId = $inputData['productoId']; // ID del producto
    $cantidad = $inputData['cantidad']; // Cantidad a agregar

    // Verificar si el usuario tiene un carrito abierto
    $queryCarrito = "SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId";
    $stmtCarrito = $conn->prepare($queryCarrito);
    $stmtCarrito->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmtCarrito->execute();
    $carrito = $stmtCarrito->fetch(PDO::FETCH_ASSOC);

    // Si no existe un carrito, crearlo
    if (!$carrito) {
        $queryCrearCarrito = "INSERT INTO carrito (car_total, car_usu_id) VALUES (0, :usuarioId)";
        $stmtCrearCarrito = $conn->prepare($queryCrearCarrito);
        $stmtCrearCarrito->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtCrearCarrito->execute();
        $carritoId = $conn->lastInsertId();
    } else {
        $carritoId = $carrito['car_id'];
    }

    // Verificar si el producto ya está en el carrito
    $queryDetalle = "SELECT det_id, det_cantidad FROM detallecarrito WHERE det_car_id = :carritoId AND det_pro_id = :productoId";
    $stmtDetalle = $conn->prepare($queryDetalle);
    $stmtDetalle->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmtDetalle->bindParam(':productoId', $productoId, PDO::PARAM_INT);
    $stmtDetalle->execute();
    $detalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC);

    if ($detalle) {
        // Actualizar la cantidad si el producto ya está en el carrito
        $nuevaCantidad = $detalle['det_cantidad'] + $cantidad;
        $queryActualizarDetalle = "UPDATE detallecarrito SET det_cantidad = :cantidad WHERE det_id = :detalleId";
        $stmtActualizarDetalle = $conn->prepare($queryActualizarDetalle);
        $stmtActualizarDetalle->bindParam(':cantidad', $nuevaCantidad, PDO::PARAM_INT);
        $stmtActualizarDetalle->bindParam(':detalleId', $detalle['det_id'], PDO::PARAM_INT);
        $stmtActualizarDetalle->execute();
    } else {
        // Insertar el producto como un nuevo detalle
        $queryInsertarDetalle = "INSERT INTO detallecarrito (det_car_id, det_pro_id, det_cantidad, det_subtotal) VALUES (:carritoId, :productoId, :cantidad, 0)";
        $stmtInsertarDetalle = $conn->prepare($queryInsertarDetalle);
        $stmtInsertarDetalle->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
        $stmtInsertarDetalle->bindParam(':productoId', $productoId, PDO::PARAM_INT);
        $stmtInsertarDetalle->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmtInsertarDetalle->execute();
    }

    // Calcular el total del carrito
    $queryActualizarTotal = "
        UPDATE carrito 
        SET car_total = (SELECT SUM(det_cantidad * pro_precio) 
                         FROM detallecarrito 
                         INNER JOIN producto ON detallecarrito.det_pro_id = producto.pro_id 
                         WHERE detallecarrito.det_car_id = :carritoId)
        WHERE car_id = :carritoId";
    $stmtActualizarTotal = $conn->prepare($queryActualizarTotal);
    $stmtActualizarTotal->bindParam(':carritoId', $carritoId, PDO::PARAM_INT);
    $stmtActualizarTotal->execute();

    echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
