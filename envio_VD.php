<?php
ob_start(); // Al inicio del archivo PHP

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

//session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "cabecera.php";

// Verificar que el usuario esté logueado como solicitante

if (!isset($_SESSION['user_id'])) {
    header("Location: loguin_formulario.php");
    exit;
}

$host = 'localhost';
$dbname = 'verydeli_verydeli';
$username = 'verydeli_tecnicaturaRedes'; 
$password = 'verydel11';

$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar que el id_publicacion esté presente en la URL
//id_publicaion=> publicacion_id y id_usuario=>user_id
if (isset($_GET['publicacion_id'])) {
    $id_publicacion = $_GET['publicacion_id'];
    $id_postulante = $_SESSION['user_id'];// Usuario logueado como postulante
    

    // Consulta para obtener detalles de la publicación
    $sql = "SELECT * FROM publicacion WHERE pu_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error en la consulta de la publicación: " . $conn->error);
    }

    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $publicacion = $result->fetch_assoc();
        
         // Consulta para obtener el estado del envío
        $stmt_estado = $conn->prepare("SELECT env_estado FROM envio WHERE env_id_publicacion = ?");
        $stmt_estado->bind_param('i', $id_publicacion);
        $stmt_estado->execute();
        $estado_result = $stmt_estado->get_result();
        
        // Determinar el estado del envío
        $estado_envio = '';
        if ($estado_result->num_rows > 0) {
            $estado_envio = $estado_result->fetch_assoc()['env_estado'];
        }
        
        /*$stmt_origen = $pdo->prepare("SELECT provincia FROM argentina WHERE arg_id = :origen_id");
        $stmt_origen->bindParam(':origen_id', $publicacion['pu_fk_origen_provincia'], PDO::PARAM_INT);
        $stmt_origen->execute();
        $origen = $stmt_origen->fetchColumn();
        
        $stmt_destino = $pdo->prepare("SELECT provincia FROM argentina WHERE arg_id = :destino_id");
        $stmt_destino->bindParam(':destino_id', $publicacion['pu_fk_destino_provincia'], PDO::PARAM_INT);
        $stmt_destino->execute();
        $destino = $stmt_destino->fetchColumn();
        
        $stmt_telefono = $pdo->prepare("SELECT u_telefono FROM usuario WHERE u_id = :u_id");
        $stmt_telefono->bindParam(':u_id', $publicacion['pu_fk_u_id'], PDO::PARAM_INT);
        $stmt_telefono->execute();
        $telefono = $stmt_telefono->fetchColumn();*/
        
        // *** INICIO DEL CAMBIO ***
        $stmt_origen = $conn->prepare("SELECT provincia FROM argentina WHERE arg_id = ?");
        $stmt_origen->bind_param('i', $publicacion['pu_fk_origen_provincia']);
        $stmt_origen->execute();
        $origen_result = $stmt_origen->get_result();
        $origen = $origen_result->fetch_assoc()['provincia'];

        $stmt_destino = $conn->prepare("SELECT provincia FROM argentina WHERE arg_id = ?");
        $stmt_destino->bind_param('i', $publicacion['pu_fk_destino_provincia']);
        $stmt_destino->execute();
        $destino_result = $stmt_destino->get_result();
        $destino = $destino_result->fetch_assoc()['provincia'];

        $stmt_telefono = $conn->prepare("SELECT u_telefono FROM usuario WHERE u_id = ?");
        $stmt_telefono->bind_param('i', $publicacion['pu_fk_u_id']);
        $stmt_telefono->execute();
        $telefono_result = $stmt_telefono->get_result();
        $telefono = $telefono_result->fetch_assoc()['u_telefono'];
        // *** FIN DEL CAMBIO ***
        ?>
        <div class="card">
            <h5 class="card-header">Detalles de la Publicación</h5>
            <div class="card-body">
                <p class="card-text">Origen: <?php echo htmlspecialchars($origen) . " - " . htmlspecialchars($publicacion['pu_fk_origen_ciudad']) . " - " . htmlspecialchars($publicacion['pu_fk_origen_direccion']); ?></p>
                <p class="card-text">Destino: <?php echo htmlspecialchars($destino) . " - " . htmlspecialchars($publicacion['pu_fk_destino_ciudad']) . " - " . htmlspecialchars($publicacion['pu_fk_destino_direccion']); ?></p>
                <p class="card-text">Descripción: <?php echo htmlspecialchars($publicacion['pu_descripcion']); ?></p>
                <p class="card-text">Contacto del remitente: <?php echo htmlspecialchars($telefono); ?></p>
                <p class="card-text">Destinatario: <?php echo htmlspecialchars($publicacion['pu_nombre_contacto']); ?></p>
                <p class="card-text">Número de contacto: <?php echo htmlspecialchars($publicacion['pu_contacto_destino']); ?></p>
                <p class="card-text">Volumen: <?php echo htmlspecialchars($publicacion['pu_volumen']); ?> cm³</p>
                <p class="card-text">Peso: <?php echo htmlspecialchars($publicacion['pu_peso']); ?> kg</p>

                <?php
                // Mostrar botones según el estado del envío
                if ($estado_envio == 'pendiente') {
                    // Si el estado es "retirado", mostrar el botón de "Entregado"
                    echo '<form action="" method="post">
                            <input type="hidden" name="id_publicacion" value="' . htmlspecialchars($id_publicacion) . '">
                            <input type="hidden" name="accion" value="entregado">
                            <input class="btn btn-outline-info" type="submit" value="Entregado">
                          </form>';
                } else {
                    // Si el estado no es "retirado", mostrar el botón de "Retirado"
                    echo '<form action="" method="post">
                            <input type="hidden" name="id_publicacion" value="' . htmlspecialchars($id_publicacion) . '">
                            <input type="hidden" name="accion" value="retirado">
                            <input class="btn btn-outline-info" type="submit" value="Retirado">
                          </form>';
                }
                ?>
            </div>
        </div>


        
     <?php   
    } else {
        echo "No se encontró la publicación.";
    }

    $stmt->close();
} else {
    echo "No se seleccionó ninguna publicación.";
    exit;
}

// Manejo de acciones cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_publicacion'])) {
    $id_publicacion = $_POST['id_publicacion'];
    $accion = $_POST['accion'];
    //$id_postulante = $_SESSION['user_id'];

    // Obtener el id del solicitante basado en el id_publicacion
    
    //$id_solicitante = obtenerIdSolicitante($id_publicacion, $conn);

    if ($accion === 'retirado') {
        // Guardar en la tabla envio como "pendiente"
        $sql = "INSERT INTO envio (env_fecha_envio, env_estado, env_id_publicacion)
                VALUES (NOW(), 'pendiente', ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta de retiro: " . $conn->error);
        }

        $stmt->bind_param("i", $id_publicacion);

        if ($stmt->execute()) {
            echo "<p>El pedido ha sido marcado como retirado (pendiente).</p>";
        } else {
            echo "Error al guardar el retiro: " . $conn->error;
        }
    } elseif ($accion === 'entregado') {
        // Mostrar los valores de id_publicacion, id_postulante, y estado
       //echo "Debug: id_publicacion = $id_publicacion, id_postulante = $id_postulante, estado = 'pendiente'<br>";
       
        // Cambiar estado a "entregado" en envio
        $sql = "UPDATE envio SET env_estado = 'entregado', env_fecha_envio = NOW() 
                WHERE env_id_publicacion = ? AND env_estado = 'pendiente'";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta de entrega: " . $conn->error);
        }
        
        // Vincular parámetros y ejecutar el UPDATE
        $stmt->bind_param("i", $id_publicacion);

        if ($stmt->execute()) {

            // Verificar si el UPDATE afectó alguna fila
            if ($stmt->affected_rows === 0) {
                die("Error: No se pudo actualizar el estado a 'entregado'. Verifica que el registro exista y tenga estado 'pendiente'.");
            }

            // Obtener el id_envio después de marcar como entregado
            $sql_envio = "SELECT env_id_envio FROM envio 
                          WHERE env_id_publicacion = ? AND env_estado = 'entregado'
                          ORDER BY env_fecha_envio DESC LIMIT 1";
            $stmt_envio = $conn->prepare($sql_envio);

            if (!$stmt_envio) {
                die("Error en la preparación de la consulta de id_envio: " . $conn->error);
            }

            $stmt_envio->bind_param("i", $id_publicacion);
            $stmt_envio->execute();
            $result_envio = $stmt_envio->get_result();
            
            //actualizo el estado de la publicacion
    
            /*$sql_envioPublicacion = "UPDATE publicacion SET pu_estado = :estado WHERE pu_id = :id";
            $stmt_envioPublicacion = $pdo->prepare($sql_envioPublicacion);
            $stmt_envioPublicacion->bindParam(':estado', $estado);
            $stmt_envioPublicacion->bindParam(':id', $id_publicacion, PDO::PARAM_INT);
            
            $estado = 'finalizada';
            $id_publicacion = $id_publicacion;
            
            $stmt_envioPublicacion->execute();*/
            
            if ($result_envio->num_rows > 0) {
                $envio = $result_envio->fetch_assoc();
                $id_envio = $envio['env_id_envio'];
                
                // Actualizar estado de la publicación
                $sql_envioPublicacion = "UPDATE publicacion SET pu_estado = 'finalizada' WHERE pu_id = ?";
                $stmt_publicacion = $conn->prepare($sql_envioPublicacion);
                $stmt_publicacion->bind_param("i", $id_publicacion);
                $stmt_publicacion->execute();
                
                
                // Verificar si los encabezados ya fueron enviados antes de la redirección
                /*if (headers_sent($file, $line)) {
                    die("Error: los encabezados ya fueron enviados en $file, línea $line");
                }*/
                
                // Redirigir a la página de calificación sin pasar id_solicitante en la URL
                header("Location: calificacionS_VD.php?env_id_envio=$id_envio");
                exit;
            } else {
                die("No se encontró el id_envio después de la actualización.");
            }
        } else {
            die("Error al actualizar la entrega: " . $stmt->error);
        }
    }

    $stmt->close();
}

// Función para obtener el id del solicitante basado en id_publicacion
function obtenerIdSolicitante($id_publicacion, $conn) {
    $sql = "SELECT pu_fk_u_id FROM publicacion WHERE pu_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $publicacion = $result->fetch_assoc();
        return $publicacion['pu_fk_u_id'];
    } else {
        return null;
    }
}


        
 include('pie.php');
        


// Cerrar conexión
$conn->close();
ob_end_flush(); // Al final del archivo PHP
?>
