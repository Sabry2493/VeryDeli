<?php
include 'cabecera.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar el token en la base de datos
    $stmt = $pdo->prepare("SELECT r_fk_u_id, r_expiracion FROM recuperacion WHERE r_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $recuperacion = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el token ha expirado
        if (new DateTime() < new DateTime($recuperacion['r_expiracion'])) {
            // Mostrar formulario para cambiar contraseña
            ?>
            
                <h2>Cambiar Contraseña</h2>
                <form action="cambiarContraseña.php" method="post">
                    <input type="hidden" name="user_id" value="<?php echo $recuperacion['r_fk_u_id']; ?>">
                    <!-- Contraseña -->
                    <div class="position-relative mb-0">
                            <label for="password" class="form-label mb-0">Nueva Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-sm" id="password" name="password" required minlength="8"
                                    pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}">
                            </div>
                            <small>Requisitos:</small>
                            <div id="password-requirements" style="font-size: 8px;">
                                <ul style="list-style-position: inside; padding-left: 0;">
                                    <li id="length" class="text-danger">Mínimo 8 caracteres</li>
                                    <li id="uppercase" class="text-danger">Al menos 1 mayúscula</li>
                                    <li id="number" class="text-danger">Al menos 1 número</li>
                                    <li id="special" class="text-danger">Al menos 1 carácter especial (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>
                    <button type="submit">Cambiar Contraseña</button>
                </form>
            
            <?php
        } else {
            echo "El enlace de recuperación ha expirado.";
        }
    } else {
        echo "Token de recuperación inválido.";
    }
} else {
    echo "Token no proporcionado.";
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la nueva contraseña

    // Actualizar la contraseña en la base de datos
    $stmt = $pdo->prepare("UPDATE usuario SET u_pwd = :password WHERE u_id = :user_id");
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        // Eliminar el token de recuperación
        $stmt = $pdo->prepare("DELETE FROM recuperacion WHERE r_fk_u_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        echo "Contraseña cambiada exitosamente. Ya puedes iniciar sesión.";
    } else {
        echo "Error al cambiar la contraseña. Intenta de nuevo.";
    }
}
?>
<script>
    // Validación de Contraseña en tiempo real
    document.getElementById("password").addEventListener("input", function() {
        const password = this.value;
        document.getElementById("length").classList.toggle("text-success", password.length >= 8);
        document.getElementById("length").classList.toggle("text-danger", password.length < 8);

        document.getElementById("uppercase").classList.toggle("text-success", /[A-Z]/.test(password));
        document.getElementById("uppercase").classList.toggle("text-danger", !/[A-Z]/.test(password));

        document.getElementById("number").classList.toggle("text-success", /[0-9]/.test(password));
        document.getElementById("number").classList.toggle("text-danger", !/[0-9]/.test(password));

        document.getElementById("special").classList.toggle("text-success", /[!@#$%^&*]/.test(password));
        document.getElementById("special").classList.toggle("text-danger", !/[!@#$%^&*]/.test(password));
    });

    // Activar validación de Bootstrap
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()

    //Alternar visibilidad de la contraseña
    document.getElementById('togglePwdVisibility').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
</script>