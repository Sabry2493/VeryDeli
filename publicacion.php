<?php
  session_start();
  $_SESSION['u_id']=14;
  $_SESSION['admin']=true;
  ;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Publicacion VeryDeli</title><!--Php con el nombre de la publicacion-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="publicacionEstilo.css">
  </head>
  <body>
      <?php
          include 'cabecera.html';
      ?>
      <br>
    <!--<h1 align="center">Publicacion de delivery</h1> Php con el nombre de la publicacion-->
    <!--Atributos publicacion: ID, Volumen, Peso, Origen, Destino, Estado, Descripcion-->
    <?php
      include 'coneccion/conexionDB.php';
      extract($_GET);
      $sql="SELECT * FROM publicacion WHERE pu_id=".$publicacion;
      $resultado=mysqli_query($conn,$sql);
      $sqlUbicacion = "SELECT * FROM argentina";
      $resultadoUbicaciones = mysqli_query($conn, $sqlUbicacion);
      $ubicacionExiste = 0;

          
      if(mysqli_num_rows($resultado)>0){
        while($row = mysqli_fetch_row($resultado)){
          $dueñoPublicacion=$row[1];
          $sql="SELECT * FROM usuario WHERE u_id=".$dueñoPublicacion;
          $resultadoNombre=mysqli_query($conn,$sql);

          if(mysqli_num_rows($resultadoNombre)>0){
            while(($rowNombre=mysqli_fetch_row($resultadoNombre))>0){
              $nombre=$rowNombre[3];
            }
          }
          if (mysqli_num_rows($resultadoUbicaciones) > 0) {
            // Bucle que recorre las ubicaciones
            while ($rowUbicacion = mysqli_fetch_row($resultadoUbicaciones)) {
                
                if ($row[3] == $rowUbicacion[0]) {
                    $origen = $rowUbicacion[1];
                    $ubicacionExiste++;
                }
                if ($row[6] == $rowUbicacion[0]) {
                    $destino = $rowUbicacion[1];
                    $ubicacionExiste++;
                }
                // controlamos origen y destino para salir
                if ($ubicacionExiste == 2) {
                    $ubicacionExiste = 0;
                    break;
                }
            }
        }
        if($row[13]==NULL){
          $imagen = "depositphotos_11506024-stock-photo-package.jpg";
        }else{
          $imagen = $row[13];
        }

          echo "
                    <div class='container' align='center'>
                      <div class='card mb-3' style='max-width: 650px;'>
                        <div class='row g-0'>
                            <div class='col-md-4'>";

                            echo"
                            <img src='".$imagen."' class='img-fluid rounded-start' alt='Imagen del paquete'>
                            </div>
                            <div class='col-md-8'>
                            <div class='card-body'>
                                <h5 class='card-title' id='tituloPublicacion'>".$row[2]."</h5>
                                <!--Descripcion-->
                                <p class='card-text'><small class='text-body-secondary'>Fecha de la publicacion: ".$row[12]."</small></p>
                            </div>
                            </div>
                        </div>
                        <div class='card'>
                          <div class='card-header' align='left'>
                            <blockquote class='blockquote mb-0'>
                                <strong>Detalles</strong>: ".$row[11]."
                            </blockquote>

                          </div>
                          <div class='card-body' align='left'>
                            
                                <p class='card-text'><strong>Peso</strong>: ".$row[10]."</p>
                                <p class='card-text'><strong>Volumen</strong>: ".$row[9]."cm3 </p>
                                <p class='card-text'><strong>Provincia de Origen</strong>: ".$origen."</p>
                                <p class='card-text'><strong>Ciudad de origen</strong>: ".$row[4]."</p>
                                <p class='card-text'><strong>Direccion</strong>: ".$row[5]."</p>
                                <p class='card-text'><strong>Provincia de Destino</strong>: ".$destino."</p>
                                <p class='card-text'><strong>Ciudad del destino</strong>: ".$row[7]."</p>
                                <p class='card-text'><strong>Direccion del destino</strong>: ".$row[8]."</p>
                                <p class='card-text'><strong>Dueño de la publicacion</strong>: @$nombre</p>";
                                if(isset($_SESSION['u_id'])){
                                  echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#postulacionModal'>
                                    Postularce
                                  </button>
                                  ";
                                if(isset($_SESSION['admin'])&&$_SESSION['admin']==true){
                                  echo "<a href='quitarPublicacion.php?publicacion=" . $publicacion."'>
                                          <button type='button' class='btn btn-danger' align='right'>
                                            Quitar Publicacion
                                          </button>
                                        </a>";
                                        if (isset($_GET['denuncia'])) {
                                          echo "<a href='descartarDenuncia.php?denuncia=" . $_GET['denuncia'] . "&publicacion=" . $publicacion . "'>
                                              <button type='button' class='btn btn-secondary' align='right'>
                                                  Descartar Denuncia
                                              </button>
                                          </a>";
                                      }
                                }else{
                                  echo  "<button type='button' class='btn btn-secondary' data-bs-toggle='modal' data-bs-target='#denunciaModal'>
                                            Denunciar publicacion
                                          </button>
                                        </a>";
                                }
                              }else{
                                echo "<div class='card-body' align='center'>
                                        <p class='card-text' >Debes iniciar sesion para poder postularte</p>
                                      </div>";
                              }
                              echo" </div>
                              </div>
                            </div>
                            ";
                    //text area para dejar comentario (Hacer control para que solo salga si el usuario esta postulado)
                    if(!isset($_SESSION['u_id'])){
                      echo"<div class='card-body' align='center'>
                            <p class='card-text' >Debes estar postulado a la publicacion para poder comentar</p>
                          </div>
                          <br>
                      ";
                    }else{
                      echo 

                      "<div class='mb-3' style='max-width: 540px;' align='center'>
                        <h2 align='center'>¿Tienes una duda? Deja tu comentario</h2><br>
                        <form action='crearComentario.php' method='post'>
                          <input type='hidden' id='idPublicacion' name='idPublicacion' value='$publicacion'>
                          <textarea class='form-control' id='comentario' name='comentario' rows='3'></textarea>
                          <br>
                          <input type=submit class='btn btn-primary' value='Comentar'>
                        </form>

                      </div>";

                    echo"</div>
                    ";
                    }
                  //arranca el modal de denuncia!!!
                  echo"
                    <div class='modal fade' id='denunciaModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                      <div class='modal-dialog'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='denunciaModalLabel'>Denunciar publicacion</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                          </div>
                          <div class='modal-body'>
                            <form action='denunciarPublicacion.php' method='post'>
                              <input type='hidden' id='idPublicacion' name='idPublicacion' value='$publicacion'>

                              <div class='container' align='center'>
                                <div class='form-check form-check-inline'>
                                  <input class='form-check-input' type='checkbox' id='opcion1' name='opcion1' value='Fraude o Estafa'>
                                  <label class='form-check-label' for='opcion1'>Fraude o Estafa</label>
                                </div>
                                <div class='form-check form-check-inline'>
                                  <input class='form-check-input' type='checkbox' id='opcion2' name='opcion2' value='Perfil Falso'>
                                  <label class='form-check-label' for='opcion2'>Perfil Falso</label>
                                </div>
                                <div class='form-check form-check-inline'>
                                  <input class='form-check-input' type='checkbox' id='opcion3' name='opcion3 value='Spam'>
                                  <label class='form-check-label' for='opcion3'>Spam</label>
                                </div>
                              </div>
                              <br>

                              <div class='container' align='center'>
                                <label for='mensajeDenuncia' align='center'>Comentario adicional</label><br>
                                <textarea name='mensajeDenuncia' id='mensajeDenuncia' rows='4' cols='60'>
                                </textarea>
                              </div>
                              <br>
                              <div class='container' align='center'>
                                <input class='btn btn-primary' type='submit' value='Denunciar'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                              </div>  
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>";

                  //arranca el modal de postulacion!!!

                    echo"
                    <div class='modal fade' id='postulacionModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                      <div class='modal-dialog'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='postulacionModalLabel'>Postularce a la publicacion</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                          </div>
                          <div class='modal-body'>
                            <form action='subirPostulacion.php' method='post'>
                              <input type='hidden' id='idPublicacion' name='idPublicacion' value='$publicacion'>
                              <div class='container' align='center'>
                                Monto:   <input type='number' id='montoPostulacion' name='montoPostulacion' min='0' value='1'><br>
                              </div>
                              <br>
                              <div class='container'>
                                <label for='mensajePostulacion' align='center'>Comentario</label><br>
                                <textarea name='mensajePostulacion' id='mensajePostulacion' rows='4' cols='60'>
                                </textarea>
                              </div>
                              
                              <br>
                              <div class='container' align='center'>
                                <input class='btn btn-primary' type='submit' value='Postularce'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                              </div>  
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>";
        }
      }
      include 'coneccion/cerrarConexion.php';
    ?>
    <!--arranca la seccion de comentarios-->
   
    <div class='container' name='comentarios' style=' width: 650px; padding: 20px; border: 1px solid #ddd;' >
      <h2 align='center'>Comentarios</h2>
      <br>
          <?php
          if(!isset($_SESSION['u_id'])){
            echo"<div class='card-body' align='center'>
                  <p class='card-text' >Debes estar postulado a la publicacion para poder comentar</p>
                </div>
            ";
          }else{
            include 'coneccion/conexionPDO.php';
            $sql = "SELECT * FROM comentario WHERE com_id_publicacion =".$publicacion;
            $stmt = $pdo->query($sql);
            $resultado = $stmt->fetchAll(PDO::FETCH_NUM);

            if (count($resultado) > 0) {
              foreach ($resultado as $row) {

                $sqlUsuario = " SELECT * FROM usuario WHERE u_id = ".$row[2];
                $stmtUsuario = $pdo->query($sqlUsuario);
                $resultadoUsuario = $stmtUsuario->fetchAll(PDO::FETCH_NUM);
                if (count($resultadoUsuario) > 0) {
                  foreach($resultadoUsuario as $rowUsuario){
                    $nombreUsuario = $rowUsuario[3];
                  }
                }

                echo "
                <div class='card mb-3' name='comentario'>
                  <div class='card-body'>
                    <h5 class='card-title'>@$nombreUsuario pregunta:</h5>
                    <p class='card-text'>".$row[3]."</p>
                    <p class='card-text'><small class='text-body-secondary'>Fecha del comentario: ".$row[5]."</small></p>
                  </div>";
                  if($dueñoPublicacion==$_SESSION['u_id']&&$row[4]==null){

                    echo"<div class='card-body'>
                        <form action='responderComentario.php' method='post'>
                            <input type='hidden' name='idPublicacion' id='idPublicacion' value=".$publicacion."
                            <input type='hidden' name='idComentario' id='idComentario' value=".$row[1]."
                            <label for='respuestaComentario'><strong>Respuesta</strong></label> <br><br>
                            <textarea  class='form-control' name='respuestaComentario' id='respuestaComentario' rows='3'>
                            </textarea><br>
                            <input type='submit' class='btn btn-primary' value='Responder'>
                        </form>
                        </div>";
                  }elseif($row[4]!=null){
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>@$nombre responde:</h5>";
                    echo "<p class='card-text'>".$row[4]."</p> </div>";
                  }else{
                    echo "<div class='card-body'>
                          <p class='card-text' align='center'><strong>El dueño de la publicacion aun no ha respondido a este comentario</strong></p>
                          </div>";
                  }
                
                  echo "</div>";
                ;
              }
            }else{
              echo "Aun no hay comentarios en esta publicacion";
            }
            include 'coneccion/cerrarConexionPDO.php';
          }
          ?>
    </div>

    <?php
      include 'pie.html';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>