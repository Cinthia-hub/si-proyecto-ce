<?php
    header('Content-Type: application/json');
    require_once 'database.php';

    $db = new Database();
    $conn = $db->getConnection();

    // Obtener usuarios
    $stmt = $conn->prepare("SELECT usu_id AS id, usu_nombre AS nombre, usu_correo AS correo FROM usuario");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "usuarios" => $usuarios,
    ]);
?>
