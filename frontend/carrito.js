// Función para cargar el carrito desde el servidor
usuarioId = 1; // ID del usuario actual
function cargarCarrito() {
    fetch(`http://localhost:8888/proyecto_a.de_internet/backend/getcarrito.php?usuarioId=${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const carrito = data.productos;
                const carritoBody = document.getElementById("carritoBody");
                const carritoTotal = document.getElementById("carritoTotal");

                let total = 0;
                carritoBody.innerHTML = "";

                carrito.forEach((item) => {
                    const totalProducto = item.det_cantidad * item.pro_precio;
                    total += totalProducto;

                    const row = document.createElement("tr");
                    row.innerHTML = `
                                <td>${item.pro_nombre}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="modificarCantidad(${item.det_id}, -1)">-</button>
                                    ${item.det_cantidad}
                                    <button class="btn btn-success btn-sm" onclick="modificarCantidad(${item.det_id}, 1)">+</button>
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

// Función para cargar el carrito desde el servidor
function cargarCarrito() {
    console.log("Cargando carrito para usuario con ID:", usuarioId); // Verificar el usuarioId
    fetch(`http://localhost:8888/proyecto_a.de_internet/backend/getcarrito.php?usuarioId=${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta al cargar carrito:", data); // Verificar la respuesta del servidor
            if (data.success) {
                const carrito = data.productos;
                const carritoBody = document.getElementById("carritoBody");
                const carritoTotal = document.getElementById("carritoTotal");

                let total = 0;
                carritoBody.innerHTML = "";

                carrito.forEach((item, index) => {
                    const totalProducto = item.det_cantidad * item.pro_precio;
                    total += totalProducto;

                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.pro_nombre}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="modificarCantidad(${item.det_id}, -1)">-</button>
                            ${item.det_cantidad}
                            <button class="btn btn-success btn-sm" onclick="modificarCantidad(${item.det_id}, 1)">+</button>
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

// Función para modificar la cantidad de un producto en el carrito (aumentar o disminuir)
function modificarCantidad(productoId, cantidad) {
    console.log("Modificando cantidad del producto ID:", productoId, "con cantidad:", cantidad); // Depuración de los parámetros
    fetch(`http://localhost:8888/proyecto_a.de_internet/backend/carrito.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'actualizar', usuarioId, productoId, cantidad })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta al modificar cantidad:", data); // Verificar la respuesta del servidor
            if (data.success) {
                cargarCarrito();  // Recargar carrito con la cantidad actualizada
            } else {
                alert("Error al actualizar la cantidad.");
            }
        });
}

// Función para eliminar un producto del carrito
function eliminarProducto(productoId) {
    console.log("Eliminando producto con ID:", productoId); // Depuración del producto a eliminar
    fetch(`http://localhost:8888/proyecto_a.de_internet/backend/carrito.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'eliminar', usuarioId, productoId })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta al eliminar producto:", data); // Verificar la respuesta del servidor
            if (data.success) {
                cargarCarrito();  // Recargar carrito tras eliminar el producto
            } else {
                alert("Error al eliminar el producto.");
            }
        });
}

// Función para vaciar el carrito
function vaciarCarrito() {
    console.log("Vaciando el carrito para el usuario con ID:", usuarioId); // Depuración del usuarioId
    fetch(`http://localhost:8888/proyecto_a.de_internet/backend/carrito.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'vaciar', usuarioId })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta al vaciar carrito:", data); // Verificar la respuesta del servidor
            if (data.success) {
                cargarCarrito();  // Recargar carrito tras vaciar
            } else {
                alert("Error al vaciar el carrito.");
            }
        });
}

// Función para finalizar la compra
function finalizarCompra() {
    console.log("Finalizando compra para el usuario con ID:", usuarioId); // Depuración del usuarioId
    fetch('http://localhost:8888/proyecto_a.de_internet/backend/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'finalizar', usuarioId })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta al finalizar compra:", data); // Verificar la respuesta del servidor
            if (data.success) {
                alert("Compra realizada con éxito.");
                cargarCarrito();  // Recargar carrito vacío
            } else {
                alert("Error al procesar la compra.");
            }
        });
}
