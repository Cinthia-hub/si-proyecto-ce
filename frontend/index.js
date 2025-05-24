// URL base para el backend (ajusta si es necesario)
const BASE_URL = 'http://localhost:8888/proyecto_a.de_internet/backend';

// Función para iniciar sesión
function loginUsuario() {
    const usuario = document.getElementById("usuario").value;
    const contrasena = document.getElementById("contrasena").value;

    // Verificar si los campos están vacíos
    if (!usuario || !contrasena) {
        alert("Por favor, ingresa tu usuario y contraseña.");
        return;
    }

    fetch('http://localhost:8888/proyecto_a.de_internet/backend/login.php', {
        method: "POST",
        body: new URLSearchParams({
            'usuario': usuario,
            'contrasena': contrasena
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json()) // Asegurarse de que la respuesta sea JSON
    .then(data => {
        if (data.success) {
            alert(data.message); // Muestra el mensaje de éxito
            window.location.href = "dashboard.html"; // Redirige a la página de dashboard
        } else {
            alert(data.message); // Muestra el mensaje de error
        }
    })
    .catch(error => {
        console.error("Error en el inicio de sesión:", error);
        alert("Error en el inicio de sesión. Intenta nuevamente.");
    });
}
