<?php
include "db_connection.php";

$sql = "CREATE TABLE IF NOT EXISTS Sugerencias_Pedidos (
    ID_Sugerencia INT AUTO_INCREMENT PRIMARY KEY,
    ID_Prod_POS VARCHAR(255) NOT NULL,
    Cod_Barra VARCHAR(255) NOT NULL,
    Nombre_Prod VARCHAR(255) NOT NULL,
    Existencia_Actual INT NOT NULL,
    Cantidad_Sugerida INT NOT NULL,
    Fecha_Sugerencia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estado_Sugerencia ENUM('Pendiente', 'Procesada', 'Cancelada') DEFAULT 'Pendiente',
    ID_H_O_D VARCHAR(255) NOT NULL,
    Fk_Sucursal INT NOT NULL,
    Usuario_Genera VARCHAR(255) NOT NULL,
    FOREIGN KEY (ID_Prod_POS) REFERENCES Productos_POS(ID_Prod_POS),
    FOREIGN KEY (Fk_Sucursal) REFERENCES Sucursales(ID_Sucursal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Tabla Sugerencias_Pedidos creada exitosamente";
} else {
    echo "Error creando la tabla: " . $conn->error;
}

$conn->close();
?> 