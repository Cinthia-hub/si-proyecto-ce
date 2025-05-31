const nombre = localStorage.getItem('nombre') || 'Administrador';
const correo = localStorage.getItem('correo') || 'correo@example.com';

document.getElementById("adminName").textContent = nombre;
document.getElementById("adminEmail").textContent = correo;
document.getElementById("adminGreeting").textContent = nombre;

// Función para ir a registrar nuevo admin
function registrarAdmin() {
  window.location.href = "admin_signup.html"; // Debes crear esta página
}

// Función para ir al inventario
function irAlInventario() {
  window.location.href = "inventario.html"; // Redirige al inventario
}

// Función para ir al panel de control
function irAlControlPanel() {
  window.location.href = "control_panel.html"; // Redirige al inventario
}

// Cerrar sesión
function cerrarSesion() {
    Swal.fire({
    title: "¿Cerrar sesión?",
    showCancelButton: true,
    confirmButtonText: "Sí, salir",
    cancelButtonText: "Cancelar",
    icon: "warning"
    }).then((result) => {
    if (result.isConfirmed) {
        localStorage.clear();
        window.location.href = "index.html";
    }
    });
}