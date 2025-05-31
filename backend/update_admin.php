<?php
    header('Content-Type: application/json');
    require_once 'database.php';

    $input = json_decode(file_get_contents("php://input"), true);
    $id = $input['id'] ?? '';
    $tipo = $input['tipo'] ?? '';
    $nombre = $input['nombre'] ?? '';
    $correo = $input['correo'] ?? '';

    if (!$id || !$tipo || !$nombre || !$correo) {
        echo json_encode(["success" => false, "message" => "Faltan datos"]);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    if ($tipo === 'administrador') {
        $sql = "UPDATE administrador SET adm_nombre = :nombre, adm_correo = :correo WHERE adm_id = :id";
    } else {
        echo json_encode(["success" => false, "message" => "Tipo inválido"]);
        exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Actualizado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar"]);
    }
?>
