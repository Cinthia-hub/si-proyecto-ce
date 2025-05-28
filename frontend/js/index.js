document.getElementById("login-form").addEventListener("submit", async function (event) {
    event.preventDefault(); // PREVIENE RECARGA

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const erroremail = document.getElementById("error-email");
    const errorpassword = document.getElementById("error-password");

    erroremail.textContent = "";
    errorpassword.textContent = "";
    try {
    const response = await fetch("../backend/login.php", {
        method: "POST",
        headers: {
        "Content-Type": "application/json"
        },
        body: JSON.stringify({ email, password })
    });

    const text = await response.text();
    let data;
    try {
        data = JSON.parse(text);
    } catch (e) {
        console.error("No se pudo parsear JSON:", text);
        throw new Error("Respuesta del servidor inválida.");
    }

    if (data.error === "correo") {
        erroremail.textContent = "El correo no está registrado.";
    } else if (data.error === "contrasena") {
        errorpassword.textContent = "La contraseña es incorrecta.";
    } else if (data.success === "usuario") {
        localStorage.setItem("nombre", data.nombre);
        localStorage.setItem("correo", data.correo)
        window.location.href = "../frontend/catalogo.html";
    } else if (data.success === "administrador") {
        localStorage.setItem("nombre", data.nombre);
        localStorage.setItem("correo", data.correo)
        window.location.href = "../frontend/admin_dashboard.html";
    }
    } catch (err) {
        console.error("Error al iniciar sesión:", err);
    }
});
