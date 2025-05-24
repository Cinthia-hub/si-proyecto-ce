<?php
// carrito.php

header('Content-Type: application/json');
// Obtener los datos enviados como JSON
$data = json_decode(file_get_contents("php://input"), true);

// Depuración: mostrar toda la data recibida
error_log("Datos recibidos: " . print_r($data, true)); // Registra toda la data para ver qué contiene

// Verificar si se ha enviado el usuarioId
if (!isset($data['usuarioId'])) {
    echo json_encode(["success" => false, "message" => "Usuario no especificado."]);
    exit;
}

// Depuración: mostrar usuarioId recibido
error_log("Usuario ID recibido: " . $data['usuarioId']);

// Incluir archivo de conexión a la base de datos
include 'database.php'; // Asegúrate de que este archivo contiene la conexión PDO en $conn

// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Obtener datos de la solicitud
$action = $data['action'] ?? null; // Acción (agregar, eliminar, etc.)
$usuarioId = $data['usuarioId'] ?? null; // ID del usuario
$productoId = $data['productoId'] ?? null; // ID del producto
$cantidad = $data['cantidad'] ?? null; // Cantidad del producto

// Depuración: mostrar la acción y los parámetros recibidos
error_log("Acción: " . $action);
error_log("Producto ID: " . $productoId);
error_log("Cantidad: " . $cantidad);

// Validar si el usuario está autenticado y si los parámetros son correctos
if (!$usuarioId || !$productoId || !$cantidad) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
    exit;
}

// Función para agregar producto al carrito
function agregarProducto($usuarioId, $productoId, $cantidad) {
    global $conn;

    // Verificar si el producto ya está en el carrito
    $query = "SELECT * FROM detallecarrito WHERE det_car_id = (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto') AND det_pro_id = :productoId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Si ya existe, actualizar la cantidad
        $query = "UPDATE detallecarrito SET det_cantidad = det_cantidad + :cantidad WHERE det_car_id = (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto') AND det_pro_id = :productoId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Si no existe, agregar nuevo producto
        $query = "INSERT INTO detallecarrito (det_car_id, det_pro_id, det_cantidad) VALUES ((SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto'), :productoId, :cantidad)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();
    }
    return true;
}

// Función para eliminar producto del carrito
function eliminarProducto($usuarioId, $productoId) {
    global $conn;

    $query = "DELETE FROM detallecarrito WHERE det_car_id = (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto') AND det_pro_id = :productoId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
    $stmt->execute();
    return true;
}

// Función para vaciar el carrito
function vaciarCarrito($usuarioId) {
    global $conn;

    $query = "DELETE FROM detallecarrito WHERE det_car_id = (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto')";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();
    return true;
}

// Función para actualizar cantidad de un producto
function actualizarCantidad($usuarioId, $productoId, $cantidad) {
    global $conn;

    $query = "UPDATE detallecarrito SET det_cantidad = :cantidad WHERE det_car_id = (SELECT car_id FROM carrito WHERE car_usu_id = :usuarioId AND car_estado = 'abierto') AND det_pro_id = :productoId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
    $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->execute();
    return true;
}

// Ejecutar la acción según la solicitud
if ($action == 'agregar') {
    if ($productoId && $cantidad) {
        $success = agregarProducto($usuarioId, $productoId, $cantidad);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
    }
} elseif ($action == 'eliminar') {
    if ($productoId) {
        $success = eliminarProducto($usuarioId, $productoId);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
    }
} elseif ($action == 'vaciar') {
    $success = vaciarCarrito($usuarioId);
    echo json_encode(['success' => $success]);
} elseif ($action == 'actualizar') {
    if ($productoId && $cantidad) {
        $success = actualizarCantidad($usuarioId, $productoId, $cantidad);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
}

?>
