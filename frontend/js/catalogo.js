const nombre = localStorage.getItem('nombre') || 'Usuario';
const correo = localStorage.getItem('correo') || 'correo@example.com';

document.getElementById("userName").textContent = nombre;
document.getElementById("userEmail").textContent = correo;

document.addEventListener("DOMContentLoaded", function () {
    const BASE_URL = 'http://localhost:80/si-proyecto-ce/backend';
    let productos = []; // Para almacenar los productos cargados desde el backend

    // Obtener productos desde el backend
    fetch(`${BASE_URL}/getproducto.php`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productos = data.productos; // Guardamos los productos en la variable
                renderProductos(productos);
            } else {
                alert('Error al cargar los productos: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));

    // Renderizar productos en la tabla
    const renderProductos = (productos) => {
        const tableBody = document.getElementById("products-table");
        tableBody.innerHTML = ""; // Limpia el contenido previo

        productos.forEach((producto) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${producto.pro_nombre}</td>
                <td>${producto.pro_descripcion}</td>
                <td>$${parseFloat(producto.pro_precio).toFixed(2)}</td>
                <td>${producto.inv_cantidad}</td>
                <td>
                    <button class="btn btn-success btn-sm me-2" onclick="agregarAlCarrito(${producto.pro_id}, ${producto.inv_cantidad})">
                        Agregar
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    // Agregar al carrito
    window.agregarAlCarrito = function (productoId, cantidadDisponible) {
        const cantidad = prompt("¿Cuántos productos deseas agregar?", 1);

        if (!cantidad || isNaN(cantidad) || cantidad < 1) {
            alert("Cantidad inválida.");
            return;
        }

        if (parseInt(cantidad) > cantidadDisponible) {
            alert("No hay suficiente stock.");
            return;
        }

        fetch(`${BASE_URL}/carrito.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'agregar',
                productoId: productoId,
                cantidad: parseInt(cantidad)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Producto agregado al carrito.");
                cargarCarrito();
            } else {
                alert("Error al agregar el producto: " + (data.message || ""));
            }
        })
        .catch(error => console.error("Error:", error));
    };

    // Actualizar el resumen del carrito
    const cargarCarrito = () => {
        fetch(`${BASE_URL}/carrito.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'listar' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.productos)) {
                const carrito = data.productos;
                if (carrito.length === 0) {
                    document.getElementById('cart-summary').innerHTML = "<p>Tu carrito está vacío.</p>";
                } else {
                    let total = 0;
                    const items = carrito.map(item => {
                        total += parseFloat(item.det_subtotal);
                        return `<li>${item.pro_nombre}: ${item.det_cantidad}</li>`;
                    }).join('');
                    document.getElementById('cart-summary').innerHTML = `
                        <h5>Resumen del Carrito</h5>
                        <ul>${items}</ul>
                        <p>Total: $${total.toFixed(2)}</p>
                    `;
                }
            } else {
                document.getElementById('cart-summary').innerHTML = "<p>Error al cargar el carrito.</p>";
            }
        })
        .catch(error => {
            console.error('Error al cargar el carrito:', error);
            document.getElementById('cart-summary').innerHTML = "<p>Error al cargar el carrito.</p>";
        });
    };

    // Cargar el carrito al inicio
    cargarCarrito();
});

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