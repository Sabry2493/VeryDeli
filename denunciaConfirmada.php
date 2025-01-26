<?php
    try {
        $idPub = $_GET['publicacion']; 
        $sql = "SELECT COUNT(*) AS cantidad FROM mi_tabla WHERE id = :id";

        $stmt = $pdo->prepare($sql);
    
        $stmt->execute([':id' => $idPub]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $cantidad = $resultado['cantidad'];
        if($cantidad>=1){
            try{
                $sql = "UPDATE denuncias SET de_estado = :estado WHERE de_fk_pu_id = :denuncia_pub";
                $stmt = $pdo->prepare($sql);
            
                // Definir los valores de los parámetros
            
                // Ejecutar la consulta
                if ($stmt->execute([':denuncia_tags' => $tag, ':denuncia_id' => $idPub,':estado'=>'confirmada'])) {
                    // Si se ejecuta con éxito, redirige
            
                } else {
                    echo "Hubo un problema al descartar la denuncia";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } catch (PDOException $e) {
        // Manejo de errores en caso de problemas de conexión o consulta
        echo "Error: " . $e->getMessage();
    }

?>