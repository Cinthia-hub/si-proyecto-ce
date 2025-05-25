const usuarioId = 1; // Cambiar según el ID de usuario autenticado
// Función para cargar el carrito desde el servidor
function cargarCarrito() {
    fetch(`http://localhost:8888/si-proyecto-ce/backend/getcarrito.php?usuarioId=${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data); // Agrega este log para verificar la respuesta
            if (data.success) {
                const carrito = data.productos;
                const carritoBody = document.getElementById("carritoBody");
                const carritoTotal = document.getElementById("carritoTotal");

                let total = 0;
                carritoBody.innerHTML = "";

                carrito.forEach((item, index) => {
                    console.log('Producto en carrito:', item);  // Log para verificar cada item recibido

                    const totalProducto = item.det_cantidad * item.pro_precio;
                    total += totalProducto;

                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.pro_nombre}</td>
                        <td>
                            <input type="number" 
                                class="form-control form-control-sm" 
                                value="${item.det_cantidad}" 
                                min="1" 
                                id="cantidad_${item.det_id}" 
                                onchange="actualizarCantidad(${item.det_id})">
                        </td>
                        <td>$${item.pro_precio}</td>
                        <td>$${totalProducto.toFixed(2)}</td>
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
// Función para actualizar la cantidad de un producto en el carrito
function actualizarCantidad(det_id) {
    const nuevaCantidad = document.getElementById(`cantidad_${det_id}`).value;
    console.log('Nueva cantidad:', nuevaCantidad); // Verificar que el valor se obtiene correctamente
    
    if (nuevaCantidad < 1) {
        alert("La cantidad no puede ser menor a 1.");
        return;
    }

    console.log('usuarioId:', usuarioId); // Verificar que usuarioId esté correctamente definido
    console.log('det_id:', det_id); // Verificar que det_id se pase correctamente

    // Enviar la nueva cantidad al servidor
    fetch(`http://localhost:8888/si-proyecto-ce/backend/actualizarCantidad.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuarioId, det_id, nuevaCantidad })
    })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                cargarCarrito(); // Recargar carrito tras actualizar la cantidad
            } else {
                alert("Error al actualizar la cantidad.");
                cargarCarrito(); // Recargar carrito tras actualizar la cantidad
            }
        })
        .catch(error => {
            console.error('Error al actualizar la cantidad:', error);
            alert("Hubo un problema al actualizar la cantidad.");
            cargarCarrito(); // Recargar carrito tras actualizar la cantidad
        });
}


// Función para eliminar un producto del carrito
function eliminarProducto(productoId) {
    console.log('Eliminando producto con ID:', productoId); // Verificar el ID del producto
    console.log('Usuario ID:', usuarioId); // Verificar el ID del usuario

    if (!productoId || !usuarioId) {
        alert("Faltan parámetros: usuario o producto no especificado.");
        return;
    }

    fetch(`http://localhost:8888/si-proyecto-ce/backend/eliminarProducto.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuarioId, productoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Producto eliminado con éxito. Recargando el carrito...');
            cargarCarrito();  // Recargar carrito tras eliminar el producto
        } else {
            console.error('Error al eliminar el producto:', data.message); // Mostrar mensaje de error
            alert("Error al eliminar el producto: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error al realizar la solicitud:', error);
        alert("Hubo un problema al eliminar el producto.");
    });
}


        // Función para vaciar el carrito
        function vaciarCarrito() {
            fetch(`http://localhost:8888/si-proyecto-ce/backend/vaciarCarrito.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ usuarioId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarCarrito();  // Recargar carrito tras vaciar
                    } else {
                        alert("Error al vaciar el carrito.");
                    }
                });
        }

        // Función para finalizar la compra
        function finalizarCompra() {
    fetch('http://localhost:8888/si-proyecto-ce/backend/finalizarCompra.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuarioId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Compra realizada con éxito.");
                cargarCarrito();  // Recargar carrito vacío
            } else {
                alert("Error al procesar la compra: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error al procesar la compra:', error);
            alert("Hubo un problema al procesar la compra.");
        });
}

        // Cargar el carrito cuando la página se cargue
        cargarCarrito();