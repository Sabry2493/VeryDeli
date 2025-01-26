<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['dni'])) {
    $dni = $_FILES['dni'];
    $usuario_id = 23;  // ID del usuario, obtén este valor del contexto adecuado (por ejemplo, sesión).

    // Verificar si el usuario existe
    $sql = "SELECT COUNT(*) FROM usuario WHERE u_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $user_exists = $stmt->fetchColumn();

    if ($user_exists) {
        // Verificar si la carpeta 'uploads' existe, y crearla si no existe
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Mover el archivo a la carpeta de documentos
        $ruta = $upload_dir . basename($dni['name']);
        if (move_uploaded_file($dni['tmp_name'], $ruta)) {
            // Guardar información del documento en la base de datos
            $sql = "INSERT INTO documentos (d_usuario_id, d_ruta, d_tipo, d_fecha_subida) VALUES (?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $tipo = 'DNI';  // Tipo de documento
            $stmt->execute([$usuario_id, $ruta, $tipo]);
            echo "Documento subido correctamente.";
        } else {
            echo "Error al subir el documento.";
        }
    } else {
        echo "Usuario no existe.";
    }
}
?>
