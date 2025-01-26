<?php
include ('conexion.php');

// Función para validar el nombre y apellido
function validarNombre($cadena) {
    // Expresión regular: al menos una consonante, mínimo 3 caracteres, sin letras consecutivas iguales
    $patron = "/^(?!.*([A-Za-z])\\1)(?=.*[BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz]).{3,}$/";
    return preg_match($patron, $cadena);
}



// Inicializar el array de errores
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $userName = trim($_POST['userName']);
    $pwd = $_POST['pwd'];
    $email = trim($_POST['email']);
    $avatar=1;

    // Validaciones

    // Validar nombre con la función
    if (!validarNombre($nombre)) {
        $errores[] = "Ingrese un nombre real de al menos 3 letras";
    }

    // Apellido
    if (empty($apellido) || !preg_match("/^[A-Za-z]{3,}$/", $apellido)) {
        $errors[] = "El apellido debe tener al menos 3 letras y no contener números.";
    }

    if (empty($userName) || !preg_match("/^[A-Za-z0-9]{3,}$/", $userName)) {
        $errors[] = "El nombre de usuario debe tener al menos 3 caracteres alfanuméricos.";
    }

    if (empty($pwd) || !preg_match("/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/", $pwd)) {
        //$errors[] = "La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, un número, y un carácter especial.";
    }

        // Email
        if (!preg_match("/^[A-Za-z0-9]{3,}@[A-Za-z]{5}.com$/", $email)) {
            $errors[] = "Por favor ingresa un email válido que tenga al menos 3 caracteres alfanuméricos antes de '@', un dominio de exactamente 5 letras y termine en '.com'.";
        }
    

    

    // Si no hay errores, insertar el usuario en la base de datos
    if (empty($errors)) {
        try {
            // Hashear la contraseña
            $hashed_pwd = password_hash($pwd, PASSWORD_BCRYPT);

            // Preparar la consulta SQL
            $sql = "INSERT INTO usuario (u_nombre, u_apellido, u_userName, u_pwd, u_email, u_avatar) 
                    VALUES (:nombre, :apellido, :userName, :pwd, :email, :avatar)";
            
            // Preparar la declaración
            $stmt = $pdo->prepare($sql);
            
            // Bind de los parámetros
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':userName', $userName);
            $stmt->bindParam(':pwd', $hashed_pwd);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':avatar', $avatar); //por defecto le pongo foto User

            // Ejecutar la consulta
            if ($stmt->execute()) {
                header("Location: index.php");
            } else {
                echo "Error al guardar el usuario.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Mostrar errores
        foreach ($errors as $error) {
            echo "<p class='text-danger'>$error</p>";
        }
    }
}
?>
