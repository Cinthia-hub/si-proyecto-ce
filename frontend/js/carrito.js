// Mostrar nombre y correo si existen en localStorage (opcional)
const nombre = localStorage.getItem('nombre') || 'Usuario';
const correo = localStorage.getItem('correo') || 'correo@example.com';

document.getElementById("userName").textContent = nombre;
document.getElementById("userEmail").textContent = correo;

// Cargar el carrito general
function cargarCarrito() {
    fetch('http://localhost:80/si-proyecto-ce/backend/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'listar' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const carrito = data.productos;
            const carritoBody = document.getElementById("carritoBody");
            const carritoTotal = document.getElementById("carritoTotal");

            let total = 0;
            carritoBody.innerHTML = "";

            carrito.forEach((item) => {
                total += parseFloat(item.det_subtotal);

                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${item.pro_nombre}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="modificarCantidad(${item.det_id}, -1)">-</button>
                        ${item.det_cantidad}
                        <button class="btn btn-success btn-sm" onclick="modificarCantidad(${item.det_id}, 1)">+</button>
                    </td>
                    <td>$${item.pro_precio}</td>
                    <td>$${parseFloat(item.det_subtotal).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${item.det_id})">Eliminar</button>
                    </td>
                `;
                carritoBody.appendChild(row);
            });

            carritoTotal.textContent = `$${total.toFixed(2)}`;
        } else {
            alert("No se pudo cargar el carrito.");
        }
    })
    .catch(error => {
        console.error('Error al cargar el carrito:', error);
        alert("Hubo un problema al cargar el carrito.");
    });
}

// Modificar cantidad de un producto
function modificarCantidad(detId, cambio) {
    // Obtener la cantidad actual del producto en la tabla
    const fila = Array.from(document.querySelectorAll("#carritoBody tr")).find(tr =>
        tr.querySelector("button.btn-warning").getAttribute("onclick").includes(detId)
    );
    if (!fila) return;
    let cantidadActual = parseInt(fila.children[1].textContent.trim());
    let nuevaCantidad = cantidadActual + cambio;
    if (nuevaCantidad < 1) nuevaCantidad = 1;

    fetch('http://localhost:80/si-proyecto-ce/backend/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'actualizar', productoId: detId, cantidad: nuevaCantidad })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            alert("Error al actualizar la cantidad.");
        }
    });
}

// Eliminar producto del carrito
function eliminarProducto(detId) {
    fetch('http://localhost:80/si-proyecto-ce/backend/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'eliminar', productoId: detId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            alert("Error al eliminar el producto.");
        }
    });
}

// Vaciar carrito
function vaciarCarrito() {
    fetch('http://localhost:80/si-proyecto-ce/backend/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'vaciar' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            alert("Error al vaciar el carrito.");
        }
    });
}

// Finalizar compra
function finalizarCompra() {
    fetch('http://localhost:80/si-proyecto-ce/backend/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'finalizar' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Compra realizada con éxito.");
            cargarCarrito();
        } else {
            alert("Error al procesar la compra.");
        }
    });
}

// Llama cargarCarrito al cargar la página
window.onload = cargarCarrito;