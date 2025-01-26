<?php
include('cabecera.php');



?>

<h2>Recuperar Contraseña</h2>
    <form action="recuperarContraseña.php" method="post">
        <label for="email">Introduce tu correo electrónico:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Enviar Enlace de Recuperación</button>
    </form>

    <?php
 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verificar si el correo está registrado
    $stmt = $pdo->prepare("SELECT u_id FROM usuario WHERE u_email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Usuario encontrado, generar token de recuperación
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = bin2hex(random_bytes(16));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour')); // token válido por 1 hora

        // Guardar el token y expiración en la base de datos
        $stmt = $pdo->prepare("INSERT INTO recuperacion (r_fk_u_id, r_token, r_expiracion) VALUES (:user_id, :token, :expiracion)");
        $stmt->bindParam(':user_id', $user['u_id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiracion', $expiracion);
        $stmt->execute();

        // Enviar el enlace de recuperación por correo
        $enlaceRecuperacion = "https://verydeli.servidoronline.net/cambiarContraseña.php?token=$token";
        $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña: $enlaceRecuperacion";
        mail($email, "Recuperación de Contraseña", $mensaje);

        echo "Se ha enviado un enlace de recuperación a tu correo electrónico.";
    } else {
        echo "No se encontró una cuenta con ese correo.";
    }
}
?>    