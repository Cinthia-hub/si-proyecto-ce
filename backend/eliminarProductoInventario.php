<?php
header('Content-Type: application/json');
require_once 'database.php'; // Conexión a la base de datos

// Verifica si la conexión es exitosa
$conn = (new Database())->getConnection();
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos."]);
    exit;
}

// Verifica si el ID del producto ha sido recibido
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Asegúrate de que el ID es un número entero

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "ID inválido"]);
        exit;
    }

    // Eliminar las entradas en la tabla inventario asociadas al producto
    $sqlInventario = "DELETE FROM inventario WHERE inv_pro_id = :id";
    $stmtInventario = $conn->prepare($sqlInventario);
    $stmtInventario->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtInventario->execute();

    // Consulta para eliminar el producto
    $sql = "DELETE FROM producto WHERE pro_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Ejecuta la consulta y devuelve el resultado
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Producto eliminado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar el producto"]);
    }

    // Cierra la declaración
    $stmt = null;
} else {
    echo json_encode(["status" => "error", "message" => "ID del producto no proporcionado"]);
}

$conn = null; // Cierra la conexión a la base de datos
?>
