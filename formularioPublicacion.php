<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Publicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .form-container {
 
            background-color: darkgrey;
        }
    </style>
</head>
  <body>
  <h2 align="center">Crear Publicacion</h2>
  
<div class="container form-container">
    
    
    <form class="row g-3" action="crearPublicacion.php" method="post" enctype="multipart/form-data">

        <div class="col-10">
            <label for="titulo" class="form-label">Titulo</label>
            <input type="text" class="form-control" id="titulo" name="titulo">
        </div>

        <div class="col-10">
            <label for="descripcion" class="form-label">Descripcion</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>

        <div class="col-md-5">
            <label for="peso" class="form-label">Peso en kg</label>
            <input type="number" class="form-control" id="peso" name="peso">
        </div>

        <div class="col-md-5">
            <label for="volumen" class="form-label">Volumen (cm³)</label>
            <input type="number" class="form-control" id="volumen" name="volumen">
        </div>
        <br>
        <div class="col-10">
            <label for="imagenPaquete" class="form-label">Subir una imagen</label>
            <input type="file" class="form-control" id="imagenPaquete" name="imagenPaquete">
            
        </div>

        <div class="col-10">
            <label for="contactoDestino" class="form-label">Contacto destino</label>
            <input type="text" class="form-control" id="contactoDestino" name="contactoDestino">
        </div>
        
        <!-- Origen -->
        <h2>Origen</h2>
        <div class="col-md-5">
            <label for="provinciaOrigen" class="form-label">Provincia</label>
            <select id="provinciaOrigen" name="provinciaOrigen" class="form-select">
                <?php
                include 'coneccion/conexionPDO.php';
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
            <input type="text" class="form-control" id="ciudadOrigen" name="ciudadOrigen">
        </div>

        <div class="col-10">
            <label for="direccionOrigen" class="form-label">Direccion</label>
            <input type="text" class="form-control" id="direccionOrigen" name="direccionOrigen">
        </div>

        <!-- Destino -->
        <br>
        <h2>Destino</h2>
        <br>
        <div class="col-md-5">
            <label for="provinciaDestino" class="form-label">Provincia</label>
            <select id="provinciaDestino" name="provinciaDestino" class="form-select">
                <?php
                        $sql = "SELECT * FROM argentina";
                        $stmt = $pdo->query($sql);
                        $resultado = $stmt->fetchAll(PDO::FETCH_NUM);

                        if ($resultado) {
                        foreach ($resultado as $row) {
                            echo "<option value='".$row[0]."'>".$row[1]."</option>";
                        }
                        }
                        include 'coneccion/cerrarConexionPDO.php';
                ?>
            </select>
        </div>

        <div class="col-md-5">
            <label for="ciudadDestino" class="form-label">Ciudad</label>
            <input type="text" class="form-control" id="ciudadDestino" name="ciudadDestino">
        </div>

        <div class="col-10">
            <label for="direccionDestino" class="form-label">Direccion</label>
            <input type="text" class="form-control" id="direccionDestino" name="direccionDestino">
        </div>
        <br>
        <div class="col-10">
            <button type="submit" class="btn btn-primary">Publicar</button>
        </div>
    </form>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>