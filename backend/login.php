<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['usuario']) || !isset($_POST['contrasena'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos de usuario o contraseña']);
        exit();
    }

    $usuario = $_POST['usuario']; 
    $contrasena = $_POST['contrasena'];   

    // Configuración de la base de datos
    $servername = "localhost:8889"; 
    $username = "root"; 
    $password = "root"; 
    $dbname = "aplicacionesproyecto"; 

    // Conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consultar si el usuario es administrador
    $queryAdmin = "SELECT * FROM administrador WHERE adm_correo = ?";
    $stmtAdmin = $conn->prepare($queryAdmin);
    $stmtAdmin->bind_param("s", $usuario);  
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows > 0) {
        $admin = $resultAdmin->fetch_assoc();
        if ($admin['adm_contrasena'] === $contrasena) {
            echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso']);
            header("Location: ../frontend/inventario.html");  
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta para administrador']);
        }
    } else {
        // Consultar si el usuario es un cliente
        $queryUser = "SELECT * FROM usuario WHERE usu_correo = ?";
        $stmtUser = $conn->prepare($queryUser);
        $stmtUser->bind_param("s", $usuario);  
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $user = $resultUser->fetch_assoc();
            if ($user['usu_contrasena'] === $contrasena) {
                // Guardar el ID de usuario en la sesión
                session_start();
                $_SESSION['usuario_id'] = $user['usu_id'];  // Guarda el ID del usuario

                echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso']);
                header("Location: ../frontend/catalogo.html");  
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta para usuario']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    }

    $stmtAdmin->close();
    $stmtUser->close();
    $conn->close();
} else {
    http_response_code(405);  
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
