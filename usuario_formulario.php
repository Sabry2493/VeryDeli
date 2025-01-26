<?php
include "cabecera.php";
include('header.php');

?>

<div class="container small" style="padding: 9px; font-size: 12px;">
   
    <div class="row justify-content-center">
    <div class="col-11 col-lg-4">
            <br>
            <div class="card">
                <div class="card-header text-center">Registrar Usuario</div>
                <div class="card-body">
                    <!-- Formulario con validación de Bootstrap -->
                    <form id="userForm" action="usuario_logica.php" method="POST" class="needs-validation" novalidate>


                        <!-- Nombre -->
                        <div class="mb-0">
                            <label for="nombre" class="form-label mb-0">Nombre:</label>
                            <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" required minlength="3"
                                pattern="^(?!.*([A-Za-z])\1)(?=.*[BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz]).{3,}$">
                            <div class="invalid-feedback">Ingrese un nombre real de al menos 3 letras</div>
                        </div>

                        <!-- Apellido -->
                        <div class="mb-0">
                            <label for="apellido" class="form-label mb-0">Apellido:</label>
                            <input type="text" class="form-control form-control-sm" id="apellido" name="apellido" required minlength="3" pattern="[A-Za-z]{3,}">
                            <div class="invalid-feedback">No reconocemos su apellido!</div>
                        </div>

                        <!-- Email -->
                        <div class="mb-0">
                            <label fo r="email" class="form-label mb-0">Email:</label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" required
                                pattern="^[A-Za-z0-9]{3,}@[A-Za-z]{5}\.com$">
                            <div class="invalid-feedback ">Por favor ingresa un email válido!</div>
                        </div>

                        <!-- Nombre de Usuario -->
                        <div class="mb-0">
                            <label for="userName" class="form-label mb-0">Nombre de Usuario:</label>
                            <input type="text" class="form-control form-control-sm" id="userName" name="userName" required minlength="3" pattern="[A-Za-z0-9]{3,}">
                            <div class="invalid-feedback">El nombre de usuario debe tener al menos 3 caracteres alfanuméricos.</div>
                        </div>

                        <!-- Contraseña -->
                        <div class="position-relative mb-0">
                            <label for="pwd" class="form-label mb-0">Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-sm" id="pwd" name="pwd" required minlength="8"
                                    pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}">
                            </div>
                            <small>Requisitos:</small>
                            <div id="password-requirements" style="font-size: 10px;">
                                <ul style="list-style-position: inside; padding-left: 0;">
                                    <li id="length" class="text-danger">Mínimo 8 caracteres</li>
                                    <li id="uppercase" class="text-danger">Al menos 1 mayúscula</li>
                                    <li id="number" class="text-danger">Al menos 1 número</li>
                                    <li id="special" class="text-danger">Al menos 1 carácter especial (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>
                        

                        <!-- Botón de Envío -->
                        <button class="btn btn-secondary btn-sm mb-0" type="submit">Guardar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación de Contraseña en tiempo real
    document.getElementById("pwd").addEventListener("input", function() {
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
        const passwordField = document.getElementById('pwd');
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

<?php
include "pie.php";
?>
