<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("admin");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/tramites.php");
    exit;
}

$folioTramite = trim($_POST["folio_tramite"] ?? "");
$estadoTramite = trim($_POST["estado_tramite"] ?? "");
$observaciones = trim($_POST["observaciones"] ?? "");
$observaciones = $observaciones !== "" ? $observaciones : null;

$estadosPermitidos = ["Pendiente", "En Revisión", "Aprobado", "Rechazado", "Finalizado"];

if ($folioTramite === "" || !in_array($estadoTramite, $estadosPermitidos, true)) {
    header("Location: ../admin/tramites.php?err=" . urlencode("Datos de trámite inválidos."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();
    $stmt = $pdo->prepare(
        "UPDATE TRAMITE
         SET estado_tramite = ?, observaciones = ?
         WHERE folio_tramite = ?"
    );
    $stmt->execute([$estadoTramite, $observaciones, $folioTramite]);

    header("Location: ../admin/tramites.php?msg=" . urlencode("Trámite " . $folioTramite . " actualizado a " . $estadoTramite . "."));
    exit;
} catch (PDOException $e) {
    error_log("Error en tramite_actualizar.php: " . $e->getMessage());
    header("Location: ../admin/tramites.php?err=" . urlencode("No se pudo actualizar el trámite."));
    exit;
}
