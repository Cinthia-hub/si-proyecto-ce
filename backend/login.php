<?php
    header('Content-Type: application/json');
    require_once '../backend/database.php';

    $input = json_decode(file_get_contents("php://input"), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    $db = new Database();
    $conn = $db->getConnection();

    // Buscar en la tabla administrador
    $stmt = $conn->prepare("SELECT * FROM administrador WHERE adm_correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        if ($admin['adm_contrasena'] === $password) {
            echo json_encode([
                "success" => "administrador",
                "nombre" => $admin['adm_nombre'],
                "correo" => $admin['adm_correo']
            ]);
        } else {
            echo json_encode(["error" => "contrasena"]);
        }
        exit;
    }

    // Buscar en la tabla usuario
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE usu_correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if ($usuario['usu_contrasena'] === $password) {
            echo json_encode([
                "success" => "usuario",
                "nombre" => $usuario['usu_nombre'],
                "correo" => $usuario['usu_correo']
            ]);
        } else {
            echo json_encode(["error" => "contrasena"]);
        }
        exit;
    }

    echo json_encode(["error" => "correo"]);
?>
