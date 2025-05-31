<?php
    header('Content-Type: application/json');
    require_once 'database.php';

    $db = new Database();
    $conn = $db->getConnection();

    // Obtener administradores
    $stmt = $conn->prepare("SELECT adm_id AS id, adm_nombre AS nombre, adm_correo AS correo FROM administrador");
    $stmt->execute();
    $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "administradores" => $administradores
    ]);
?>
