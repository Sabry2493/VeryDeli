<?php
include('cabecera.php');
?>

<div class="container">
    <nav class="nav nav-tabs mt-2">
      <a class="nav-link " href="misPublicaciones.php">Publicaciones</a>
      <a class="nav-link " href="misPostulacionesPendientes.php">Postulaciones Pendientes</a>
      <a class="nav-link " href="misPostulacionesActivas.php">Mis envios</a>
      <a class="nav-link " href="misCalificacionesPendientes.php">Calificaciones a postulantes</a>
      <a class="nav-link " href="misCalificacionesSolicitantesPendientes.php">Calificaciones a solicitantes</a>
      <a class="nav-link active" href="#">Calificaciones recibidas</a>
      <a class="nav-link " href="calificacionesDadas.php">Calificaciones Dadas</a>
    </nav>
</div>



<div class="row" id="misCalificaciones" >

<?php
$user_id = $_SESSION['user_id'];

try {
    $estado = "finalizada";
    //$sql = "SELECT ca_califica, ca_puntaje, ca_fecha, ca_comentario, ca_id_envio FROM calificacion WHERE ca_calificado = :id";
    $sql = "SELECT ca_califica, ca_puntaje, ca_fecha, ca_comentario, ca_id_envio FROM calificacion WHERE ca_calificado = :id AND ca_puntaje > 0";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cont=0;
    if ($calificaciones) {
        foreach ($calificaciones as $calificacion) {
            $puntaje = $calificacion['ca_puntaje'];
            $calificador = $calificacion['ca_califica'];
            $fecha=$calificacion['ca_fecha'];
            $comentario=$calificacion['ca_comentario'];
            $envio=$calificacion['ca_id_envio'];
            $cont++;
            // Obtener el ID de envío
            $sql = "SELECT env_id_publicacion FROM envio WHERE env_id_envio = :envio";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':envio', $envio);
            $stmt->execute();
            $idPublicacion = $stmt->fetchColumn();
            
            // Verificar si existe una calificación para el envío
            $sql = "SELECT pu_titulo FROM publicacion WHERE pu_id = :envio";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':envio', $idPublicacion);
            $stmt->execute();
            $publicacion = $stmt->fetchColumn();
            
            $sql = "SELECT u_userName FROM usuario WHERE u_id = :u_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':u_id',$calificador);
            $stmt->execute();
            $calificadorNombre = $stmt->fetchColumn();
            
                // Imprimir la tarjeta
                ?>
                <div class="col-12 col-md-6 col-lg-3 mb-1 d-flex justify-content-center">
                    <div class="card" style="width: 100%;">
                        <div class="card-body">
                            <h5 class="card-title card-header text-center "><stronger>Publicacion</stronger>: <?php echo htmlspecialchars($publicacion); ?></h5>
                            <p class="card-text text-center" style="font-size:12px"><stronger>Te ha calificado</stronger>: <?php echo htmlspecialchars($calificadorNombre); ?></p>
                            <p class="card-text text-center" style="font-size:12px"><stronger>Comentario</stronger>: <?php echo htmlspecialchars($comentario); ?></p>
                            <p class="card-text text-center" style="font-size:12px"><stronger>Puntaje</stronger>: <?php echo htmlspecialchars($puntaje); ?>&#9733;</p>
                            <p class="card-text text-center" style="font-size:12px"><stronger>Fecha</stronger>: <?php echo htmlspecialchars($fecha); ?></p>
                        </div>
                    </div>
                </div>
                <?php
            
        }
    }
    if($cont==0){
        echo "<div class='container d-flex justify-content-center align-items-center' style='height: 80vh;''>
                 <h5>No te han calificado</h5>
              </div>";
    }
    
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
</div>
<?php
    include 'pie.php';
?>