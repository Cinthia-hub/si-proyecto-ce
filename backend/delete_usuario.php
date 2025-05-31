<?php
    header('Content-Type: application/json');
    require_once 'database.php';

    $input = json_decode(file_get_contents("php://input"), true);
    $id = $input['id'] ?? '';
    $tipo = $input['tipo'] ?? '';

    if (!$id || !$tipo) {
        echo json_encode(["success" => false, "message" => "Faltan datos"]);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    if ($tipo === 'usuario') {
        $sql = "DELETE FROM usuario WHERE usu_id = :id";
    } else {
        echo json_encode(["success" => false, "message" => "Tipo inválido"]);
        exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Eliminado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar"]);
    }
?>
