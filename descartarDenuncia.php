<?php
    session_start();
    include 'coneccion/conexionPDO.php';

    try {
        // Usar sentencias preparadas para evitar inyección SQL
        $sql = "SELECT de_tags FROM denuncias WHERE de_id = :denuncia_id";
        $stmt = $pdo->prepare($sql);
        $denuncia_id = $_GET['denuncia'];
        $stmt->execute([':denuncia_id' => $denuncia_id]);  // Ejecutar con el parámetro

        $resultadoDenuncias = $stmt->fetchAll(PDO::FETCH_NUM);  // Obtener los resultados
        $tag = null;
        if (count($resultadoDenuncias) > 0) {
            foreach ($resultadoDenuncias as $row) {
                $tag = $row[0];
            }
        }

        if ($tag !== null) {
            $sql = "UPDATE denuncias SET de_estado = :estado WHERE de_fk_pu_id = :denuncia_pub AND de_tags = :denuncia_tags";
            $stmt = $pdo->prepare($sql);

            // Definir los valores de los parámetros
            $denuncia_pub = $_GET['publicacion'];

            // Ejecutar la consulta
            if ($stmt->execute([':denuncia_tags' => $tag, ':denuncia_pub' => $denuncia_pub, ':estado' => 'descartada'])) {
                // Si se ejecuta con éxito, redirige
                header('Location: ventanaAdminDenuncias.php');
                exit();
            } else {
                echo "Hubo un problema al descartar la denuncia";
            }
        } else {
            echo "No se encontró la denuncia especificada.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        // Cerrar la conexión
        include 'coneccion/cerrarConexionPDO.php';
    }

?>