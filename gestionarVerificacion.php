<?php
include 'conexion.php';  // Conexión a la base de datos

// Obtener datos JSON enviados por la solicitud AJAX
$data = json_decode(file_get_contents('php://input'), true);

$accion = $data['accion'];
$verificacionId = $data['verificacionId'];

if ($accion == 'verificar') {
    // Actualizar la verificación a "gestionada"
    $sql = "UPDATE verificaciones SET ve_estado = 'gestionada' WHERE ve_id = :verificacionId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':verificacionId', $verificacionId, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el ID del usuario de la verificación
    $sql = "SELECT ve_user_id FROM verificaciones WHERE ve_id = :verificacionId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':verificacionId', $verificacionId, PDO::PARAM_INT);
    $stmt->execute();
    $verificacion = $stmt->fetch(PDO::FETCH_ASSOC);

    // Actualizar el usuario como verificado
    $sql = "UPDATE usuario SET u_verificado = 'true' WHERE u_id = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $verificacion['ve_user_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Responder con éxito
    echo json_encode(['success' => true]);

} elseif ($accion == 'descartar') {
    // Actualizar la verificación a "gestionada" sin cambiar el estado del usuario
    $sql = "UPDATE verificaciones SET ve_estado = 'gestionada' WHERE ve_id = :verificacionId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':verificacionId', $verificacionId, PDO::PARAM_INT);
    $stmt->execute();

    // Responder con éxito
    echo json_encode(['success' => true]);

} else {
    // Responder con error si la acción no es válida
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
