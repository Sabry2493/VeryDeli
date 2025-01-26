<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('cabecera.php');
?>

<div class="container">
    <nav class="nav nav-tabs mt-2">
      <a class="nav-link " href="misPublicaciones.php">Publicaciones</a>
      <a class="nav-link " href="misPostulacionesPendientes.php">Postulaciones Pendientes</a>
      <a class="nav-link " href="misPostulacionesActivas.php">Mis envios</a>
      <a class="nav-link " href="misCalificacionesPendientes.php">Calificaciones a postulantes</a>
      <a class="nav-link active" href="misCalificacionesSolicitantesPendientes.php">Calificaciones a solicitantes</a>
      <a class="nav-link" href="calificacionesRecibidas.php">Calificaciones recibidas</a>
      <a class="nav-link " href="calificacionesDadas.php">Calificaciones Dadas</a>
    </nav>
</div>



<div class="row" id="misCalificaciones">

<?php
$user_id = $_SESSION['user_id'];

try {
    $estado = "finalizada";
    $sql = "SELECT pu_id, pu_titulo, pu_fk_u_id FROM publicacion WHERE pu_estado = :estado";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':estado', $estado);
    $stmt->execute();
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $contador=0;
    
    if (count($publicaciones) > 0) {
        
        //recorrer las publicaciones
        foreach ($publicaciones as $publicacion) {
            $publicacionId = $publicacion['pu_id'];
            $publicacionTitulo = $publicacion['pu_titulo'];
            
            // Obtener el ID de envío
            $sql = "SELECT env_id_envio FROM envio WHERE env_id_publicacion = :publicacion";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':publicacion', $publicacionId);
            $stmt->execute();
            $envio = $stmt->fetchColumn();
            
            
            /*Gabriel
            // Verificar si existe una calificación para el envío
            $sql = "SELECT ca_califica FROM calificacion WHERE ca_id_envio = :envio AND ca_califica = :ca_califica";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':envio', $envio);
            $stmt->bindParam(':ca_califica', $_SESSION['user_id']);
            $stmt->execute();
            $calificacion = $stmt->fetchColumn();
            
            $sql = "SELECT po_fk_u_id FROM postulacion WHERE po_fk_pu_id = :publicacion AND po_estado = :estadoPo";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':publicacion', $publicacionId);
            $estadoPo = "elegido";
            $stmt->bindParam(':estadoPo', $estadoPo);
            $stmt->execute();
            $postulante = $stmt->fetchColumn();
            
            if ($calificacion !== false) {
                continue;
            } else {
                Gabriel
             */
            
            //INICIO del cambio 1
            // Verificar las calificaciones pendientes (ca_puntaje = 0)
            //$sql = "SELECT ca_puntaje FROM calificacion WHERE ca_id_envio = :envio AND ca_califica = :ca_califica";
            //$sql = "SELECT ca_id_envio, ca_puntaje FROM calificacion WHERE ca_califica = :ca_califica AND ca_puntaje = 0";
            $sql = "SELECT ca_puntaje FROM calificacion WHERE ca_califica = :ca_califica AND ca_puntaje = 0";
            $stmt = $pdo->prepare($sql);
            //$stmt->bindParam(':envio', $envio);
            $stmt->bindParam(':ca_califica', $_SESSION['user_id']);
            $stmt->execute();
            $calificacion = $stmt->fetchColumn();

            // Si la calificación está pendiente (ca_puntaje = 0), la mostramos
            if ($calificacion === '0') {
                // Obtener el nombre del solicitante
                /*$sql = "SELECT u_userName FROM usuario WHERE u_id = :u_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':u_id', $publicacion['pu_fk_u_id']);
                $stmt->execute();
                $nombreSolicitante = $stmt->fetchColumn();*/
                
                //-----para avatar
                $sql = "SELECT u_userName, u_avatar FROM usuario WHERE u_id = :u_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':u_id', $publicacion['pu_fk_u_id']);
                $stmt->execute();
                $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $nombreSolicitante = $usuarioData['u_userName'];
                $avatarId = $usuarioData['u_avatar'];
                
                // Obtener la URL del avatar desde la tabla `avatar`
                $sql = "SELECT a_url FROM avatar WHERE a_id = :a_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':a_id', $avatarId);
                $stmt->execute();
                $avatarUrl = $stmt->fetchColumn();
                
                // Si no hay URL, asignar una imagen predeterminada
                $avatarUrl = !empty($avatarUrl) ? htmlspecialchars($avatarUrl) : 'ruta_a_avatar_por_defecto.png';
                
                //------------

                // Obtener el ID del postulante para comparar
                $sql = "SELECT po_fk_u_id FROM postulacion WHERE po_fk_pu_id = :publicacion AND po_estado = :estadoPo";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':publicacion', $publicacionId);
                $estadoPo = "elegido";
                $stmt->bindParam(':estadoPo', $estadoPo);
                $stmt->execute();
                $postulante = $stmt->fetchColumn();
            //FIN del cambio 1
                
                if($postulante==$user_id){
                 $contador++;   
                 
                /*Saco esto ya que lo obtuve arriba en cambio 1
                // Obtener el nombre del Solicitante
                $sql = "SELECT u_userName FROM usuario WHERE u_id = :u_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':u_id', $publicacion['pu_fk_u_id']);
                $stmt->execute();
                $nombreSolicitante = $stmt->fetchColumn();*/
                
                // Imprimir la tarjeta
                ?>
                <div class="col-12 col-md-6 col-lg-3 mb-1 d-flex justify-content-center">
                    <div class="card" style="width: 100%;">
                        <div class="card-body">
                             <!-- Avatar del usuario -->
                             <img src="<?php echo $avatarUrl; ?>" alt="Avatar de <?php echo htmlspecialchars($nombreSolicitante); ?>" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                            
                            <!-- nombre y titulo publicacion -->
                            <h5 class="card-title text-center">Califica a: <?php echo htmlspecialchars($nombreSolicitante); ?></h5>
                            <p class="card-text text-center" style="font-size:12px">Publicación que realizó: <?php echo htmlspecialchars($publicacionTitulo); ?></p>
                            
                            <div class="d-flex justify-content-between" align="center"> 
                                <a href="calificacionS_VD.php?env_id_envio=<?php echo htmlspecialchars($envio); ?>" class="btn btn-outline-info">Calificar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
            }
          
        }
        

    } else {
        echo "<div class='container d-flex justify-content-center align-items-center' style='height: 80;'>
                 <h5>No tienes calificaciones pendientes</h5>
              </div>";
    }
    if($contador==0){
        echo "<div class='container d-flex justify-content-center align-items-center' style='height: 80vh;''>
                 <h5>No tienes calificaciones pendientes</h5>
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
