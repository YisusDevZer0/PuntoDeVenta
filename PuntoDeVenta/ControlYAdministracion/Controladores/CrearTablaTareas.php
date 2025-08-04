<?php
include_once "db_connect.php";

// Script para crear la tabla de tareas
$sql = "CREATE TABLE IF NOT EXISTS Tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    prioridad ENUM('Baja', 'Media', 'Alta') DEFAULT 'Media',
    fecha_limite DATE,
    estado ENUM('Por hacer', 'En progreso', 'Completada', 'Cancelada') DEFAULT 'Por hacer',
    asignado_a INT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_asignado_a (asignado_a),
    INDEX idx_creado_por (creado_por),
    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad),
    INDEX idx_fecha_limite (fecha_limite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Tabla Tareas creada exitosamente<br>";
} else {
    echo "Error creando tabla: " . $conn->error . "<br>";
}

// Crear índices adicionales para optimizar consultas
$indexes = [
    "CREATE INDEX idx_tareas_estado_prioridad ON Tareas(estado, prioridad)",
    "CREATE INDEX idx_tareas_asignado_fecha ON Tareas(asignado_a, fecha_limite)",
    "CREATE INDEX idx_tareas_creado_fecha ON Tareas(creado_por, fecha_creacion)"
];

foreach ($indexes as $index) {
    if ($conn->query($index) === TRUE) {
        echo "Índice creado exitosamente<br>";
    } else {
        echo "Error creando índice: " . $conn->error . "<br>";
    }
}

echo "Script de creación de tabla completado.";
?> 