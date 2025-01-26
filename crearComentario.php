<?php
    include 'coneccion/conexionPDO.php';
    if(isset($_POST['comentario'])){
        $comentario = $_POST['comentario'];
        $idPublicacion = $_POST['idPublicacion'];
        $idComentador = 14;
        $fechaActual = new DateTime();
        $fechaFormateada = $fechaActual->format('Y-m-d H:i:s');
        $sql = "INSERT INTO comentario (com_id_publicacion, com_id_comentador, com_comentario, com_fecha)
                VALUES (:com_id_publicacion, :com_id_comentador, :com_comentario, :com_fecha)";
        
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':com_id_publicacion', $idPublicacion);
        $stmt->bindParam(':com_id_comentador', $idComentador);
        $stmt->bindParam(':com_comentario', $comentario);
        $stmt->bindParam(':com_fecha', $fechaFormateada);
        if ($stmt->execute()) {
            echo "Se ha guardado el comentario";
            include 'coneccion/cerrarConexionPDO.php';
            header('location: publicacion.php?publicacion='.$idPublicacion);
            die();
        } else {
            echo "No se pudo comentar la publicacion";
            include 'coneccion/cerrarConexionPDO.php';
            header('location: publicacion.php?publicacion='.$idPublicacion);
            die();
        }
    }else{
        echo "No existe un comentario para la publicacion";
    }
    
?>