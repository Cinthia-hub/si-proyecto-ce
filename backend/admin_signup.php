<?php
    header('Content-Type: application/json');
    require_once 'database.php';

    // Mostrar errores en desarrollo
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $input = json_decode(file_get_contents("php://input"), true);

    $nombre = $input['nombre'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (!$nombre || !$email || !$password) {
        echo json_encode(["success" => false, "error" => "Faltan datos obligatorios"]);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Verifica si el correo ya está registrado como usuario
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE usu_correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "error" => "El correo ya está registrado como usuario"]);
        exit;
    }

    // Verifica si el correo ya está registrado como administrador
    $stmt = $conn->prepare("SELECT * FROM administrador WHERE adm_correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "error" => "El correo ya está registrado como administrador"]);
        exit;
    }

    // Inserta nuevo usuario
    $stmt = $conn->prepare("INSERT INTO administrador (adm_nombre, adm_correo, adm_contrasena) VALUES (:nombre, :email, :password)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se pudo registrar el administrador"]);
    }
?>
