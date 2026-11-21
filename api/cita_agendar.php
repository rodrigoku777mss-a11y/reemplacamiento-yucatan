<?php

require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../ciudadano/citas.php");
    exit;
}

$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);
$folioTramite = trim($_POST["folio_tramite"] ?? "");
$idModulo = (int) ($_POST["id_modulo"] ?? 0);
$fecha = trim($_POST["fecha_programada"] ?? "");
$hora = trim($_POST["hora"] ?? "");

if ($idPropietario <= 0 || $folioTramite === "" || $idModulo <= 0 || $fecha === "" || $hora === "") {
    header("Location: ../ciudadano/citas.php?err=" . urlencode("Completa todos los campos de la cita."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();

    // Validar que el trámite pertenezca al propietario
    $stmtVal = $pdo->prepare(
        "SELECT 1
         FROM TRAMITE AS t
         INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
         WHERE t.folio_tramite = ? AND v.id_propietario = ?"
    );
    $stmtVal->execute([$folioTramite, $idPropietario]);
    if (!$stmtVal->fetch()) {
        header("Location: ../ciudadano/citas.php?err=" . urlencode("El trámite seleccionado no pertenece a tu cuenta."));
        exit;
    }

    $horaNormalizada = strlen($hora) === 5 ? $hora . ":00" : $hora;

    $stmt = $pdo->prepare(
        "INSERT INTO CITA (fecha_programada, hora, estado_cita, folio_tramite, id_modulo)
         VALUES (?, ?, 'Programada', ?, ?)"
    );
    $stmt->execute([$fecha, $horaNormalizada, $folioTramite, $idModulo]);

    header("Location: ../ciudadano/citas.php?msg=" . urlencode("Cita agendada correctamente para el " . $fecha . "."));
    exit;
} catch (PDOException $e) {
    error_log("Error en cita_agendar.php: " . $e->getMessage());
    header("Location: ../ciudadano/citas.php?err=" . urlencode("No se pudo agendar la cita."));
    exit;
}