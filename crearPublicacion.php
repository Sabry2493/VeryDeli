<?php
    include('cabecera.php');
    if(!isset($_SESSION['user_id'])){
        header('Location: loguin_formulario.php');
    }
?>
<h2 align="center">Crear Publicacion</h2>
  
<div class="container form-container">
    
    
    <form class="row g-3" action="subirPublicacion.php" method="post" enctype="multipart/form-data">

        <div class="col-10">
            <label for="titulo" class="form-label">Titulo</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>

        <div class="col-10">
            <label for="descripcion" class="form-label">Descripcion</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea >
        </div>

        <div class="col-md-5">
            <label for="peso" class="form-label">Peso en kg</label>
            <input type="number" class="form-control" id="peso" name="peso" required>
        </div>

        <div class="col-md-5">
            <label for="volumen" class="form-label">Volumen (cm³)</label>
            <input type="number" class="form-control" id="volumen" name="volumen" required>
        </div>
        <br>
        <div class="col-10">
            <label for="imagenPaquete" class="form-label">Subir una imagen</label>
            <input type="file" class="form-control" id="imagenPaquete" name="imagenPaquete" >
            
        </div>


        <div class="col-10">
            <label for="nombreContactoDestino" class="form-label">Nombre destinatario</label>
            <input type="text" class="form-control" id="nombreContactoDestino" name="nombreContactoDestino" >
        </div>

        <div class="col-10">
            <label for="contactoDestino" class="form-label">Contacto destino</label>
            <input type="number" class="form-control" id="contactoDestino" name="contactoDestino" >
        </div>
        
        <!-- Origen -->
        <h2>Origen</h2>
        <div class="col-md-5">
            <label for="provinciaOrigen" class="form-label">Provincia</label>
            <select id="provinciaOrigen" name="provinciaOrigen" class="form-select" required>
                <?php
               
                $sql = "SELECT * FROM argentina";
                $stmt = $pdo->query($sql);
                $resultado = $stmt->fetchAll(PDO::FETCH_NUM);

                if ($resultado) {
                foreach ($resultado as $row) {
                    echo "<option value='".$row[0]."'>".$row[1]."</option>";
                }
                }
            ?>
            </select>
        </div>

        <div class="col-md-5">
            <label for="ciudadOrigen" class="form-label">Ciudad</label>
            <input type="text" class="form-control" id="ciudadOrigen" name="ciudadOrigen" required>
        </div>

        <div class="col-10">
            <label for="direccionOrigen" class="form-label">Direccion</label>
            <input type="text" class="form-control" id="direccionOrigen" name="direccionOrigen" required>
        </div>

        <!-- Destino -->
        <br>
        <h2>Destino</h2>
        <br>
        <div class="col-md-5">
            <label for="provinciaDestino" class="form-label">Provincia</label>
            <select id="provinciaDestino" name="provinciaDestino" class="form-select" required>
                <?php
                        $sql = "SELECT * FROM argentina";
                        $stmt = $pdo->query($sql);
                        $resultado = $stmt->fetchAll(PDO::FETCH_NUM);

                        if ($resultado) {
                        foreach ($resultado as $row) {
                            echo "<option value='".$row[0]."'>".$row[1]."</option>";
                        }
                        }
                        
                ?>
            </select>
        </div>

        <div class="col-md-5">
            <label for="ciudadDestino" class="form-label">Ciudad</label>
            <input type="text" class="form-control" id="ciudadDestino" name="ciudadDestino"  required>
        </div>

        <div class="col-10">
            <label for="direccionDestino" class="form-label">Direccion</label>
            <input type="text" class="form-control" id="direccionDestino" name="direccionDestino" required>
        </div>
        <br>
        <div class="col-10">
            <button type="submit" class="btn btn-primary" id="btnPublicar" name="btnPublicar">Publicar</button>
        </div>
    </form>
</div>
<?php
    include 'pie.php'
?>