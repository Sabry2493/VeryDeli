<?php
include 'cabecera.php';

try {
    if (isset($_POST['btnPublicar'])) { // Cambiado a $_POST

        // Verificar que todos los datos requeridos están presentes
        if (
            isset($_POST['titulo']) && isset($_POST['peso']) && isset($_POST['volumen']) && 
            isset($_POST['contactoDestino']) && isset($_POST['provinciaOrigen']) && 
            isset($_POST['ciudadOrigen']) && isset($_POST['direccionOrigen']) &&
            isset($_POST['provinciaDestino']) && isset($_POST['ciudadDestino']) && 
            isset($_POST['direccionDestino'])
        ) {
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $peso = $_POST['peso'];
            $volumen = $_POST['volumen'];
            $destinatario=$_POST['nombreContactoDestino'];
            $contacto = $_POST['contactoDestino'];
            $provinciaOrigen = $_POST['provinciaOrigen'];
            $ciudadOrigen = $_POST['ciudadOrigen'];
            $direccionOrigen = $_POST['direccionOrigen'];
            $provinciaDestino = $_POST['provinciaDestino'];
            $ciudadDestino = $_POST['ciudadDestino'];
            $direccionDestino = $_POST['direccionDestino'];
            $imagen = '';
            $idUsuario = $_SESSION['user_id'];
            $estadoPu = "publicada";

            if (isset($_FILES['imagenPaquete']) && $_FILES['imagenPaquete']['error'] == 0) {
                $foto = $_FILES['imagenPaquete'];
                $nombre = $foto['name'];
                $tipo = $foto['type'];
                $ruta = $foto['tmp_name'];
                $size = $foto['size'];
                $carpeta = "foto/";

                $allowed_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($tipo, $allowed_types)) {
                    echo "Error: el archivo no es una imagen.";
                    exit;
                } elseif ($size > 3 * 1024 * 1024) {
                    echo "Error: el tamaño máximo permitido es de 3MB.";
                    exit;
                } else {
                    $src = $carpeta . $nombre;
                    move_uploaded_file($ruta, $src);
                    $imagen = $src;
                }
            }

            $sql = "INSERT INTO publicacion
            (pu_fk_u_id, pu_titulo, pu_fk_origen_provincia, pu_fk_origen_ciudad, pu_fk_origen_direccion,
            pu_fk_destino_provincia, pu_fk_destino_ciudad, pu_fk_destino_direccion, pu_volumen, pu_peso,
            pu_descripcion, pu_foto, pu_estado, pu_contacto_destino,pu_nombre_contacto)
            VALUES (:pu_fk_u_id, :pu_titulo, :pu_fk_origen_provincia, :pu_fk_origen_ciudad, :pu_fk_origen_direccion,
            :pu_fk_destino_provincia, :pu_fk_destino_ciudad, :pu_fk_destino_direccion, :pu_volumen, :pu_peso,
            :pu_descripcion, :pu_imagen, :pu_estado, :pu_contacto_destino, :pu_nombre_contacto)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':pu_fk_u_id', $idUsuario);
            $stmt->bindParam(':pu_titulo', $titulo);
            $stmt->bindParam(':pu_descripcion', $descripcion);
            $stmt->bindParam(':pu_peso', $peso);
            $stmt->bindParam(':pu_volumen', $volumen);
            $stmt->bindParam(':pu_fk_origen_provincia', $provinciaOrigen);
            $stmt->bindParam(':pu_fk_origen_ciudad', $ciudadOrigen);
            $stmt->bindParam(':pu_fk_origen_direccion', $direccionOrigen);
            $stmt->bindParam(':pu_fk_destino_provincia', $provinciaDestino);
            $stmt->bindParam(':pu_fk_destino_ciudad', $ciudadDestino);
            $stmt->bindParam(':pu_fk_destino_direccion', $direccionDestino);
            $stmt->bindParam(':pu_imagen', $imagen);
            $stmt->bindParam(':pu_estado', $estadoPu);
            $stmt->bindParam(':pu_contacto_destino', $contacto);
            $stmt->bindParam(':pu_nombre_contacto', $destinatario);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit();
            }
        } else {
            echo "Faltan campos obligatorios para la publicación.";
        }
    }
} catch (PDOException $e) {
    echo "Error en la inserción de la publicación: " . $e->getMessage();
}
?>
