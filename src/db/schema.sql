Definición de tablas de la base de datos
-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de ligas y partidos
CREATE TABLE partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    liga VARCHAR(100),
    equipo1 VARCHAR(100),
    equipo2 VARCHAR(100),
    hora TIME,
    fecha DATE
);

-- Tabla de noticias
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255),
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
