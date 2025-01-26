<?php
include 'cabecera.php';
    /*$publicacionesPorPagina = 7;
    $paginaActual = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
    $offset = ($paginaActual - 1) * $publicacionesPorPagina;

    // Consulta SQL con límite y desplazamiento para obtener las publicaciones de la página actual
    $sqlPublicaciones = "SELECT * FROM denuncias LIMIT :offset, :limit";
    $stmtPublicaciones = $pdo->prepare($sqlPublicaciones);
    $stmtPublicaciones->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmtPublicaciones->bindValue(':limit', $publicacionesPorPagina, PDO::PARAM_INT);
    $stmtPublicaciones->execute();
    */
    ?>

    <div class="container">
    <nav class="nav nav-tabs mt-2">
      <a class="nav-link active" href="#">Denuncias</a>
      <a class="nav-link active" href="ventanaAdminVerificar.php">Verificar perfiles</a>
      
    </nav>
</div>


<?php

$sql = "SELECT * FROM denuncias WHERE d_estado= 'pendiente'";
$stmt = $pdo->query($sql);
$resultadoDenuncias = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($resultadoDenuncias) > 0) {
    foreach ($resultadoDenuncias as $denuncia) {
        $idDenuncia = $denuncia['d_id'];
        $idPublicacion = $denuncia['d_fk_pu_id'];

        $sql = "SELECT * FROM publicacion WHERE pu_id= $idPublicacion";
        $stmt = $pdo->query($sql);
        $resultadoPublicacion = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultadoPublicacion as $publicacion) {
?>
            <div class="card" id="card-<?php echo $denuncia['d_id']; ?>">
  <div class="card-header">
    <?php echo $idPublicacion; ?>
  </div>
  <div class="card-body">
    <img src="<?php echo $publicacion['pu_foto']; ?>" alt="..." style="width: 150px; height: 150px; object-fit: cover;">
    <h5 class="card-title">Tags: <?php echo $denuncia['d_tag']; ?></h5>
    <p class="card-text">Descripción: <?php echo $denuncia['d_comentario']; ?></p>
    
    <a href="#" class="btn btn-danger" onclick="gestionarDenuncia('eliminar', <?php echo $denuncia['d_id']; ?>)">Eliminar publicacion</a>
    <a href="#" class="btn btn-outline-info" onclick="gestionarDenuncia('descartar', <?php echo $denuncia['d_id']; ?>)">Descartar denuncia</a>
  </div>
</div>


<?php
        }
    }
} else {
    echo "<br><br><br><h2 align='center'>No hay denuncias</h2><br><br><br>";
}



  /*  $totalPublicaciones = $pdo->query("SELECT COUNT(*) FROM publicacion")->fetchColumn();
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
    echo "</ul></nav></div>";*/
?>
<script>
function gestionarDenuncia(accion, denunciaId) {
    // Crear el objeto que se enviará al servidor
    const data = {
        accion: accion,      // 'eliminar' o 'descartar'
        denunciaId: denunciaId
    };

    // Enviar la solicitud AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'gestionarDenuncia.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        console.log('Estado de la solicitud:', xhr.status);
        console.log('Respuesta del servidor:', xhr.responseText); // Ver respuesta del servidor
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);

            if (response.success) {
                // Si la operación fue exitosa, ocultar la tarjeta
                const tarjeta = document.getElementById('card-' + denunciaId);
                if (accion === 'eliminar') {
                    // Aquí puedes añadir alguna validación de que el estado es 'gestionada' si es necesario
                    tarjeta.style.display = 'none';
                } else if (accion === 'descartar') {
                    // Si es descartada, puedes dejar la tarjeta visible si es necesario
                    tarjeta.style.display = 'none';  // O puedes hacer algo diferente si lo prefieres
                }
            } else {
                alert('Ocurrió un error al gestionar la denuncia: ' + (response.message || 'Desconocido'));
            }
        } else {
            alert('Hubo un problema con la solicitud.');
        }
    };
    xhr.send(JSON.stringify(data));
}


</script>