<?php
require 'conexion.php'; 

if (isset($_GET['id'])) {
    $publicacion_id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM publicacion WHERE pu_id = :id");
        $stmt->bindParam(':id', $publicacion_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($publicacion) {
            
            ?>
            <div class="container">
                <h1>Detalles de la Publicación #<?php echo $publicacion['pu_id']; ?></h1>
                <img src="<?php echo $publicacion['pu_foto']; ?>" alt="Foto de Publicación">
                <p>Origen: <?php echo $publicacion['pu_fk_origen']; ?></p>
                <p>Destino: <?php echo $publicacion['pu_fk_destino']; ?></p>
                <p>Volumen: <?php echo $publicacion['pu_volumen']; ?></p>
                <p>Peso: <?php echo $publicacion['pu_peso']; ?></p>
                <p>Descripción: <?php echo $publicacion['pu_descripcion']; ?></p>
                
            </div>
            <?php
        } else {
            echo "No se encontró la publicación.";
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
} else {
    echo "ID de publicación no especificado.";
}
