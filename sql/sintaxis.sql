CREATE DATABASE IF NOT EXISTS APLICACIONESPROYECTO;
USE APLICACIONESPROYECTO;
 
CREATE TABLE IF NOT EXISTS administrador(
	adm_id INT NOT NULL AUTO_INCREMENT,
    adm_nombre VARCHAR(50) NOT NULL,
    adm_correo VARCHAR(50) NOT NULL,
    adm_contrasena VARCHAR(50) NOT NULL,
    PRIMARY KEY (adm_id),
    INDEX idx_admin_nombre(adm_nombre)
);
 
CREATE TABLE IF NOT EXISTS usuario(
	usu_id INT NOT NULL AUTO_INCREMENT,
    usu_nombre VARCHAR(50) NOT NULL,
    usu_correo VARCHAR(50) NOT NULL,
    usu_contrasena VARCHAR(50) NOT NULL,
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
 
CREATE TABLE IF NOT EXISTS carrito(
	car_id INT NOT NULL AUTO_INCREMENT,
    car_total DECIMAL(10,2),
    car_usu_id INT NOT NULL,
    PRIMARY KEY(car_id),
    CONSTRAINT fk_usu_car
		FOREIGN KEY (car_usu_id) 
		REFERENCES usuario(usu_id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT
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