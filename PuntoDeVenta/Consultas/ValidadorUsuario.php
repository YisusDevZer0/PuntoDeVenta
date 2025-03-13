<?php
session_start();
require_once("db_connect.php");

/**
 * Clase para manejar la validación de usuarios
 */
class ValidadorUsuario {
    private $conn;
    private $stmt;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    /**
     * Limpia y sanitiza los datos de entrada
     * @param string $data
     * @return string
     */
    private function sanitizarDatos($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    /**
     * Valida las credenciales del usuario
     * @param string $email
     * @param string $password
     * @return array|bool
     */
    public function validarCredenciales($email, $password) {
        try {
            $email = $this->sanitizarDatos($email);
            $password = $this->sanitizarDatos($password);

            $sql = "SELECT u.Id_PvUser, u.Correo_Electronico, u.Password, u.Estatus,
                    u.Fk_Usuario, t.ID_User, t.TipoUsuario 
                    FROM Usuarios_PV u
                    INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
                    WHERE u.Correo_Electronico = ? AND u.Estatus = 'Activo'
                    LIMIT 1";

            $this->stmt = mysqli_prepare($this->conn, $sql);
            if (!$this->stmt) {
                throw new Exception("Error en la preparación de la consulta");
            }

            mysqli_stmt_bind_param($this->stmt, "s", $email);
            mysqli_stmt_execute($this->stmt);
            $resultado = mysqli_stmt_get_result($this->stmt);
            
            if ($row = mysqli_fetch_assoc($resultado)) {
                if ($row['Password'] === $password) { // En un entorno real, usar password_verify()
                    return $row;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en validación de usuario: " . $e->getMessage());
            return false;
        } finally {
            if ($this->stmt) {
                mysqli_stmt_close($this->stmt);
            }
        }
    }

    /**
     * Establece la sesión según el tipo de usuario
     * @param array $userData
     * @return bool
     */
    public function establecerSesion($userData) {
        if (!$userData) return false;

        $tiposUsuario = [
            'Administrador' => 'ControlMaestro',
            'Farmaceutico' => 'VentasPos',
            'Administrador General' => 'AdministradorGeneral',
            'Supervisor' => 'ResponsableDeSupervision',
            'Recursos Humanos' => 'AdministradorRH',
            'Responsable Cedis' => 'ResponsableDelCedis',
            'Inventarios' => 'Inventarios',
            'Enfermero' => 'Enfermeria'
        ];

        if (isset($tiposUsuario[$userData['TipoUsuario']])) {
            $_SESSION[$tiposUsuario[$userData['TipoUsuario']]] = $userData['Id_PvUser'];
            $_SESSION['ultima_actividad'] = time();
            return true;
        }
        return false;
    }
}

// Procesar la solicitud de login
if (isset($_POST['login_button'])) {
    $validador = new ValidadorUsuario($conn);
    $userData = $validador->validarCredenciales($_POST['user_email'], $_POST['password']);
    
    if ($userData && $validador->establecerSesion($userData)) {
        echo "ok";
    } else {
        echo "Error: Usuario no autorizado";
    }
}
?>
