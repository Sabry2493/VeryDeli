<?php
    session_start();
    include 'coneccion/conexionPDO.php';
try{
    if(isset($_POST['opcion1']) || isset($_POST['opcion2']) || isset($_POST['opcion3'])|| isset($_POST['mensajeDenuncia'])){
        $usuarioId = $_SESSION['u_id'];
        $mensaje = $_POST['mensajeDenuncia'];
        $idPublicacion = $_POST['idPublicacion'];
        $opcionesStr = "";
        $estado = "pendiente";
        if(isset($_POST['opcion1'])){
            $opcionesStr=$_POST['opcion1']." ";
        }
        if(isset($_POST['opcion2'])){
            $opcionesStr=$_POST['opcion2']." ";
        }
        if(isset($_POST['opcion3'])){
            $opcionesStr=$_POST['opcion3'];
        }

        $sql = "INSERT INTO denuncias (de_fk_pu_id,de_fk_u_id,de_tags,de_mensaje,de_estado) 
                VALUES (:de_fk_pu_id,:de_fk_u_id,:de_tags,:de_mensaje,:de_estado)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':de_fk_pu_id', $idPublicacion);
        $stmt->bindParam(':de_fk_u_id', $usuarioId);
        $stmt->bindParam(':de_tags', $opcionesStr);
        $stmt->bindParam(':de_mensaje', $mensaje);
        $stmt->bindParam(':de_estado', $estado);

        if ($stmt->execute()) {
            echo "Se ha guardado la denuncia";
            include 'coneccion/cerrarConexionPDO.php';
            header('location: buscador.php');
            die();
        } else {
            echo "Hubo un problema al subir la denuncia";
            include 'coneccion/cerrarConexionPDO.php';
            header('location: buscador.php');
            die();
        }
    }
    
    
}catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
    
    include 'coneccion/cerrarConexion.php';
    header('Location: buscador.php');
    die();

?>