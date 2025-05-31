const nombre = localStorage.getItem('nombre') || 'Administrador';
const correo = localStorage.getItem('correo') || 'correo@example.com';

document.getElementById("adminName").textContent = nombre;
document.getElementById("adminEmail").textContent = correo;

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