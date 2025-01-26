<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $documento_id = $_GET['id'];

    // Obtener ID de usuario asociado al documento
    $sql = "SELECT d_usuario_id FROM documentos WHERE d_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documento_id]);
    $usuario_id = $stmt->fetchColumn();

    // Validar el documento
    $sql = "UPDATE documentos SET validado = 1 WHERE d_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documento_id]);

    // Validar el usuario
    $sql = "UPDATE usuario SET u_validado = 1 WHERE u_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);

    echo "Usuario validado correctamente.";
}
?>

