
<?php

include('conexion.php'); 
include('header.php');
session_start();

   
$admin='';

// Inicializar variable para el avatar
$avatar_url = 'Imagenes/avatarAbeja.jpg'; // URL por defecto si no hay avatar

// Verificar si el usuario está logueado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Consulta para obtener la URL del avatar usando el ID del usuario
    $query = "
        SELECT a.a_url 
        FROM avatar a
        JOIN usuario u ON u.u_avatar = a.a_id
        WHERE u.u_id = :user_id
    ";
    
    try {
        $stmt = $pdo->prepare($query); // Usar $pdo para preparar la consulta
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Usar :user_id como parámetro
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $avatar_url = $row['a_url']; // Asignar el URL del avatar del usuario
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}
?>

<!-- Código HTML para la barra de navegación -->
 

<nav class="navbar navbar-expand-lg" id="navBar">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="Imagenes/logo.png" alt="Verydeli" width="90" height="24">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            

            <?php 
            if($admin===true){

?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="administrar.php">Administrar</a>
                </li>
                
            </ul>



            <?php
            }else{
//si no es administrador le da opcion publicar/postular
            ?>
            
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="crearPublicacion.php">Publicar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="misPublicaciones.php">Mis interacciones</a>
                </li>
            </ul>
            <?php }?>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <?php if (isset($_SESSION['user_id'])): // Usuario logueado ?>
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $avatar_url; ?>" alt="Avatar" width="30" height="30" class="rounded-circle"> <!-- Muestra el avatar -->
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="editarPerfil.php">Mi perfil</a></li>
                            <li><a class="dropdown-item" href="misPublicaciones.php">Mis publicaciones</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    <?php else: // Usuario no logueado ?>
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Ingresar
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="loguin_formulario.php">Loguin</a></li>
                            <li><a class="dropdown-item" href="usuario_formulario.php">Regístrate</a></li>
                        </ul>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
