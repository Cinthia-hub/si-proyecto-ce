// URL base para el backend (ajusta si es necesario)
const BASE_URL = 'http://localhost:8888/proyecto_a.de_internet/backend';


// Función para manejar el formulario de agregar productos
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addInventoryForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Obtener los datos del formulario
        const productoNombre = document.getElementById('producto_nombre').value;
        const productoDescripcion = document.getElementById('producto_descripcion').value;
        const productoPrecio = parseFloat(document.getElementById('producto_precio').value);
        const cantidad = parseInt(document.getElementById('cantidad').value);
        const adminId = document.getElementById('admin_id').value; // ID del administrador

        // Crear el objeto del producto
        const producto = {
            producto_nombre: productoNombre,
            producto_descripcion: productoDescripcion,
            producto_precio: productoPrecio,
            cantidad: cantidad,
            admin_id: adminId
        };

        // Enviar los datos al servidor
        console.log("Datos enviados:", producto);
        fetch(`${BASE_URL}/addproducto.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(producto)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                agregarProductoTabla(data.producto); // Actualiza la tabla
                alert('Producto agregado exitosamente al inventario');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Hubo un error al agregar el producto.');
        });
    });

    // Cargar los productos al iniciar la página
    cargarProductos();
});

// Función para agregar el producto a la tabla
function agregarProductoTabla(producto) {
    // Asegúrate de que el tbody con id 'products-table' exista
    const tableBody = document.getElementById('products-table');
    if (!tableBody) {
        console.error('El elemento tbody con id "products-table" no se encuentra.');
        return;
    }

    // Verificar si el precio es válido antes de usar toFixed()
    const precio = (producto.pro_precio && !isNaN(producto.pro_precio)) 
        ? Number(producto.pro_precio).toFixed(2) 
        : 'N/A';

    // Obtener la cantidad desde inv_cantidad (o el nombre correcto de la propiedad)
    const cantidad = producto.inv_cantidad || '0'; // Asegúrate de que esto tenga la propiedad correcta de la respuesta

    // Crear una nueva fila en la tabla
    const row = tableBody.insertRow();
    row.id = `producto-${producto.pro_id}`; // Establece el ID de la fila para identificarla

    // Insertar celdas con los datos del producto
    row.insertCell(0).textContent = producto.pro_nombre || 'Sin nombre';
    row.insertCell(1).textContent = producto.pro_descripcion || 'Sin descripción';
    row.insertCell(2).textContent = precio;
    row.insertCell(3).textContent = cantidad;
    row.insertCell(4).innerHTML = `<button class="btn btn-danger" onclick="eliminarProducto(${producto.pro_id})">Eliminar</button>`;
}

// Función para eliminar un producto de la base de datos y de la tabla (frontend)
function eliminarProducto(id) {
    // Realizamos la solicitud para eliminar el producto de la base de datos
    fetch(`${BASE_URL}/eliminarProductoInventario.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'id': id
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);

        if (data.status === "success") {
            // Eliminar producto de la tabla en el frontend
            const row = document.getElementById(`producto-${id}`);
            if (row) {
                row.remove(); // Elimina la fila de la tabla
            }
            alert(data.message); // Muestra un mensaje de éxito
        } else {
            alert(data.message); // Muestra un mensaje de error
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        alert('Hubo un error al eliminar el producto');
    });
}

// Función para cargar productos al iniciar la página (suponiendo que la lógica backend ya esté lista)
function cargarProductos() {
    fetch(`${BASE_URL}/getproducto.php`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.productos) {
            data.productos.forEach(producto => {
                agregarProductoTabla(producto);
            });
        } else {
            console.error("Error al cargar los productos:", data.message);
        }
    })
    .catch(error => {
        console.error("Error al cargar productos:", error);
    });
}
