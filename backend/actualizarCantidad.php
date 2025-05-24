<?php
// actualizarCantidad.php

header('Content-Type: application/json');

// Incluir archivo de conexión a la base de datos
include 'database.php'; 

// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Obtener los datos de la solicitud (en formato JSON)
$data = json_decode(file_get_contents("php://input"));

// Recuperar los parámetros enviados desde el frontend
$usuarioId = $data->usuarioId;
$det_id = $data->det_id;
$nuevaCantidad = $data->nuevaCantidad;

// Verificar si los parámetros son válidos
if (!$usuarioId || !$det_id || !$nuevaCantidad) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
    exit;
}

try {
    // Obtener el ID del producto (det_pro_id) desde el detalle del carrito
    $queryProducto = "
        SELECT det_pro_id 
        FROM detallecarrito 
        WHERE det_id = :det_id
    ";
    $stmt = $conn->prepare($queryProducto);
    $stmt->bindParam(':det_id', $det_id, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito.']);
        exit;
    }

    $pro_id = $producto['det_pro_id'];

    // Verificar cuántos productos hay disponibles en el inventario
    $queryInventario = "
        SELECT SUM(inv_cantidad) AS cantidad_disponible 
        FROM inventario 
        WHERE inv_pro_id = :pro_id 
        AND inv_accion = 'Agregar'
    ";
    $stmtInventario = $conn->prepare($queryInventario);
    $stmtInventario->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
    $stmtInventario->execute();
    $inventario = $stmtInventario->fetch(PDO::FETCH_ASSOC);

    $cantidadDisponible = $inventario['cantidad_disponible'] ?? 0;

    // Si la cantidad solicitada es mayor que la disponible, mostrar un error
    if ($nuevaCantidad > $cantidadDisponible) {
        echo json_encode(['success' => false, 'message' => 'No hay suficiente stock disponible para esta cantidad.']);
        exit;
    }

    // Actualizar la cantidad en el carrito
    $query = "
        UPDATE detallecarrito 
        SET det_cantidad = :nuevaCantidad 
        WHERE det_id = :det_id 
        AND det_car_id IN 
            (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId)
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nuevaCantidad', $nuevaCantidad, PDO::PARAM_INT);
    $stmt->bindParam(':det_id', $det_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();

    // Verificar si se realizó alguna actualización
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la cantidad.']);
    }

} catch (Exception $e) {
    // Respuesta de error en caso de fallo
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
