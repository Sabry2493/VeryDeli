<?php
include "cabecera.php";
?>

<div class="content" style="padding-bottom: 20px;">
    <div class="container">
        <div class="row justify-content-center">
        <div class="col-11 col-lg-4">
                <br>
                <div class="card">
                    <div class="card-header">Iniciar sesión</div>
                    <div class="card-body">
                        <?php
                        // Mostrar mensaje de error si existe en la URL
                        if (isset($_GET['error'])) {
                            $error_msg = "";
                            switch ($_GET['error']) {
                                case 'contraseña_incorrecta':
                                    $error_msg = "Contraseña incorrecta.";
                                    break;
                                case 'usuario_no_encontrado':
                                    $error_msg = "Usuario no encontrado.";
                                    break;
                                case 'campos_vacios':
                                    $error_msg = "Por favor, complete todos los campos.";
                                    break;
                            }
                            echo "<div class='alert alert-danger'>$error_msg</div>";
                        }
                        ?>
                        <form action="loguin_logica.php" method="POST">
                            Usuario: <input class="form-control" type="text" name="usuario" id="">
                            <br>
                            Contraseña: <input class="form-control" type="password" name="pwd" id="">
                            <span><a href="recuperarContraseña.php">Olvidé mi contraseña</a></span>
                            <br>
                            <button class="btn btn-secondary" type="submit">Iniciar sesión</button>
                            
                            <br><br>
                            <p class="text-body-secondary fw-light small text-center">
                                ¿Aún no tienes una cuenta? <a href="usuario_formulario.php" class="text-reset">¡Regístrate ahora!</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php
include "pie.php";
?>
