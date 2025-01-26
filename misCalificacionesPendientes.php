<?php
include('cabecera.php');
?>

<div class="container">
    <nav class="nav nav-tabs mt-2">
      <a class="nav-link " href="misPublicaciones.php">Publicaciones</a>
      <a class="nav-link " href="misPostulacionesPendientes.php">Postulaciones Pendientes</a>
      <a class="nav-link " href="misPostulacionesActivas.php">Mis envios</a>
      <a class="nav-link active" href="misCalificacionesPendientes.php">Calificaciones a postulantes</a>
      <a class="nav-link " href="misCalificacionesSolicitantesPendientes.php">Calificaciones a solicitantes</a>
      <a class="nav-link" href="calificacionesRecibidas.php">Calificaciones recibidas</a>
      <a class="nav-link " href="calificacionesDadas.php">Calificaciones Dadas</a>
    </nav>
</div>



<div class="row" id="misCalificaciones">

<?php
$user_id = $_SESSION['user_id'];

try {
    $estado = "finalizada";
    $sql = "SELECT pu_id, pu_titulo, pu_fk_u_id  FROM publicacion WHERE pu_estado = :estado AND pu_fk_u_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->bindParam(':estado', $estado);
    $stmt->execute();
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cont=0;
    if ($publicaciones) {
        foreach ($publicaciones as $publicacion) {
            $publicacionId = $publicacion['pu_id'];
            $publicacionTitulo = $publicacion['pu_titulo'];
            
            // Obtener el ID de envío
            $sql = "SELECT env_id_envio FROM envio WHERE env_id_publicacion = :publicacion";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':publicacion', $publicacionId);
            $stmt->execute();
            $envio = $stmt->fetchColumn();
            
            // Verificar si existe una calificación para el envío
            $sql = "SELECT ca_califica FROM calificacion WHERE ca_id_envio = :envio AND ca_califica = :ca_califica";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':envio', $envio);
            $stmt->bindParam(':ca_califica', $_SESSION['user_id']);
            $stmt->execute();
            $calificacion = $stmt->fetchColumn();
            
            if ($calificacion != NULL) {
                continue;
            } else {
                // Obtener el postulante elegido para la publicación
                $sql = "SELECT po_fk_u_id FROM postulacion WHERE po_fk_pu_id = :publicacion AND po_estado = :estadoPo";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':publicacion', $publicacionId);
                $estadoPo = "elegido";
                $stmt->bindParam(':estadoPo', $estadoPo);
                $stmt->execute();
                $postulante = $stmt->fetchColumn();
                $cont++;
                
                //---------para avatar
                
                $sql = "SELECT u_userName, avatar.a_url 
                        FROM usuario 
                        LEFT JOIN avatar ON usuario.u_avatar = avatar.a_id 
                        WHERE usuario.u_id = :u_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':u_id', $postulante);
                $stmt->execute();
                $postulanteData = $stmt->fetch(PDO::FETCH_ASSOC);

                $nombrePostulante = $postulanteData['u_userName'] ?? 'Nombre no disponible';
                $avatarURL = $postulanteData['a_url'] ?? 'Imagenes/avatarUsuario.png';

                //----------
                
                // Obtener el nombre del postulante
                /*$sql = "SELECT u_userName FROM usuario WHERE u_id = :u_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':u_id', $postulante);
                $stmt->execute();
                $nombrePostulante = $stmt->fetchColumn();*/
                
                // Imprimir la tarjeta
                ?>
                <div class="col-12 col-md-6 col-lg-3 mb-1 d-flex justify-content-center">
                    <div class="card" style="width: 100%;">
                        <div class="card-body">
                            <!-- Mostrar avatar -->
                            <img src="<?php echo htmlspecialchars($avatarURL); ?>" alt="Avatar de <?php echo htmlspecialchars($nombrePostulante); ?>" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                            
                            <h5 class="card-title text-center">Califica a: <?php echo htmlspecialchars($nombrePostulante); ?></h5>
                            <p class="card-text text-center" style="font-size:12px">Publicación que realizó: <?php echo htmlspecialchars($publicacionTitulo); ?></p>
                            
                            <div class="d-flex justify-content-between" align="center"> 
                                <a href="calificacionP_VD.php?env_id_envio=<?php echo htmlspecialchars($envio); ?>" class="btn btn-outline-info">Calificar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    } 
    if($cont==0) {
         echo "<div class='container d-flex justify-content-center align-items-center' style='height: 80vh;'>
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