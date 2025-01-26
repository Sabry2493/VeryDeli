<?php
    include 'coneccion/conexionPDO.php';
    $sql = "UPDATE publicacion SET pu_estado = :estado WHERE pu_id = :publicacion_id";
    $stmt = $pdo->prepare($sql);
    $estado = "descartada";
    $publicacion_id = $_GET['publicacion'];

    if ($stmt->execute([':estado' => $estado, ':publicacion_id' => $publicacion_id])) {
        include 'denunciaConfirmada.php';
        
        include 'coneccion/cerrarConexionPDO.php';
        header('Location: buscador.php');
        exit();
    } else {
        echo "Hubo un problema al descartar la publicacion";
    }

    include 'coneccion/cerrarConexionPDO.php';
?>