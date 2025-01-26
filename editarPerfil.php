
<?php
include('cabecera.php');
include('header.php');

if (isset($_SESSION['user_id'])) {
    $usuario_id = $_SESSION['user_id'];
    
    // obtener datos del usuario desde la base de datos con su avatr
    $sql = "SELECT * FROM usuario,avatar
where u_avatar=a_id
and u_id= :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "<p>Error al encontrar el usuario. <a href='index.php'>Regresar</a></p>";
        exit();
    }
?>

<div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
<form action="editarPerfil_logica.php" method="POST" enctype="multipart/form-data" id="formEditarPerfil" class="p-4 rounded" style="background-color: #fff;">


  
  <!-- Foto de Perfil -->
  <div class="mb-3">
      <label for="avatar" class="form-label">Foto de Perfil</label>
      <div>
          <img id="avatarSeleccionado" src="<?php echo htmlspecialchars($usuario['a_url'] ?? 'Imagenes/avatarUsuario.png'); ?>" alt="Avatar Seleccionado" class="img-thumbnail" width="180" height="180">
          
          <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#avatarModal">
              Cambiar Avatar
          </button>
      </div>
  </div>


  <!-- Modal para seleccionar avatar -->
  <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="avatarModalLabel">Selecciona tu Avatar</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <?php
                      // para mostrar todos los avatares disponibles
                      $sqlAvatares = "SELECT a_id, a_url FROM avatar";
                      $stmtAvatares = $pdo->prepare($sqlAvatares);
                      $stmtAvatares->execute();

                      $avatares = $stmtAvatares->fetchAll(PDO::FETCH_ASSOC);
                      foreach ($avatares as $avatar) {
                          echo "
                          <div class='col-4'>
                              <img src='{$avatar['a_url']}' alt='Avatar {$avatar['a_id']}' class='img-thumbnail avatar-opcion rounded-circle' width='100' height='100' data-id='{$avatar['a_id']}' onclick='seleccionarAvatar(this)'>
                          </div>
                          ";
                      }
                      ?>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
          </div>
      </div>
  </div>
  
  <!-- Nombre de Usuario -->
  <div class="mb-3">
    <label for="username" class="form-label">Nombre de Usuario</label>
    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($usuario['u_userName']); ?>" required>
  </div>

  <!-- Nombre  -->
  <div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['u_nombre']); ?>" required pattern="[A-Za-záéíóúÁÉÍÓÚÑñ\s]{3,}" title="El nombre debe tener al menos 3 letras y no contener números.">
  </div>

  <!-- Apellido  -->
  <div class="mb-3">
    <label for="apellido" class="form-label">Apellido</label>
    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['u_apellido']); ?>" required pattern="[A-Za-záéíóúÁÉÍÓÚÑñ\s]{3,}" title="El apellido debe tener al menos 3 letras y no contener números.">
  </div>

  <!-- Correo Electrónico -->
  <div class="mb-3">
    <label for="email" class="form-label">Correo Electrónico</label>
    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['u_email']); ?>" required>
    <div id="emailHelp" class="form-text">No compartiremos tu correo con nadie más.</div>
  </div>

  <!-- Teléfono -->
  <div class="mb-3">
    <label for="telefono" class="form-label">Teléfono</label>
    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['u_telefono']); ?>" required pattern="[0-9]{9,}" title="El teléfono debe tener al menos 9 dígitos y no puede ser negativo.">
  </div>

  <!-- Dirección -->
  <div class="mb-3">
    <label for="direccion" class="form-label">Dirección</label>
    <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['u_domicilio']); ?>" required>
  </div>

  <?php
  
  $sqlVehiculos = "SELECT * FROM vehiculo WHERE v_fk_u_id = :user_id";
$stmtVehiculos = $pdo->prepare($sqlVehiculos);
$stmtVehiculos->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);
$stmtVehiculos->execute();
$vehiculos = $stmtVehiculos->fetchAll(PDO::FETCH_ASSOC);
  
  
  ?>




  
<!-- Vehículo -->
<div class="mb-3">
  <label for="cantidadVehiculos" class="form-label">¿Cuántos vehículos posee?</label>
  <select id="cantidadVehiculos" class="form-control" name="cantidadVehiculos" onchange="mostrarCamposVehiculo()">
    <option value="0" <?php echo empty($vehiculos) ? 'selected' : ''; ?>>0</option>
    <option value="1" <?php echo count($vehiculos) === 1 ? 'selected' : ''; ?>>1</option>
    <option value="2" <?php echo count($vehiculos) === 2 ? 'selected' : ''; ?>>2</option>
  </select>
</div>

<!-- Contenedor para los campos de vehículos -->
<div id="campos-vehiculo" class="mb-3" style="display: <?php echo !empty($vehiculos) ? 'block' : 'none'; ?>;">
  <!-- Campos para Vehículo 1 -->
  <div id="vehiculo1" class="vehiculo" style="display: <?php echo isset($vehiculos[0]) ? 'block' : 'none'; ?>;">
    <h4>Vehículo 1</h4>
    <label for="v1_modelo" class="form-label">Modelo:</label>
    <input type="text" class="form-control" id="v1_modelo" name="v1_modelo" placeholder="Ingrese el modelo del vehículo" value="<?php echo htmlspecialchars($vehiculos[0]['v_modelo'] ?? ''); ?>"><br>

    <label for="v1_patente" class="form-label">Patente:</label>
    <input type="text" class="form-control" id="v1_patente" name="v1_patente" placeholder="Ingrese la patente" value="<?php echo htmlspecialchars($vehiculos[0]['v_patente'] ?? ''); ?>"><br>

    <label class="form-label">Foto actual:</label>
                <div>
                    <img src="<?php echo htmlspecialchars($vehiculos[0]['v_foto']); ?>" alt="Foto del Vehículo" class="img-thumbnail" width="180" height="180">
                </div>
            
            
            <!-- Input para subir una nueva foto -->
            <label for="foto_<?php echo $index; ?>" class="form-label">Cambiar foto del vehículo (opcional):</label>
            <input type="file" class="form-control" id="foto_<?php echo $index; ?>" name="foto_<?php echo $index; ?>" accept="image/*"><br>
  </div>

  <!-- Campos para Vehículo 2 -->
  <div id="vehiculo2" class="vehiculo" style="display: <?php echo isset($vehiculos[1]) ? 'block' : 'none'; ?>;">
    <h4>Vehículo 2</h4>
    <label for="v2_modelo" class="form-label">Modelo:</label>
    <input type="text" class="form-control" id="v2_modelo" name="v2_modelo" placeholder="Ingrese el modelo del vehículo" value="<?php echo htmlspecialchars($vehiculos[1]['v_modelo'] ?? ''); ?>"><br>

    <label for="v2_patente" class="form-label">Patente:</label>
    <input type="text" class="form-control" id="v2_patente" name="v2_patente" placeholder="Ingrese la patente" value="<?php echo htmlspecialchars($vehiculos[1]['v_patente'] ?? ''); ?>"><br>

    <label for="v2_foto" class="form-label">Foto del vehículo:</label>
    <input type="file" class="form-control" id="v2_foto" name="v2_foto" accept="image/*"><br>
  </div>
</div>




  <!-- Input oculto para enviar el avatar seleccionado -->
  <input type="hidden" id="avatarSeleccionadoInput" name="avatarSeleccionado" value="<?php echo htmlspecialchars($usuario['u_avatar']); ?>">


  <!-- Apartado enviar/cancelar -->
  <div class="modal-footer">
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btnConfirmar ms-2">Guardar Cambios</button>
</div>

  

</form>
</div>

<?php
} else {
  echo "<p>No estás conectado. Por favor inicia sesión.</p>";
}
?>


<script>
function seleccionarAvatar(element) {
    const avatarUrl = element.src;
    const avatarId = element.getAttribute('data-id');

    document.getElementById('avatarSeleccionado').src = avatarUrl;
    document.getElementById('avatarSeleccionadoInput').value = avatarId;

    // Cerrar el modal
    const avatarModal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
    if (avatarModal) {
        avatarModal.hide();
    }
}

function mostrarCamposVehiculo() {
    const cantidadVehiculos = document.getElementById("cantidadVehiculos").value;
    const contenedorVehiculos = document.getElementById("campos-vehiculo");
    const vehiculo1 = document.getElementById("vehiculo1");
    const vehiculo2 = document.getElementById("vehiculo2");

    // Ocultar todos los campos por defecto
    contenedorVehiculos.style.display = 'none';
    vehiculo1.style.display = 'none';
    vehiculo2.style.display = 'none';

    // Mostrar campos según la cantidad de vehículos seleccionada
    if (cantidadVehiculos === "1") {
      contenedorVehiculos.style.display = 'block';
      vehiculo1.style.display = 'block';
    } else if (cantidadVehiculos === "2") {
      contenedorVehiculos.style.display = 'block';
      vehiculo1.style.display = 'block';
      vehiculo2.style.display = 'block';
    }
  }




</script>
</body>
</html>









