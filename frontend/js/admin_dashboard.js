const nombre = localStorage.getItem('nombre') || 'Administrador';
const correo = localStorage.getItem('correo') || 'correo@example.com';

document.getElementById("adminName").textContent = nombre;
document.getElementById("adminEmail").textContent = correo;
document.getElementById("adminGreeting").textContent = nombre;

// Función para ir a registrar nuevo admin
function registrarAdmin() {
  window.location.href = "registrar_admin.html"; // Debes crear esta página
}

// Función para ir al inventario
function irAlInventario() {
  window.location.href = "inventario.html"; // Redirige al inventario
}

// Función para cerrar sesión
function cerrarSesion() {
  Swal.fire({
      icon: 'success',
      title: 'Cerrando sesión ',
      text: 'Cerró sesión exitosamente',
  });
  localStorage.clear(); 
  window.location.href = "index.html"; // Redirige al login
}