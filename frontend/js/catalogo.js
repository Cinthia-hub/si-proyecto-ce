const nombre = localStorage.getItem('nombre') || 'Usuario';
const correo = localStorage.getItem('correo') || 'correo@example.com';

document.getElementById("userName").textContent = nombre;
document.getElementById("userEmail").textContent = correo;

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
        localStorage.clear;
        window.location.href = "index.html";
    }
    });
}