<?php
include('cabecera.php');
?>

<div class="container">
    <nav class="nav nav-tabs mt-2">
      <a class="nav-link active" href="misPublicaciones.php">Publicaciones</a>
      <a class="nav-link active" href="#">Postulaciones</a>
      <a class="nav-link active" href="#">Calificaciones</a>
    </nav>
</div>


<div id="misPostulaciones">

<?php
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM postulacion WHERE po_fk_u_id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
    if ($postulaciones) {
        foreach ($postulaciones as $postulacion) {
            // origen y destino 
            $stmt_origen = $pdo->prepare("SELECT provincia FROM argentina WHERE arg_id = :origen_id");
            $stmt_origen->bindParam(':origen_id', $postulacion['pu_fk_origen'], PDO::PARAM_INT);
            $stmt_origen->execute();
            $origen = $stmt_origen->fetchColumn();

            $stmt_destino = $pdo->prepare("SELECT provincia FROM argentina WHERE arg_id = :destino_id");
            $stmt_destino->bindParam(':destino_id', $postulacion['pu_fk_destino'], PDO::PARAM_INT);
            $stmt_destino->execute();
            $destino = $stmt_destino->fetchColumn();
            ?>
            
            <div class="col-12 col-md-6 col-lg-3 mb-1 d-flex justify-content-center">
                <div class="card" style="width: 100%;">
                    <div class="card-img-container" style="height: 200px; overflow: hidden;">
                        
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center">Postulacion #<?php echo $postulacion['po_id']; ?></h5>
                        <p class="card-text text-center" style="font-size:12px">Origen: <?php echo $origen ? $origen : 'Desconocido'; ?> - Destino: <?php echo $destino ? $destino : 'Desconocido'; ?></p>
                        <div class="d-flex justify-content-between"> 
                            <a href="publicacion.php?id=<?php echo $postulacion['po_fk_pu_id']; ?>" class="btn btn-outline-info">Ver Más</a>
                            
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }
    } else {
        echo "No has realizado postulaciones";
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
</div>
<!---fin Mis postulaciones-->



  