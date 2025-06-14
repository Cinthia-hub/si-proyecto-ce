CREATE DATABASE IF NOT EXISTS aplicacionesproyecto;
USE aplicacionesproyecto;

CREATE TABLE IF NOT EXISTS administrador(
    adm_id INT NOT NULL AUTO_INCREMENT,
    adm_nombre VARCHAR(50) NOT NULL,
    adm_correo VARCHAR(50) NOT NULL UNIQUE,
    adm_contrasena VARCHAR(50) NOT NULL,
    PRIMARY KEY (adm_id),
    INDEX idx_admin_nombre(adm_nombre)
);

CREATE TABLE IF NOT EXISTS usuario(
    usu_id INT NOT NULL AUTO_INCREMENT,
    usu_nombre VARCHAR(50) NOT NULL,
    usu_correo VARCHAR(50) NOT NULL UNIQUE,
    usu_contrasena VARCHAR(250) NOT NULL,
    PRIMARY KEY(usu_id),
    INDEX idx_usuario_nombre(usu_nombre)
);

CREATE TABLE IF NOT EXISTS producto(
    pro_id INT NOT NULL AUTO_INCREMENT,
    pro_nombre VARCHAR(50) NOT NULL,
    pro_descripcion VARCHAR(100) NOT NULL,
    pro_precio DECIMAL(10,2),
    PRIMARY KEY(pro_id),
    INDEX idx_producto_nombre(pro_nombre)
);

-- Cambios aquí: carrito general, sin usuario
CREATE TABLE IF NOT EXISTS carrito(
    car_id INT NOT NULL AUTO_INCREMENT,
    car_total DECIMAL(10,2),
    PRIMARY KEY(car_id)
);

CREATE TABLE IF NOT EXISTS detallecarrito(
    det_id INT NOT NULL AUTO_INCREMENT,
    det_pro_id INT NOT NULL,
    det_car_id INT NOT NULL,
    det_cantidad INT NOT NULL,
    det_subtotal DECIMAL(10,2),
    PRIMARY KEY(det_id),
    CONSTRAINT fk_car_det
        FOREIGN KEY (det_car_id) 
        REFERENCES carrito(car_id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT,
    CONSTRAINT fk_pro_det
        FOREIGN KEY (det_pro_id) 
        REFERENCES producto(pro_id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);

CREATE TABLE IF NOT EXISTS inventario(
    inv_id INT NOT NULL AUTO_INCREMENT,
    inv_fecha DATE NOT NULL,
    inv_accion ENUM('Agregar','Quitar'),
    inv_cantidad INT NOT NULL,
    inv_pro_id INT NOT NULL,
    inv_adm_id INT NOT NULL,
    PRIMARY KEY (inv_id),
    INDEX idx_inventario_producto(inv_pro_id),
    CONSTRAINT fk_pro_inv
        FOREIGN KEY (inv_pro_id) 
        REFERENCES producto(pro_id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT fk_adm_inv
        FOREIGN KEY (inv_adm_id) 
        REFERENCES administrador(adm_id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);

-- Insertar administradores con contraseñas
INSERT IGNORE INTO administrador (adm_nombre, adm_correo, adm_contrasena) VALUES
('Cinthia Camila', 'cc.bravomarmolejo@ugto.mx', 'camila'),
('Andres Torres', 'a.torresceja@ugto.mx', 'andres'),
('Carmen Mar', 'cm.marmolejoflores@ugto.mx', 'carmen');

-- Insertar el carrito general si no existe
INSERT IGNORE INTO carrito (car_id, car_total) VALUES (1, 0.00);