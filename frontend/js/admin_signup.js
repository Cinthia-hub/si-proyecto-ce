document.querySelector("form").addEventListener("submit", async function (event) {
  event.preventDefault();

  const nombre = document.getElementById("nombre").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  if (password !== confirmPassword) {
    Swal.fire("Error", "Las contraseñas no coinciden", "error");
    return;
  }

  try {
    const response = await fetch("../backend/admin_signup.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ nombre, email, password })
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire("Registro exitoso", "La cuenta ha sido creada", "success");
      document.querySelector("form").reset();
    } else {
      Swal.fire("Error", data.error || "No se pudo registrar", "error");
    }
  } catch (err) {
    console.error("Error al registrar:", err);
    Swal.fire("Error", "Ocurrió un error al registrar", "error");
  }
});