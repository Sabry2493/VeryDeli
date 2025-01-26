<?php
include 'cabecera.php';
include('header.php');
?>

<div class="container">
    <nav class="nav nav-tabs mt-2">
      <a class="nav-link active" href="ventanaAdminDenuncias.php">Denuncias</a>
      <a class="nav-link active" href="#">Verificar perfiles</a>
    </nav>
</div>

<?php
// Consulta las verificaciones pendientes
$sql = "SELECT * FROM verificaciones WHERE ve_estado = 'pendiente'";
$stmt = $pdo->query($sql);
$resultadoVerificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($resultadoVerificaciones) > 0) {
    foreach ($resultadoVerificaciones as $verificacion) {
        $idUsuario = $verificacion['ve_user_id'];

        // Obtener los detalles del usuario
        $sql = "SELECT u.*, a.a_url FROM usuario u 
                LEFT JOIN avatar a ON u.u_avatar = a.a_id 
                WHERE u.u_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
        <div class="card" id="card-<?php echo $verificacion['ve_id']; ?>">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <img src="<?php echo $usuario['a_url']; ?>" alt="Perfil" class="rounded-circle img-fluid" style="width: 50px; height: 50px;">
                    <a href="verPerfil.php?perfil=<?php echo $idUsuario?>" class="ms-3"><?php echo $usuario['u_userName']; ?></a>
                </div>
            </div>

            <div class="card-body">
                <!-- Imágenes de verificación con espaciado -->
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $verificacion['ve_foto_rostro']; ?>" alt="Rostro" class="rounded img-fluid me-3" style="width: 150px; height: 150px;" onclick="verImagenEnGrande(this)">
                    <img src="<?php echo $verificacion['ve_foto_dni']; ?>" alt="Identificación" class="rounded img-fluid" style="width: 150px; height: 150px;" onclick="verImagenEnGrande(this)">
                </div>

                <p class="card-text"><?php echo $verificacion['ve_fecha_solicitud']?></p>

                <!-- Contenedor de botones -->
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-danger me-2" onclick="gestionarVerificacion('verificar', <?php echo $verificacion['ve_id']; ?>)">Verificar perfil</a>
                    <a href="#" class="btn btn-outline-info" onclick="gestionarVerificacion('descartar', <?php echo $verificacion['ve_id']; ?>)">Descartar</a>
                </div>
            </div>
        </div>
    <?php
    }
} else {
    echo "<br><br><br><h2 align='center'>No hay verificaciones pendientes</h2><br><br><br>";
}
?>

<!-- Modal para ver imágenes en grande -->
<div class="modal fade" id="modalImagen" tabindex="-1" role="dialog" aria-labelledby="tituloModalImagen" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalImagen">Vista en Grande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagenGrande" src="" alt="Imagen en grande" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function verImagenEnGrande(imagen) {
    const urlImagen = imagen.src;
    document.getElementById("imagenGrande").src = urlImagen;
    $("#modalImagen").modal("show");
}

function gestionarVerificacion(accion, verificacionId) {
    const data = {
        accion: accion,  // 'verificar' o 'descartar'
        verificacionId: verificacionId
    };

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'gestionarVerificacion.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Si la operación fue exitosa, ocultar la tarjeta
                const tarjeta = document.getElementById('card-' + verificacionId);
                tarjeta.style.display = 'none';
            } else {
                alert('Ocurrió un error al gestionar la verificación: ' + (response.message || 'Desconocido'));
            }
        } else {
            alert('Hubo un problema con la solicitud.');
        }
    };
    xhr.send(JSON.stringify(data));
}
</script>
