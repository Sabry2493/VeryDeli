<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $usuario_id = $_SESSION['user_id'];

        // Actualizar datos del usuario
        $username = $_POST['username'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $avatarSeleccionado = $_POST['avatarSeleccionado'];

        $sql = "UPDATE usuario SET 
                    u_userName = :username, 
                    u_nombre = :nombre, 
                    u_apellido = :apellido,
                    u_email = :email, 
                    u_telefono = :telefono, 
                    u_domicilio = :direccion, 
                    u_avatar = :avatar
                WHERE u_id = :user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':avatar', $avatarSeleccionado);
        $stmt->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            echo "<p>Error al actualizar los datos del usuario.</p>";
            exit();
        }

        // Actualizar vehículos existentes
        $cantidadVehiculos = $_POST['cantidadVehiculos'];
        
        for ($i = 1; $i <= $cantidadVehiculos; $i++) {
            $modelo = $_POST["v{$i}_modelo"];
            $patente = $_POST["v{$i}_patente"];
            $vehiculo_id = $_POST["v{$i}_id"]; // El ID del vehículo es necesario para actualizarlo

            // Verificar si hay una nueva foto
            if (isset($_FILES["v{$i}_foto"]) && $_FILES["v{$i}_foto"]['error'] == 0) {
                $foto = $_FILES["v{$i}_foto"];
                $nombreFoto = uniqid() . "_" . basename($foto['name']);
                $rutaFoto = $foto['tmp_name'];
                $sizeFoto = $foto['size'];

                $allowed_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
                if (in_array($foto['type'], $allowed_types) && $sizeFoto <= 3 * 1024 * 1024) {
                    $src = "foto/" . $nombreFoto;
                    move_uploaded_file($rutaFoto, $src);

                    // Actualizar el vehículo con la nueva foto
                    $sqlUpdate = "UPDATE vehiculo SET v_modelo = :modelo, v_patente = :patente, v_foto = :foto WHERE v_id = :vehiculo_id";
                    $stmtUpdate = $pdo->prepare($sqlUpdate);
                    $stmtUpdate->bindParam(':modelo', $modelo);
                    $stmtUpdate->bindParam(':patente', $patente);
                    $stmtUpdate->bindParam(':foto', $src);
                    $stmtUpdate->bindParam(':vehiculo_id', $vehiculo_id, PDO::PARAM_INT);
                } else {
                    echo "<p>Error con la imagen del vehículo $i. Solo se permiten imágenes jpg, jpeg, png, gif y máximo 3MB.</p>";
                    exit();
                }
            } else {
                // Si no se sube una nueva foto, solo actualizar el modelo y patente
                $sqlUpdate = "UPDATE vehiculo SET v_modelo = :modelo, v_patente = :patente WHERE v_id = :vehiculo_id";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':modelo', $modelo);
                $stmtUpdate->bindParam(':patente', $patente);
                $stmtUpdate->bindParam(':vehiculo_id', $vehiculo_id, PDO::PARAM_INT);
            }

            if (!$stmtUpdate->execute()) {
                echo "<p>Error al guardar el vehículo $i.</p>";
                exit();
            }
        }

        header("Location: index.php");
        exit();
    } else {
        echo "<p>No estás conectado. Por favor inicia sesión.</p>";
    }
} else {
    echo "<p>Solicitud no válida.</p>";
}
?>
