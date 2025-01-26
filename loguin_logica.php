
<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';
include('header.php');


session_start(); // Iniciar la sesión para almacenar los datos del usuario si es necesario

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['pwd']);
    
    // Validar que los campos no estén vacíos
    if (!empty($usuario) && !empty($password)) {
        
        // Preparar la consulta SQL
        $sql = "SELECT u_id, u_userName, u_pwd FROM usuario WHERE u_userName = :usuario";
        $stmt = $pdo->prepare($sql);
        
        // Vincular el parámetro
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Verificar si se encontró el usuario
        if ($stmt->rowCount() > 0) {
            // Obtener los datos del usuario
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar la contraseña
            if (password_verify($password, $user['u_pwd'])) {
                // Si la contraseña es correcta, almacenar la información del usuario en la sesión
                $_SESSION['user_id'] = $user['u_id'];
                $_SESSION['username'] = $user['u_userName'];
                
                // Redirigir al usuario a la página principal o a un área privada
                header("Location: index.php");
                exit();
            } else {
                // Contraseña incorrecta
                //echo "<div class='alert alert-danger'>Contraseña incorrecta.</div>";
                header("Location: pag2.php");
                exit();
            }
        } else {
            // Usuario no encontrado
            header("Location: pag2.php");
                exit();
        }
    } else {
        // Campos vacíos
        //echo "<div class='alert alert-danger'>Por favor, complete todos los campos.</div>";
        
        header("Location: pag2.php");
                exit();
    }
}
?>