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

    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmPassword)) {
        echo json_encode(["error" => "Todos los campos son obligatorios"]);
        exit();
    }

    if ($contrasena != $confirmPassword) {
        echo json_encode(["error" => "Las contraseñas no coinciden"]);
        exit();
    }

    $sql = "INSERT INTO usuario (usu_nombre, usu_correo, usu_contrasena) 
            VALUES (:nombre, :correo, :contrasena)";
    $stmt = $conn->prepare($sql);

    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':correo', $correo);
    $stmt->bindValue(':contrasena', $contrasena);

    if ($stmt->execute()) {
        header("Location: ../frontend/catalogo.html");
        exit(); // asegúrate de salir después de redirigir
    } else {
        echo json_encode(["error" => "Error al registrar usuario"]);
    }    
}
?>
