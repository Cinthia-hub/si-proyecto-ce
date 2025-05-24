<?php
include_once 'database.php'; 

$database = new Database();
$conn = $database->getConnection(); 

if ($conn === null) {
    echo json_encode(["error" => "Conexión a la base de datos fallida"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['email'];
    $contrasena = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    
    if ($contrasena != $confirmPassword) {
        echo json_encode(["error" => "Las contraseñas no coinciden"]);
        exit();
    }

    
    $sql = "INSERT INTO usuario (usu_nombre, usu_correo, usu_contrasena) VALUES (:nombre, :correo, :contrasena)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':contrasena', password_hash($contrasena, PASSWORD_DEFAULT));

    if ($stmt->execute()) {
        echo json_encode(["success" => "Usuario registrado exitosamente"]);
        header("Location: ../frontend/catalogo.html");  
        exit();
    } else {
        echo json_encode(["error" => "Error al registrar usuario"]);
    }    
}
?>
