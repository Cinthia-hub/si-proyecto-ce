<?php
header('Content-Type: application/json');
require_once 'database.php'; // Archivo de conexión a la base de datos

// Conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();

// Recibe los datos de la solicitud (en formato JSON)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_nombre = $data['producto_nombre'] ?? '';
    $producto_descripcion = $data['producto_descripcion'] ?? '';
    $producto_precio = $data['producto_precio'] ?? '';
    $cantidad_inicial = $data['cantidad'] ?? 0; // Cantidad inicial para el inventario
    $admin_id = $data['admin_id'] ?? '';

    // Validar campos obligatorios
    if (empty($producto_nombre) || empty($producto_descripcion) || empty($producto_precio) || empty($cantidad_inicial) || empty($admin_id)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
        exit;
    }

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Verificar si el producto ya existe
        $sql_check_producto = "SELECT pro_id FROM producto WHERE pro_nombre = :producto_nombre";
        $stmt_check_producto = $conn->prepare($sql_check_producto);
        $stmt_check_producto->bindParam(':producto_nombre', $producto_nombre);
        $stmt_check_producto->execute();

        if ($stmt_check_producto->rowCount() == 0) {
            // Insertar el producto en la tabla producto
            $sql_insert_producto = "INSERT INTO producto (pro_nombre, pro_descripcion, pro_precio) 
                                    VALUES (:producto_nombre, :producto_descripcion, :producto_precio)";
            $stmt_insert_producto = $conn->prepare($sql_insert_producto);
            $stmt_insert_producto->bindParam(':producto_nombre', $producto_nombre);
            $stmt_insert_producto->bindParam(':producto_descripcion', $producto_descripcion);
            $stmt_insert_producto->bindParam(':producto_precio', $producto_precio);
            $stmt_insert_producto->execute();
            $producto_id = $conn->lastInsertId(); // Obtener el ID del producto recién creado
        } else {
            echo json_encode(['success' => false, 'message' => 'El producto ya existe']);
            exit;
        }

        // Insertar la cantidad inicial en la tabla inventario
        $sql_insert_inventario = "INSERT INTO inventario (inv_fecha, inv_accion, inv_cantidad, inv_pro_id, inv_adm_id) 
                                  VALUES (NOW(), 'Agregar', :cantidad_inicial, :producto_id, :admin_id)";
        $stmt_insert_inventario = $conn->prepare($sql_insert_inventario);
        $stmt_insert_inventario->bindParam(':cantidad_inicial', $cantidad_inicial, PDO::PARAM_INT);
        $stmt_insert_inventario->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $stmt_insert_inventario->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt_insert_inventario->execute();

        // Confirmar transacción
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Producto creado y agregado al inventario',
            'producto' => [
                'id' => $producto_id,
                'nombre' => $producto_nombre,
                'descripcion' => $producto_descripcion,
                'precio' => $producto_precio,
                'cantidad' => $cantidad_inicial
            ]
        ]);
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
    }

    // Liberar recursos
    $stmt_check_producto = null;
    $stmt_insert_producto = null;
    $stmt_insert_inventario = null;
    $conn = null;
}
?>
