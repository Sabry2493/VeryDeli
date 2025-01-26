<!DOCTYPE html>
<html lang="en">

<?php
include ('cabecera.php');
include('postulacion.php');
?>
<body>
    <!-- Contenido de todo el index-->
<div class="container-fluid" id="contenido">




<?php
include('filtro.php');

?>



  
    <!-- publicacionesDestacadas(3 de usuarios mejores puntuados)-->

    <div class="row d-flex justify-content-center align-items-center">
    <?php


try {
    $query = "SELECT *
              FROM publicacion
              ORDER BY pu_id DESC
              LIMIT 4";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($publicaciones) {
        foreach ($publicaciones as $publicacion) {
            //nombres de origen y destino 
            $stmt_origen = $pdo->prepare("SELECT provincia FROM argentina WHERE arg_id = :origen_id");
            $stmt_destino = $pdo->prepare("SELECT provincia FROM argentina WHERE arg_id = :destino_id");
            
            // nombre de origen
            $stmt_origen->bindParam(':origen_id', $publicacion['pu_fk_origen'], PDO::PARAM_INT);
            $stmt_origen->execute();
            $origen = $stmt_origen->fetchColumn();

            // nombre del destino
            $stmt_destino->bindParam(':destino_id', $publicacion['pu_fk_destino'], PDO::PARAM_INT);
            $stmt_destino->execute();
            $destino = $stmt_destino->fetchColumn();

            ?>
            <div class="col-12 col-md-6 col-lg-3 mb-1 d-flex justify-content-center">
    <div class="card" style="width: 100%;"> 
        <div class="card-img-container" style="height: 200px; overflow: hidden;">
            <img src="<?php echo $publicacion['pu_foto']; ?>" class="card-img-top" alt="..." style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="card-body">
            <h5 class="card-title text-center">Publicación #<?php echo $publicacion['pu_id']; ?></h5>
            <p class="card-text text-center" style="font-size:12px">Origen: <?php echo $origen ? $origen : 'Desconocido'; ?> - Destino: <?php echo $destino ? $destino : 'Desconocido'; ?></p>
            <div class="d-flex justify-content-between"> 
                <a href="publicacion.php?id=<?php echo $publicacion['pu_id']; ?>" class="btn btn-outline-info">Ver Más</a>
                <form method="post" id="formPostulacion">
    <!-- Tu formulario dentro de la tarjeta -->
    <button type="button" class="btn btnConfirmar" data-bs-toggle="modal" data-bs-target="#modalPostulacion" id="btnPostularse" name="btnPostularse">
        Postularse
    </button>
</form>

            </div>
        </div>
    </div>
</div>

            <?php
        }
    } else {
        echo "No se encontraron publicaciones.";
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
// el pdo se cierra automáticamente al final del script.

?>


<!--Fin pubDestacadas-->




<?php
include('pie.php');

?>

</div>



</div>




</body>
</html>