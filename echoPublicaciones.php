<?php
    $publicacionesPorPagina = 7;
    $paginaActual = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
    $offset = ($paginaActual - 1) * $publicacionesPorPagina;

    // Consulta SQL con límite y desplazamiento para obtener las publicaciones de la página actual
    $sqlPublicaciones = "SELECT * FROM publicacion LIMIT :offset, :limit";
    $stmtPublicaciones = $pdo->prepare($sqlPublicaciones);
    $stmtPublicaciones->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmtPublicaciones->bindValue(':limit', $publicacionesPorPagina, PDO::PARAM_INT);
    $stmtPublicaciones->execute();
    
    $publicaciones = $stmtPublicaciones->fetchAll(PDO::FETCH_ASSOC);

    // Define la consulta SQL para ubicaciones
    $sqlUbicacion = "SELECT * FROM argentina";
    $stmtUbicaciones = $pdo->query($sqlUbicacion);
    $resultadoUbicaciones = $stmtUbicaciones->fetchAll(PDO::FETCH_NUM); 

    $ubicacionExiste = 0;

    if (count($publicaciones) > 0) {
        
        foreach ($publicaciones as $publicacion) {
            
            $ubicacionExiste = 0;

            if (count($resultadoUbicaciones) > 0) {
                
                foreach ($resultadoUbicaciones as $rowUbicacion) {
                    if ($publicacion['pu_fk_origen'] == $rowUbicacion[0]) {  
                        $origen = $rowUbicacion[1];
                        $ubicacionExiste++;
                    }
                    if ($publicacion['pu_fk_destino']  == $rowUbicacion[0]) {  
                        $destino = $rowUbicacion[1];
                        $ubicacionExiste++;
                    }

                    if ($ubicacionExiste == 2) {
                        $ubicacionExiste = 0;
                        break;
                    }
                }
            }

            // Generar un id dinámico para el modal
            $modalId = "modalPostulacion_" . $publicacion['pu_id'];

            // Imprimir la publicación con origen y destino
            echo "
            <div class='card d-flex flex-row ' style='height: 100%;'>
                <img src='".$publicacion['pu_foto']."' alt='' class='card-img-left' style='width: 150px; height: auto; object-fit: cover;'> 
                <div class='card-body d-flex flex-column'>
                    <div class='flex-grow-1'>
                        <h5 class='card-title'>" . $publicacion['pu_id'] . "</h5>
                        <p class='card-text'></p>
                        <p class='card-text'> Descripción: " . $publicacion['pu_descripcion'] . "</p>
                        <p class='card-text'> Peso: " . $publicacion['pu_peso'] . "kg</p>
                        <small class='text-body-secondary'><strong>Origen</strong>: " . $origen ." - ".$origen."</small> 
                        <small class='text-body-secondary'><strong>Destino</strong>: " . $destino ." - ".$destino. "</small><br><br>
                    </div>
                    <div class='ms-auto'> 
                        <a href='verPublicacion.php?publicacion=" . $publicacion['pu_id'] . "' class='btn btn-outline-info'>Ver más</a>"; 

            // Empieza el modal de postulación
            echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#$modalId'>
                    Postularse
                </button>      

                <div class='modal fade' id='$modalId' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='exampleModalLabel'>Postularse a la publicación</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <form action='subirPostulacion.php' method='post'>
                                    <div class='container' align='center'>
                                        Monto:   <input type='number' id='montoPostulacion' name='montoPostulacion' min='0' value='1'><br>
                                    </div>
                                    <br>
                                    <div class='container' align='center'>
                                        <label for='mensajePostulacion' align='center'>Comentario</label><br>
                                        <textarea name='mensajePostulacion' id='mensajePostulacion' rows='4' cols='60'></textarea>
                                    </div>
                                    <br>
                                    <div class='container' align='center'>
                                        <input class='btn btn-primary' type='submit' value='Postularse'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                                    </div>  
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            ";
        }
    } else {
        echo "<br><br><br><h2 align='center'>No hay publicaciones que coincidan con el resultado que buscas</h2><br><br><br>";
    }

    $totalPublicaciones = $pdo->query("SELECT COUNT(*) FROM publicacion")->fetchColumn();
    $totalPaginas = ceil($totalPublicaciones / $publicacionesPorPagina);

    echo "<br>
        <div align='right'><nav aria-label='Page navigation example'>
        <ul class='pagination'>";
    if ($paginaActual > 1) {
        echo "<li class='page-item'><a class='page-link' href=?pag=" . ($paginaActual - 1) . ">Anterior</a></li>";
    }
    for ($i = 1; $i <= $totalPaginas; $i++) {
        echo "<li class='page-item'><a class='page-link' href=?pag=" . $i . ">". $i . "</a></li>";
    }
    if ($paginaActual < $totalPaginas) {
        echo "<li class='page-item'><a class='page-link' href=?pag=". ($paginaActual + 1) . ">Siguiente</a></li>";
    }
    echo "</ul></nav></div>";
?>