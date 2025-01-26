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
      <a class="nav-link " href="calificacionesRecibidas.php">Calificaciones recibidas</a>
      <a class="nav-link active" href="#">Calificaciones Dadas</a>
    </nav>
</div>



<div class="row" id="misCalificaciones">

<?php
$user_id = $_SESSION['user_id'];

try {
    $estado = "finalizada";
    //$sql = "SELECT ca_calificado, ca_puntaje, ca_fecha, ca_comentario, ca_id_envio FROM calificacion WHERE ca_califica = :id";
    //$sql = "SELECT ca_calificado, ca_puntaje, ca_fecha, ca_comentario, ca_id_envio FROM calificacion WHERE ca_califica = :id AND ca_puntaje > 0";
    //que considere solo aquellas mayor a 0 y que esten en estado entrgadas
     $sql= "SELECT c.ca_calificado, c.ca_puntaje, c.ca_fecha, c.ca_comentario, c.ca_id_envio, e.env_id_publicacion 
        FROM calificacion c
        INNER JOIN envio e ON c.ca_id_envio = e.env_id_envio
        WHERE c.ca_califica = :id 
          AND c.ca_puntaje > 0
          AND e.env_estado = 'entregado'"; // Filtro para envíos entregados
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cant=0;
    if ($calificaciones) {
        foreach ($calificaciones as $calificacion) {
            $cant++;
            $puntaje = $calificacion['ca_puntaje'];
            $calificador = $calificacion['ca_calificado'];
            $fecha=$calificacion['ca_fecha'];
            $comentario=$calificacion['ca_comentario'];
            $envio=$calificacion['ca_id_envio'];
            
            // Obtener el ID de envío
            $sql = "SELECT env_id_publicacion FROM envio WHERE env_id_envio = :envio";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':envio', $envio);
            $stmt->execute();
            $idPublicacion = $stmt->fetchColumn();
            
            // Verificar si existe una calificación para el envío
            $sql = "SELECT pu_titulo FROM publicacion WHERE pu_id = :envio";
            //$sql = "SELECT pu_titulo,pu_foto FROM publicacion WHERE pu_id = :envio";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':envio', $idPublicacion);
            $stmt->execute();
            $publicacion = $stmt->fetchColumn();
            // Obtener la foto de la publicación
            //$fotoPublicacion = $publicacion['pu_foto']; // Foto de la publicación
           
            
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
                            <h5 class="card-title card-header text-center ">Publicacion: <?php echo htmlspecialchars($publicacion); ?></h5>
                            <p class="card-text text-center" style="font-size:12px">Calificaste a: <?php echo htmlspecialchars($calificadorNombre); ?></p>
                            <p class="card-text text-center" style="font-size:12px"> Comentario: <?php echo htmlspecialchars($comentario); ?></p>
                            <p class="card-text text-center" style="font-size:12px">Puntaje: <?php echo htmlspecialchars($puntaje); ?>&#9733;</p>
                            <p class="card-text text-center" style="font-size:12px">Fecha: <?php echo htmlspecialchars($fecha); ?></p>
                            
                        </div>
                    </div>
                </div>
                <?php
            
        }
    } 
    if($cant==0) {
        echo "<div class='container d-flex justify-content-center align-items-center' style='height: 80vh;'>
        <h5>No has realizado calificaciones</h5>
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