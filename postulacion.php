<?php


 // Verifica si el usuario está logueado comprobando una variable de sesión
 if (!isset($_SESSION['user_id'])) {
   // Si la variable de sesión 'usuario' no existe, redirigir a la página de inicio de sesión
   header("Location: loguin_formulario.php");
   exit(); // Detiene la ejecución del script
 }
 
 ?>
 <!-- Modal para postularse -->
 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Postularse a la Publicación</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="subirPostulacion.php" method="post">
                                <input type="hidden" name="idPublicacion" value="<?= $publicacion_id ?>">
                                <div class="container" align="center">
                                    Monto: <input type="number" name="montoPostulacion" min="0" value="1"><br>
                                </div>
                                <br>
                                <div class="container" align="center">
                                    <label for="mensajePostulacion" align="center">Comentario</label><br>
                                    <textarea name="mensajePostulacion" rows="4" cols="60"></textarea>
                                </div>
                                <br>
                                <div class="container" align="center">
                                    <input class="btn btn-primary" type="submit" value="Postularse">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>