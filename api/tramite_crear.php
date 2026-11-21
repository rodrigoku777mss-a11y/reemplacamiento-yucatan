<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../ciudadano/tramites.php");
    exit;
}

$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);
$numSerie = strtoupper(trim($_POST["num_serie"] ?? ""));
$tipoTramite = trim($_POST["tipo_tramite"] ?? "");
$tiposPermitidos = ["Canje de Placas", "Alta Vehicular", "Reposición de Placas"];

if ($idPropietario <= 0 || $numSerie === "" || !in_array($tipoTramite, $tiposPermitidos, true)) {
    header("Location: ../ciudadano/tramites.php?err=" . urlencode("Selecciona un vehículo y un tipo de trámite válido."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();

    // Verificar que el vehículo pertenezca al propietario autenticado
    $stmtVal = $pdo->prepare("SELECT 1 FROM VEHICULO WHERE num_serie = ? AND id_propietario = ? AND activo = 1");
    $stmtVal->execute([$numSerie, $idPropietario]);
    if (!$stmtVal->fetch()) {
        header("Location: ../ciudadano/tramites.php?err=" . urlencode("El vehículo seleccionado no corresponde a tu cuenta."));
        exit;
    }

    $folio = "YUC-" . date("Y") . "-" . strtoupper(bin2hex(random_bytes(3)));

    $stmt = $pdo->prepare(
        "INSERT INTO TRAMITE (folio_tramite, tipo_tramite, estado_tramite, fecha_inicio, num_serie)
         VALUES (?, ?, 'Pendiente', CURRENT_DATE, ?)"
    );
    $stmt->execute([$folio, $tipoTramite, $numSerie]);

    header("Location: ../ciudadano/tramites.php?msg=" . urlencode("Trámite creado con folio: " . $folio));
    exit;
} catch (PDOException $e) {
    error_log("Error en tramite_crear.php: " . $e->getMessage());
    header("Location: ../ciudadano/tramites.php?err=" . urlencode("No se pudo generar el trámite."));
    exit;
}