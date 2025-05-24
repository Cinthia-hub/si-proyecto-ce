document.addEventListener("DOMContentLoaded", function () {
    const BASE_URL = 'http://localhost:8888/proyecto_a.de_internet/backend';
    let productos = []; // Para almacenar los productos cargados desde el backend
    const usuarioId = 1; // Suponiendo que el usuario está autenticado y su ID es 1 (esto debería cambiar dependiendo de tu sistema de autenticación)

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

        if (cantidad > cantidadDisponible) {
            alert("No hay suficiente stock.");
            return;
        }

        // Enviar los datos al backend para agregar el producto al carrito
        fetch(`${BASE_URL}/addcarrito.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuarioId: usuarioId,
                productoId: productoId,
                cantidad: parseInt(cantidad)
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Una vez que el producto se ha agregado correctamente, actualizamos el resumen del carrito
                    cargarCarrito();
                } else {
                    alert("Error al agregar el producto: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
    };

    // Actualizar el resumen del carrito
    const cargarCarrito = () => {
        console.log('Cargando carrito...');
        // Realizar la solicitud para obtener el carrito
        const usuarioId = 1; // Asegúrate de que el usuarioId es el correcto
        console.log(`Solicitando carrito para usuarioId: ${usuarioId}`);
    
        fetch(`${BASE_URL}/getcarrito.php?usuarioId=${usuarioId}`)
            .then(response => {
                console.log('Respuesta recibida del servidor:', response);
                return response.json();
            })
            .then(data => {
                console.log('Datos obtenidos de la API:', data);
    
                if (data.success) {
                    // Aquí cambiamos 'carrito' por 'productos' porque el backend devuelve 'productos'
                    if (Array.isArray(data.productos)) {
                        const carrito = data.productos;  // Cambiar 'carrito' por 'productos'
                        console.log('Carrito:', carrito);
                        
                        if (carrito.length === 0) {
                            console.log('Carrito vacío');
                            document.getElementById('cart-summary').innerHTML = "<p>Tu carrito está vacío.</p>";
                        } else {
                            console.log('Mostrando productos del carrito');
                            document.getElementById('cart-summary').innerHTML = `
                                <h5>Resumen del Carrito</h5>
                                <ul>
                                    ${carrito.map(item => {
                                        // Asegúrate de que 'item.det_cantidad' esté presente y es válido
                                        return `<li>${item.pro_nombre}: ${item.det_cantidad}</li>`;
                                    }).join('')}
                                </ul>
                                <p>Total: $${data.carritoTotal}</p>  <!-- Mostrar el total -->
                            `;
                        }
                    } else {
                        console.error('Respuesta inesperada: El carrito no es un arreglo', data);
                        document.getElementById('cart-summary').innerHTML = "<p>Error al cargar los productos del carrito.</p>";
                    }
                } else {
                    console.error('Error al cargar el carrito:', data.message);
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
