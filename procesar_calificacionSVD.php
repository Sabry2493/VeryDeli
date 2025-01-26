<?php
session_start();


$host = 'localhost';
$dbname = 'verydeli_verydeli';
$username = 'verydeli_tecnicaturaRedes'; 
$password = 'verydel11';

//para arrojar los errores por pantalla y ver que esta fallando
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa.<br>";
}



// Verificar si se enviaron los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
// Obtener la calificación y el comentario desde el formulario
    
    // Capturar si se presionó el botón "Omitir"
    $calificacion = isset($_POST['omitir']) ? 0 : (int)$_POST['rating'];
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
    $id_solicitante = isset($_POST['id_solicitante']) ? trim($_POST['id_solicitante']) : '';
    $id_postulante = $_SESSION['user_id'];
    // Obtener el ID del envío
    $id_envio = isset($_POST['id_envio']) ? (int)$_POST['id_envio'] : 0;

    // Debugging: Imprimir el ID de envío recibido
   

    // Verificar si el ID del envío es válido
    if ($id_envio <= 0) {
        die("ID de envío no válido.");
    }

    // Verificar si se ingresó una calificación válida
    if ($calificacion < 0 || $calificacion > 5) {
        die("Calificación no válida.");
    }
    
    //-------INICIO de Cambio
    // **Verificar si ya existe una calificación para este id_envio y id_solicitante**
    $sql_verificar = "SELECT * FROM calificacion WHERE ca_id_envio = ? AND ca_calificado = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $id_envio, $id_solicitante);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    
  
    
    if ($result_verificar->num_rows > 0) {
        // Si existe, actualizamos la calificación y el comentario
        $sql_updateC = "UPDATE calificacion SET ca_puntaje = ?, ca_comentario = ? WHERE ca_id_envio = ? AND ca_calificado = ?";
        $stmt_updateC = $conn->prepare($sql_updateC);
        $stmt_updateC->bind_param("isii", $calificacion, $comentario, $id_envio, $id_solicitante);

        if ($stmt_updateC->execute()) {
            echo "Calificación actualizada correctamente.";
            //------codigo de calculos
            
            // **Obtener las últimas calificaciones del solicitante**
            $sql_calificaciones = "
                SELECT ca_puntaje 
                FROM calificacion  
                WHERE ca_calificado = ? AND ca_puntaje > 0  /* -- Solo calificaciones mayores que 0-- */
                ORDER BY ca_fecha DESC 
                LIMIT 5";
            $stmt_calificaciones = $conn->prepare($sql_calificaciones);
            $stmt_calificaciones->bind_param("i", $id_solicitante);
            $stmt_calificaciones->execute();
            
            $result_calificaciones = $stmt_calificaciones->get_result();//toma los resultados de la consulta(get_result)
    
            $calificaciones = [];
            while ($row = $result_calificaciones->fetch_assoc()) {
                $calificaciones[] = $row['ca_puntaje'];
                
            }
    
        // Calcular promedio de calificaciones del solicitante
            // Asignar el id_solicitante a id_usuario desde el principio
            $id_usuario = $id_solicitante; 
    
            // **Paso 1: Verificar 3 últimas calificaciones consecutivas con promedio < 40%**
            if (count($calificaciones) >= 3) {
                $ultimas_tres = array_slice($calificaciones, 0, 3); // Esto ya toma las más recientes
                $promedio_ultimas_3 = (array_sum($ultimas_tres) / count($ultimas_tres)) * 20;
                
                if ($promedio_ultimas_3 < 40) {
                    $responsable = 0;
                } else {
                    // **Paso 2: Verificar si cumple con 5 últimas calificaciones > 80%**
                    if (count($calificaciones) == 5) {
                        $promedio_ultimas_5 = (array_sum($calificaciones) / 5) * 20;
                        $responsable = $promedio_ultimas_5 >= 80 ? 1 : 0;
                    } else {
                        // **Paso 3: Si tiene entre 3-4 calificaciones y promedio > 80%**
                        $promedio = (array_sum($calificaciones) / count($calificaciones)) * 20;
                        $responsable = $promedio >= 80 ? 1 : 0;
                    }
                }
            } else {
                // Si tiene menos de 3 calificaciones, no es responsable
                $responsable = 0;
            }
    
            // **Actualizar estado de responsabilidad del usuario**
            //var_dump($id_usuario);
            $sql_update = "UPDATE usuario SET u_responsable = ? WHERE u_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $responsable, $id_usuario);
            if ($stmt_update->execute()) {
                echo "Estado de responsabilidad actualizado correctamente.";
            } else {
                echo "Error al actualizar el estado de responsabilidad: " . $stmt_update->error;
            }
    
    
            // **Aquí comienza el código para penalizar calificaciones faltantes**
    
            // Fecha límite para agregar calificaciones (hace 1 semana)
            $fecha_limite = date('Y-m-d H:i:s', strtotime('-1 week'));
    
            // Consulta para obtener los id_solicitante de envios sin calificación
            $sql_penalizar = "
                SELECT e.env_id_envio, c.ca_calificado 
                FROM envio e 
                LEFT JOIN calificacion c ON e.env_id_envio = c.ca_id_envio
                WHERE e.env_fecha_envio <= ? AND (c.ca_puntaje = 0 AND c.ca_contado_negativo = 0)
            ";// Solo contabiliza las que no han sido contabilizadas 
    
            //WHERE e.fecha_envio <= ? AND c.id_envio IS NULL
            $stmt_penalizar = $conn->prepare($sql_penalizar);
            $stmt_penalizar->bind_param("s", $fecha_limite);
            $stmt_penalizar->execute();
            $resultado_penalizar = $stmt_penalizar->get_result();
    
            if ($resultado_penalizar->num_rows > 0) {
                while ($row = $resultado_penalizar->fetch_assoc()) {
                    // Obtener id_solicitante del resultado
                    /* $id_solicitante = $row['env_id_solicitante'];
                    echo "Penalizando al solicitante con ID: $id_solicitante<br>";
                      */
                    // Obtener id_envio y id_solicitante del resultado
                    $id_envio = $row['env_id_envio'];
                    $id_solicitante = $row['ca_calificado'];
                    echo "Penalizando al solicitante con ID: $id_solicitante<br>";
                    //-----------------------------------------------------------
                    // **Actualizar contado_negativo en calificacion para la calificación de 0**
                     $sql_actualizar_contado = "
                        UPDATE calificacion 
                        SET ca_contado_negativo = 1 
                        WHERE ca_id_envio = ? AND (ca_puntaje = 0 AND ca_contado_negativo = 0)
                    ";
                    $stmt_actualizar_contado = $conn->prepare($sql_actualizar_contado);
                    $stmt_actualizar_contado->bind_param("i", $id_envio);
                    $stmt_actualizar_contado->execute();
                    //------------------------------------------------------------------
    
                    // **Asignar el id_solicitante a id_usuario**
                    $id_usuario = $id_solicitante; // Asignar directamente aquí
    
                    // Actualizar las calificaciones negativas para el usuario encontrado
                    $sql_update_negativas = "
                        UPDATE usuario 
                        SET u_calificaciones_negativas = u_calificaciones_negativas + 1 
                        WHERE u_id = ?
                    ";
                    $stmt_update_negativas = $conn->prepare($sql_update_negativas);
                    $stmt_update_negativas->bind_param("i", $id_usuario);
                    $stmt_update_negativas->execute();
    
                    
                    // Verificar si el usuario pierde la responsabilidad
                    $sql_verificar_responsable = "
                        SELECT u_calificaciones_negativas 
                        FROM usuario 
                        WHERE u_id = ?
                    ";
                    $stmt_verificar = $conn->prepare($sql_verificar_responsable);
                    $stmt_verificar->bind_param("i", $id_usuario);
                    $stmt_verificar->execute();
                    $res_negativas = $stmt_verificar->get_result()->fetch_assoc();
    
                    if ($res_negativas['u_calificaciones_negativas'] >= 2) {
                        $sql_perder_responsabilidad = "
                            UPDATE usuario 
                            SET u_responsable = 0 
                            WHERE u_id = ?
                        ";
                        $stmt_perder_responsabilidad = $conn->prepare($sql_perder_responsabilidad);
                        $stmt_perder_responsabilidad->bind_param("i", $id_usuario);
                        $stmt_perder_responsabilidad->execute();
                    }
                }
            } else {
                echo "No se encontraron envíos sin calificación.";
            }
            
            //------------fin calculos
            header('Location: index.php');
        } else {
            echo "Error al actualizar la calificación: " . $stmt_update->error;
        }
    } else {
    //-------FIN de cambio
    

    // Insertamos los datos en la base de datos
    $sql = "INSERT INTO calificacion (ca_id_envio, ca_puntaje, ca_comentario, ca_calificado, ca_califica) VALUES (?, ?, ?, ?, ?)";
    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iisii", $id_envio, $calificacion, $comentario, $id_solicitante, $id_postulante);
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "Calificación guardada correctamente.";

        //inicio de calculos
        // **Obtener las últimas calificaciones del solicitante**
        $sql_calificaciones = "
            SELECT ca_puntaje 
            FROM calificacion  
            WHERE ca_calificado = ? AND ca_puntaje > 0  /* -- Solo calificaciones mayores que 0-- */
            ORDER BY ca_fecha DESC 
            LIMIT 5";
        $stmt_calificaciones = $conn->prepare($sql_calificaciones);
        $stmt_calificaciones->bind_param("i", $id_solicitante);
        $stmt_calificaciones->execute();
        
        $result_calificaciones = $stmt_calificaciones->get_result();//toma los resultados de la consulta(get_result)

        $calificaciones = [];
        while ($row = $result_calificaciones->fetch_assoc()) {
            $calificaciones[] = $row['ca_puntaje'];
            
        }

    // Calcular promedio de calificaciones del solicitante
        // Asignar el id_solicitante a id_usuario desde el principio
        $id_usuario = $id_solicitante; 

        // **Paso 1: Verificar 3 últimas calificaciones consecutivas con promedio < 40%**
        if (count($calificaciones) >= 3) {
            $ultimas_tres = array_slice($calificaciones, 0, 3); // Esto ya toma las más recientes
            $promedio_ultimas_3 = (array_sum($ultimas_tres) / count($ultimas_tres)) * 20;
            
            if ($promedio_ultimas_3 < 40) {
                $responsable = 0;
            } else {
                // **Paso 2: Verificar si cumple con 5 últimas calificaciones > 80%**
                if (count($calificaciones) == 5) {
                    $promedio_ultimas_5 = (array_sum($calificaciones) / 5) * 20;
                    $responsable = $promedio_ultimas_5 >= 80 ? 1 : 0;
                } else {
                    // **Paso 3: Si tiene entre 3-4 calificaciones y promedio > 80%**
                    $promedio = (array_sum($calificaciones) / count($calificaciones)) * 20;
                    $responsable = $promedio >= 80 ? 1 : 0;
                }
            }
        } else {
            // Si tiene menos de 3 calificaciones, no es responsable
            $responsable = 0;
        }

        // **Actualizar estado de responsabilidad del usuario**
        //var_dump($id_usuario);
        $sql_update = "UPDATE usuario SET u_responsable = ? WHERE u_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $responsable, $id_usuario);
        if ($stmt_update->execute()) {
            echo "Estado de responsabilidad actualizado correctamente.";
        } else {
            echo "Error al actualizar el estado de responsabilidad: " . $stmt_update->error;
        }


        // **Aquí comienza el código para penalizar calificaciones faltantes**

        // Fecha límite para agregar calificaciones (hace 1 semana)
        $fecha_limite = date('Y-m-d H:i:s', strtotime('-1 week'));

        // Consulta para obtener los id_solicitante de envios sin calificación
        $sql_penalizar = "
            SELECT e.env_id_envio, c.ca_calificado 
            FROM envio e 
            LEFT JOIN calificacion c ON e.env_id_envio = c.ca_id_envio
            WHERE e.env_fecha_envio <= ? AND (c.ca_puntaje = 0 AND c.ca_contado_negativo = 0)
        ";// Solo contabiliza las que no han sido contabilizadas 

        //WHERE e.fecha_envio <= ? AND c.id_envio IS NULL
        $stmt_penalizar = $conn->prepare($sql_penalizar);
        $stmt_penalizar->bind_param("s", $fecha_limite);
        $stmt_penalizar->execute();
        $resultado_penalizar = $stmt_penalizar->get_result();

        if ($resultado_penalizar->num_rows > 0) {
            while ($row = $resultado_penalizar->fetch_assoc()) {
                // Obtener id_solicitante del resultado
                /* $id_solicitante = $row['env_id_solicitante'];
                echo "Penalizando al solicitante con ID: $id_solicitante<br>";
                  */
                // Obtener id_envio y id_solicitante del resultado
                $id_envio = $row['env_id_envio'];
                $id_solicitante = $row['ca_calificado'];
                echo "Penalizando al solicitante con ID: $id_solicitante<br>";
                //-----------------------------------------------------------
                // **Actualizar contado_negativo en calificacion para la calificación de 0**
                 $sql_actualizar_contado = "
                    UPDATE calificacion 
                    SET ca_contado_negativo = 1 
                    WHERE ca_id_envio = ? AND (ca_puntaje = 0 AND ca_contado_negativo = 0)
                ";
                $stmt_actualizar_contado = $conn->prepare($sql_actualizar_contado);
                $stmt_actualizar_contado->bind_param("i", $id_envio);
                $stmt_actualizar_contado->execute();
                //------------------------------------------------------------------

                // **Asignar el id_solicitante a id_usuario**
                $id_usuario = $id_solicitante; // Asignar directamente aquí

                // Actualizar las calificaciones negativas para el usuario encontrado
                $sql_update_negativas = "
                    UPDATE usuario 
                    SET u_calificaciones_negativas = u_calificaciones_negativas + 1 
                    WHERE u_id = ?
                ";
                $stmt_update_negativas = $conn->prepare($sql_update_negativas);
                $stmt_update_negativas->bind_param("i", $id_usuario);
                $stmt_update_negativas->execute();

                
                // Verificar si el usuario pierde la responsabilidad
                $sql_verificar_responsable = "
                    SELECT u_calificaciones_negativas 
                    FROM usuario 
                    WHERE u_id = ?
                ";
                $stmt_verificar = $conn->prepare($sql_verificar_responsable);
                $stmt_verificar->bind_param("i", $id_usuario);
                $stmt_verificar->execute();
                $res_negativas = $stmt_verificar->get_result()->fetch_assoc();

                if ($res_negativas['u_calificaciones_negativas'] >= 2) {
                    $sql_perder_responsabilidad = "
                        UPDATE usuario 
                        SET u_responsable = 0 
                        WHERE u_id = ?
                    ";
                    $stmt_perder_responsabilidad = $conn->prepare($sql_perder_responsabilidad);
                    $stmt_perder_responsabilidad->bind_param("i", $id_usuario);
                    $stmt_perder_responsabilidad->execute();
                }
            }
        } else {
            echo "No se encontraron envíos sin calificación.";
        }
        //fin de calculos
        
        header('Location: index.php');
    
      } else {
            echo "Error al guardar la calificación: " . $conn->error;
      }
      
    }//cierra el else agregado en cambio
  
    // Cerrar la consulta
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>


<!--
La condición e.fecha_envio <= ? verifica que el envío se haya registrado antes de la fecha límite de una semana.
(c.calificacion IS NULL OR c.calificacion = 0) asegura que el envío aparezca si no tiene ninguna calificación o si 
fue calificado como 0 (cuando presionaste "Omitir").
-->
