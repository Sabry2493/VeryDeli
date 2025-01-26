<?php
include 'conexion.php'; // Asegúrate de incluir tu archivo de conexión

// Obtener los datos enviados por la solicitud AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['accion']) && isset($data['denunciaId'])) {
    $accion = $data['accion'];
    $denunciaId = $data['denunciaId'];

    // Procesar la acción dependiendo de lo que se pase
    if ($accion == 'descartar') {
        // Cambiar el estado de la denuncia a 'gestionada'
        $sql = "UPDATE denuncias SET d_estado = 'gestionada' WHERE d_id = :denunciaId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':denunciaId', $denunciaId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } elseif ($accion == 'eliminar') {
        // Cambiar el estado de la denuncia a 'gestionada'
        $sql = "UPDATE denuncias SET d_estado = 'gestionada' WHERE d_id = :denunciaId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':denunciaId', $denunciaId, PDO::PARAM_INT);
        $stmt->execute();

        // Eliminar o cambiar el estado de la publicación
        $sqlPublicacion = "UPDATE publicacion SET pu_estado = 'eliminada' WHERE pu_id = (SELECT d_fk_pu_id FROM denuncias WHERE d_id = :denunciaId)";
        $stmtPublicacion = $pdo->prepare($sqlPublicacion);
        $stmtPublicacion->bindParam(':denunciaId', $denunciaId, PDO::PARAM_INT);
        $stmtPublicacion->execute();

        // Eliminar las postulaciones asociadas
        $sqlPostulaciones = "UPDATE postulacion SET po_estado = 'eliminada' WHERE po_fk_pu_id = (SELECT d_fk_pu_id FROM denuncias WHERE d_id = :denunciaId)";
        $stmtPostulaciones = $pdo->prepare($sqlPostulaciones);
        $stmtPostulaciones->bindParam(':denunciaId', $denunciaId, PDO::PARAM_INT);
        $stmtPostulaciones->execute();

        // Respuesta de éxito
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>
