<?php
// finalizarCompra.php

header('Content-Type: application/json');

// Incluir archivo de conexión a la base de datos
include 'database.php'; 

// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Obtener los datos de la solicitud (en formato JSON)
$data = json_decode(file_get_contents("php://input"));

// Recuperar el ID del usuario
$usuarioId = $data->usuarioId;

// Verificar si el usuario ID es válido
if (!$usuarioId) {
    echo json_encode(['success' => false, 'message' => 'Usuario no especificado.']);
    exit;
}

try {
    // Iniciar una transacción
    $conn->beginTransaction();

    // 1. Obtener los productos y cantidades del carrito del usuario
    $queryCarrito = "
        SELECT dc.det_pro_id, dc.det_cantidad, p.pro_precio
        FROM detallecarrito dc
        JOIN carrito c ON c.car_id = dc.det_car_id
        JOIN producto p ON p.pro_id = dc.det_pro_id
        WHERE c.car_usu_id = :usuarioId
    ";

    $stmtCarrito = $conn->prepare($queryCarrito);
    $stmtCarrito->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmtCarrito->execute();
    $productosCarrito = $stmtCarrito->fetchAll(PDO::FETCH_ASSOC);

    if (empty($productosCarrito)) {
        echo json_encode(['success' => false, 'message' => 'El carrito está vacío.']);
        exit;
    }

    // 2. Para cada producto, eliminarlo del inventario
    foreach ($productosCarrito as $producto) {
        $pro_id = $producto['det_pro_id'];
        $cantidad = $producto['det_cantidad'];
        $precio = $producto['pro_precio'];

        // 2.1 Eliminar el inventario de los productos
        $queryEliminarInventario = "
            DELETE FROM inventario 
            WHERE inv_pro_id = :pro_id 
            AND inv_accion = 'Agregar'
            LIMIT :cantidad
        ";
        $stmtEliminarInventario = $conn->prepare($queryEliminarInventario);
        $stmtEliminarInventario->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmtEliminarInventario->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
        $stmtEliminarInventario->execute();
    }

    // 3. Vaciar el carrito
    $queryVaciarCarrito = "
        DELETE FROM detallecarrito 
        WHERE det_car_id IN 
            (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId)
    ";
    $stmtVaciarCarrito = $conn->prepare($queryVaciarCarrito);
    $stmtVaciarCarrito->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmtVaciarCarrito->execute();

    // 4. Confirmar la transacción
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito.']);
} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
