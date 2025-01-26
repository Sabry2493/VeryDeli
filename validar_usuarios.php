<?php
include 'conexion.php';

// Obtener lista de documentos no validados
$sql = "SELECT d_id, d_usuario_id, d_ruta, d_tipo, d_fecha_subida FROM documentos WHERE validado = 0";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
  <title>Validación de Usuarios</title>
</head>
<body>
<div class="container mt-5">
  <h2>Validar Documentos</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID Documento</th>
        <th>ID Usuario</th>
        <th>Ruta</th>
        <th>Tipo</th>
        <th>Fecha de Subida</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?php echo $row['d_id']; ?></td>
          <td><?php echo $row['d_usuario_id']; ?></td>
          <td><?php echo $row['d_ruta']; ?></td>
          <td><?php echo $row['d_tipo']; ?></td>
          <td><?php echo $row['d_fecha_subida']; ?></td>
          <td>
            <a href="validar_documento.php?id=<?php echo $row['d_id']; ?>" class="btn btn-success">Validar</a>
            <!-- Botón para abrir el modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#imageModal" 
              data-image="<?php echo $row['d_ruta']; ?>">Ver Imagen</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Imagen del Documento</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="modalImage" class="img-fluid" alt="Documento">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
  // Script para cargar la imagen en el modal
  $('#imageModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Botón que activó el modal
    var imageSrc = button.data('image'); // Obtener la ruta de la imagen

    var modal = $(this);
    modal.find('#modalImage').attr('src', imageSrc);
  });
</script>
</body>
</html>
