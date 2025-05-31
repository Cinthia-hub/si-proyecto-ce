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

function registrarAdmin() {
  window.location.href = "admin_signup.html"; // Debes crear esta página
}

document.addEventListener("DOMContentLoaded", () => {
  cargarUsuarios();
  cargarAdministradores();
});

// --- Cargar usuarios ---
async function cargarUsuarios() {
  const res = await fetch("../backend/get_usuarios.php");
  const data = await res.json();
  const tbody = document.querySelector("#tabla-usuarios tbody");
  tbody.innerHTML = "";

  data.usuarios.forEach(user => {
    tbody.innerHTML += `
      <tr>
        <td>${user.nombre}</td>
        <td>${user.correo}</td>
        <td>
          <button class="btn btn-sm btn-warning me-2" onclick="editarUsuario(${user.id}, '${user.nombre}', '${user.correo}')">Editar</button>
          <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${user.id})">Eliminar</button>
        </td>
      </tr>`;
  });
}

// --- Cargar administradores ---
async function cargarAdministradores() {
  const res = await fetch("../backend/get_admins.php");
  const data = await res.json();
  const tbody = document.querySelector("#tabla-admins tbody");
  tbody.innerHTML = "";

  data.administradores.forEach(admin => {
    tbody.innerHTML += `
      <tr>
        <td>${admin.nombre}</td>
        <td>${admin.correo}</td>
        <td>
          <button class="btn btn-sm btn-warning me-2" onclick="editarAdmin(${admin.id}, '${admin.nombre}', '${admin.correo}')">Editar</button>
          <button class="btn btn-sm btn-danger" onclick="eliminarAdmin(${admin.id})">Eliminar</button>
        </td>
      </tr>`;
  });
}

// --- Editar usuario ---
function editarUsuario(id, nombreActual, correoActual) {
  Swal.fire({
    title: 'Editar Usuario',
    html: `
      <input type="text" id="nombre" class="swal2-input" value="${nombreActual}">
      <input type="email" id="correo" class="swal2-input" value="${correoActual}" disabled>
    `,
    confirmButtonText: 'Guardar',
    focusConfirm: false,
    preConfirm: async () => {
      const nombre = document.getElementById('nombre').value;
      const correo = document.getElementById('correo').value;
      const res = await fetch('../backend/update_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, nombre, correo, tipo: 'usuario' }) // <- aquí estaba el problema
      });
      const result = await res.json();
      if (result.success) {
        cargarUsuarios();
        Swal.fire('Actualizado', '', 'success');
      } else {
        Swal.fire('Error', result.error || 'No se pudo actualizar', 'error');
      }
    }
  });
}

// --- Eliminar usuario ---
async function eliminarUsuario(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "No podrás revertir esto",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      const res = await fetch(`../backend/delete_usuario.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, tipo: 'usuario' }) // <- ¡IMPORTANTE!
      });
      const result = await res.json();
      if (result.success) {
        cargarUsuarios();
        Swal.fire('Eliminado', '', 'success');
      } else {
        Swal.fire('Error', result.message || 'No se pudo eliminar', 'error');
      }
    }
  });
}

// --- Editar y eliminar administrador: funciones similares ---
function editarAdmin(id, nombreActual, correoActual) {
  Swal.fire({
    title: 'Editar Administrador',
    html: `
      <input type="text" id="nombre" class="swal2-input" value="${nombreActual}">
      <input type="email" id="correo" class="swal2-input" value="${correoActual}" disabled>
    `,
    confirmButtonText: 'Guardar',
    focusConfirm: false,
    preConfirm: async () => {
      const nombre = document.getElementById('nombre').value;
      const correo = document.getElementById('correo').value;
      const res = await fetch('../backend/update_admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, nombre, correo, tipo: 'administrador' }) // <- aquí estaba el problema
      });
      const result = await res.json();
      if (result.success) {
        cargarAdministradores();
        Swal.fire('Actualizado', '', 'success');
      } else {
        Swal.fire('Error', result.error || 'No se pudo actualizar', 'error');
      }
    }
  });
}

async function eliminarAdmin(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "No podrás revertir esto",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      const res = await fetch(`../backend/delete_admin.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, tipo: 'administrador' }) // <- ¡IMPORTANTE!
      });
      const result = await res.json();
      if (result.success) {
        cargarAdministradores();
        Swal.fire('Eliminado', '', 'success');
      } else {
        Swal.fire('Error', result.message || 'No se pudo eliminar', 'error');
      }
    }
  });
}