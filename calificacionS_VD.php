
<?php
session_start();
//var_dump($_SESSION); // Esto te mostrará si las variables existen

// Conectar a la base de datos


$host = 'localhost';
$dbname = 'verydeli_verydeli';
$username = 'verydeli_tecnicaturaRedes'; 
$password = 'verydel11';


// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID de envío
$id_envio = isset($_GET['env_id_envio']) ? (int)$_GET['env_id_envio'] : 0;

// Obtener el ID de solicitante

//Paso1: Obtener el ID de publicación a partir del ID de envío
$sql_envio = "SELECT env_id_publicacion FROM envio WHERE env_id_envio = ?";
$stmt_envio = $conn->prepare($sql_envio);
$stmt_envio->bind_param("i", $id_envio);
$stmt_envio->execute();
$result_envio = $stmt_envio->get_result();

if ($result_envio->num_rows > 0) {
    $data = $result_envio->fetch_assoc();
    $id_publicacion = $data['env_id_publicacion'];
     
    //Paso 2:Obtener la foto del paquete desde la tabla `publicacion`
    $sql_foto_paquete = "SELECT pu_foto FROM publicacion WHERE pu_id = ?";
    $stmt_foto_paquete = $conn->prepare($sql_foto_paquete);
    $stmt_foto_paquete->bind_param("i", $id_publicacion);
    $stmt_foto_paquete->execute();
    $result_foto_paquete = $stmt_foto_paquete->get_result();

    if ($result_foto_paquete->num_rows > 0) {
        $data_foto_paquete = $result_foto_paquete->fetch_assoc();
        $foto_paquete_url = $data_foto_paquete['pu_foto'];
    } else {
        die("No se encontró la foto del paquete para el ID de publicación: " . htmlspecialchars($id_publicacion));
    }

     // Paso 3: Obtener el ID de solicitante desde la tabla `publicacion`
     $sql_publicacion = "SELECT pu_fk_u_id FROM publicacion WHERE pu_id = ?";
     $stmt_publicacion = $conn->prepare($sql_publicacion);
     $stmt_publicacion->bind_param("i", $id_publicacion);
     $stmt_publicacion->execute();
     $result_publicacion = $stmt_publicacion->get_result();
 
     if ($result_publicacion->num_rows > 0) {
         $data_publicacion = $result_publicacion->fetch_assoc();
         $id_solicitante = $data_publicacion['pu_fk_u_id'];
        

         // Paso 4: Obtener el nombre del solicitante
         $sql_solicitante = "SELECT u_nombre, u_avatar FROM usuario WHERE u_id = ?";
         $stmt_solicitante = $conn->prepare($sql_solicitante);
         $stmt_solicitante->bind_param("i", $id_solicitante);
         $stmt_solicitante->execute();
         $result_solicitante = $stmt_solicitante->get_result();
 
         if ($result_solicitante->num_rows > 0) {
             $data_solicitante = $result_solicitante->fetch_assoc();
             $nombre_solicitante = $data_solicitante['u_nombre'];
             $avatar_id = $data_solicitante['u_avatar'];

         } else {
             die("No se encontró el solicitante con el ID: " . htmlspecialchars($id_solicitante));
         }

         // Obtener la URL del avatar
            $sql_avatar = "SELECT a_url FROM avatar WHERE a_id = ?";
            $stmt_avatar = $conn->prepare($sql_avatar);
            $stmt_avatar->bind_param("i", $avatar_id);
            $stmt_avatar->execute();
            $result_avatar = $stmt_avatar->get_result();
            $avatar_url = $result_avatar->fetch_assoc()['a_url'];

     } else {
         die("No se encontró el solicitante para el ID de publicación: " . htmlspecialchars($id_publicacion));
     }
 
     // Paso 5: Obtener el ID de postulante desde la tabla `postulacion`
     $sql_postulacion = "SELECT po_fk_u_id FROM postulacion WHERE po_id = ? AND po_estado = 'elegido'";
     $stmt_postulacion = $conn->prepare($sql_postulacion);
     $stmt_postulacion->bind_param("i", $id_publicacion);
     $stmt_postulacion->execute();
     $result_postulacion = $stmt_postulacion->get_result();
 
     if ($result_postulacion->num_rows > 0) {
         $data_postulacion = $result_postulacion->fetch_assoc();
         $id_postulante = $data_postulacion['po_fk_u_id'];
     } else {
         die("No se encontró el postulante para el ID de publicación: " . htmlspecialchars($id_publicacion));
     }

} else {
    die("No se encontró la publicación para el ID de envío: " . htmlspecialchars($id_envio));
}

// Mostrar el formulario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificación a solicitante</title>
    <style>
        
        @import url('https://fonts.googleapis.com/css2?family=Oregano&family=Zilla+Slab:wght@400;500;600;700&display=swap');
        body {
            font-family: Arial, sans-serif;
            font-family: "Zilla Slab", serif;/*cambio*/
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            background: linear-gradient(to bottom, #e7fffb,#dddddd,#cac9c9);/*cambio*/
        }
        header {
            text-align: center;
            background-color: #007bff;
            background: linear-gradient(to right, #615778, #B3A1DE);
            background: linear-gradient(to right, #6e5976, #9780a0);/*cambio*/
            color: white;
            padding: 20px;
        }
        article {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 8px rgba(128, 0, 128, 0.5);  /* Sombra púrpura */
            max-width: 600px;
            margin: 20px auto; /* Centra horizontalmente el article */
            text-align: center; /* Centra el contenido dentro del article */
            position: relative; /* Para posicionar el vehículo relativo al contenedor */
        }
        img {
            display: block;
            max-width: 100px;
            margin: 10px auto;
        }
        h2 {
            text-align: center;
        }
        .calificacion {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .calificacion input[type="radio"] {
            display: none;
        }
        .calificacion label {
            font-size: 30px;
            color: gray;
            cursor: pointer;
        }
        .calificacion input[type="radio"]:checked ~ label {
            color: gold;
        }
        textarea {
            width: 94%;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            font-family: "Zilla Slab", serif;/*cambio*/
        }
        input[type="submit"] {
            display: block;
            width: 100%;
            background-color: #007bff;
            background: linear-gradient(to right, #615778, #B3A1DE);
            background: linear-gradient(to right, #6e5976, #9780a0);/*cambio*/
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
            font-family: "Zilla Slab", serif;/*cambio*/
        }
        .contenedor-imagen {
            position: relative; /* Hace que el contenedor sea el punto de referencia para el vehículo */
            display: inline-block; /* Mantiene las imágenes juntas */
        }
        .imagen-postulante {
            max-width: 250px;
            display: block;
        }
        .imagen-vehiculo {
            position: absolute;
            bottom: -20px; /* Posiciona la imagen en la parte inferior */
            right: -35px;  /* Posiciona la imagen en la esquina derecha */
            max-width: 120px;
            border-radius: 50%; /* Hace la imagen del vehículo circular, si lo prefieres */
        }
        .error {
            color: red;
            display: none; /* Oculta el mensaje por defecto */
        }
    </style>
</head>
<body>
    <header>
        <h1>Califica tu experiencia</h1>
        <h2>Publicación N°: <?php echo htmlspecialchars($id_publicacion); ?></h2>
    </header>
    <article>
        <!-- Fecha con hora dinámica -->
        <h2><span id="fecha-hora"></span></h2>
         <!-- Contenedor que agrupa las imágenes del postulante y del vehículo -->
         <div class="contenedor-imagen">
            <!-- Avatar del solicitante -->
            <img src="<?php echo isset($avatar_url) ? htmlspecialchars($avatar_url) : '/Imagenes/user2.png'; ?>" alt="Imagen del solicitante" class="imagen-postulante" />
            <!-- Foto del paquete -->
            <img src="<?php echo isset($foto_paquete_url) ? htmlspecialchars($foto_paquete_url) : '/foto/paquet.png'; ?>" alt="Paquete llevado" class="imagen-vehiculo" />
        </div>
        <p><strong>Nombre del solicitante:</strong> <?php echo htmlspecialchars($nombre_solicitante); ?></p>
        
        <form action="procesar_calificacionSVD.php" method="POST" ><!-- onsubmit="return validarComentario()" -->
        <input type="hidden" name="id_envio" value="<?php echo $id_envio; ?>">
        <input type="hidden" name="id_solicitante" value="<?php echo $id_solicitante; ?>">
        <input type="hidden" name="id_postulante" value="<?php echo $id_postulante; ?>">
        <h3>Calificación:</h3>
        <div class="calificacion">
            <input type="radio" name="rating" id="star1" value="5" >
            <label for="star1">&#9733;</label>
            <input type="radio" name="rating" id="star2" value="4">
            <label for="star2">&#9733;</label>
            <input type="radio" name="rating" id="star3" value="3">
            <label for="star3">&#9733;</label>
            <input type="radio" name="rating" id="star4" value="2">
            <label for="star4">&#9733;</label>
            <input type="radio" name="rating" id="star5" value="1">
            <label for="star5">&#9733;</label>
        </div>
       
        <label for="comentario">Deja tu comentario:</label>
            <textarea name="comentario" id="comentario" rows="4" placeholder="Escribe tu comentario aquí..." ></textarea>
            <div class="error" id="error-message">Debes escribir al menos 2 palabras.</div>
        <input type="submit" value="Enviar calificación" style="font-family: Oregano, cursive;font-size: 20px;">
        <input type="submit" value="Omitir por ahora" name="omitir" ></input>    <!-- onclick="window.location.href='publicaciones.php'" -->
    </form>
    </article>
    
    <script>
        // Obtener la fecha y hora actual
        function obtenerFechaHora() {
            const fecha = new Date();
            const opciones = { 
                year: 'numeric', month: 'long', day: 'numeric', 
                hour: 'numeric', minute: 'numeric', second: 'numeric' 
            };
            const fechaFormateada = fecha.toLocaleDateString('es-ES', opciones);
            document.getElementById('fecha-hora').textContent = fechaFormateada;
        }

        // Actualizar el elemento al cargar la página
        window.onload = obtenerFechaHora;

        function contarLetras(texto) {
            // Elimina los espacios en blanco y cuenta solo letras
            texto = texto.replace(/\s+/g, ''); // Elimina todos los espacios
            return texto.length; // Devuelve la longitud del texto sin espacios
        }

        function validarComentario() {
            const textarea = document.getElementById('comentario');
            const errorMessage = document.getElementById('error-message');
            const minimoLetras = 7;  // Define el mínimo de letras

            const numLetras = contarLetras(textarea.value);

            if (numLetras < minimoLetras) {
                errorMessage.style.display = 'block';  // Muestra el mensaje de error
                return false;  // Evita el envío del formulario
            } else {
                errorMessage.style.display = 'none';  // Oculta el mensaje de error
                return true;  // Permite el envío del formulario
            }
        }
    </script>
</body>
</html>
