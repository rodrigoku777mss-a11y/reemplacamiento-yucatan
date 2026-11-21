

CREATE DATABASE IF NOT EXISTS reemplacamiento
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_spanish_ci;

USE reemplacamiento;


CREATE TABLE IF NOT EXISTS USUARIOS (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('ciudadano', 'admin') NOT NULL DEFAULT 'ciudadano',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS PROPIETARIO (
    id_propietario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    curp CHAR(18) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL,
    primer_apellido VARCHAR(60) NOT NULL,
    segundo_apellido VARCHAR(60) NULL,
    telefono VARCHAR(15) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS MODELO (
    id_modelo INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(60) NOT NULL,
    nombre_modelo VARCHAR(80) NOT NULL,
    UNIQUE KEY uk_marca_modelo (marca, nombre_modelo)
);

CREATE TABLE IF NOT EXISTS VEHICULO (
    num_serie VARCHAR(17) PRIMARY KEY,
    placa_actual VARCHAR(15) NOT NULL UNIQUE,
    num_motor VARCHAR(30) NOT NULL UNIQUE,
    anio SMALLINT NOT NULL,
    id_propietario INT NOT NULL,
    id_modelo INT NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_propietario) REFERENCES PROPIETARIO(id_propietario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (id_modelo) REFERENCES MODELO(id_modelo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


CREATE TABLE IF NOT EXISTS TRAMITE (
    folio_tramite VARCHAR(20) PRIMARY KEY,
    tipo_tramite ENUM('Canje de Placas', 'Alta Vehicular', 'Reposición de Placas') NOT NULL DEFAULT 'Canje de Placas',
    estado_tramite ENUM('Pendiente', 'En Revisión', 'Aprobado', 'Rechazado', 'Finalizado') NOT NULL DEFAULT 'Pendiente',
    observaciones TEXT NULL,
    fecha_inicio DATE NOT NULL DEFAULT (CURRENT_DATE),
    num_serie VARCHAR(17) NOT NULL,
    FOREIGN KEY (num_serie) REFERENCES VEHICULO(num_serie)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS MODULO (
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_modulo VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NOT NULL,
    municipio VARCHAR(80) NOT NULL,
    tel_modulo VARCHAR(15) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS CITA (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    fecha_programada DATE NOT NULL,
    hora TIME NOT NULL,
    estado_cita ENUM('Programada', 'Atendida', 'Cancelada', 'Reprogramada') NOT NULL DEFAULT 'Programada',
    folio_tramite VARCHAR(20) NOT NULL,
    id_modulo INT NOT NULL,
    FOREIGN KEY (folio_tramite) REFERENCES TRAMITE(folio_tramite)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (id_modulo) REFERENCES MODULO(id_modulo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);



INSERT INTO USUARIOS (email, password_hash, rol) VALUES
('admin@yucatan.gob.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('juan.perez@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ciudadano');

INSERT INTO PROPIETARIO (id_usuario, curp, nombre, primer_apellido, segundo_apellido, telefono, email) VALUES
(2, 'PEHJ900515HYNRNN01', 'Juan Carlos', 'Pérez', 'Hernández', '9991234567', 'juan.perez@correo.com');

INSERT INTO MODELO (marca, nombre_modelo) VALUES
('Nissan', 'Versa'),
('Mazda', 'Mazda 2'),
('Chevrolet', 'Aveo'),
('Toyota', 'Yaris'),
('Honda', 'Civic'),
('Italika', 'FT150');

INSERT INTO VEHICULO (num_serie, placa_actual, num_motor, anio, id_propietario, id_modelo) VALUES
('3N1CN7AD5KL123456', 'YUC-123-A', 'HR16DE987654', 2019, 1, 1);

INSERT INTO MODULO (nombre_modulo, direccion, municipio, tel_modulo) VALUES
('Módulo Central Mérida (Kukulcán)', 'Calle 28 x 19 y 21, Col. Unidad Deportiva Kukulcán', 'Mérida', '9999303200'),
('Módulo Valladolid', 'Calle 41 #215 x 46 y 48, Col. Centro', 'Valladolid', '9858562100'),
('Módulo Tizimín', 'Calle 50 #410 x 51 y 53, Col. Centro', 'Tizimín', '9868634200'),
('Módulo Progreso', 'Calle 31 #142 x 76 y 78, Col. Centro', 'Progreso', '9699351800');

INSERT INTO TRAMITE (folio_tramite, tipo_tramite, estado_tramite, observaciones, fecha_inicio, num_serie) VALUES
('YUC-2026-0001', 'Canje de Placas', 'Pendiente', 'Solicitud inicial en espera de revisión.', CURRENT_DATE, '3N1CN7AD5KL123456');

INSERT INTO CITA (fecha_programada, hora, estado_cita, folio_tramite, id_modulo) VALUES
(DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY), '10:00:00', 'Programada', 'YUC-2026-0001', 1);