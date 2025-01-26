<?php
    include 'coneccion/conexionPDO.php';

    if(isset($_POST['respuestaComentario']) && isset($_POST['idComentario'])){
        $respuesta = $_POST['respuestaComentario'];
        $idComentario = $_POST['idComentario'];
        $idPublicacion= $_POST['idPublicacion'];
        $sql = "UPDATE comentario SET com_respuesta = :respuesta WHERE com_id = :idComentario";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':respuesta', $respuesta);
        $stmt->bindParam(':idComentario', $idComentario);


        if ($stmt->execute()) {
            echo "Se ha guardado la respuesta";
            include 'coneccion/cerrarConexionPDO.php';
            header('location: publicacion.php?publicacion='.$idPublicacion);
            die();
        } else {
            echo "No se pudo responder la publicacion";
            include 'coneccion/cerrarConexionPDO.php';
            header('location: publicacion.php?publicacion='.$idPublicacion);
            die();
        }
    }else{
        echo "No existe un respuesta para el comentario";
    }
    
?>