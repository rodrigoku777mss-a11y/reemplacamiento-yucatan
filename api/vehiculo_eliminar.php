<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../ciudadano/vehiculos.php");
    exit;
}

$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);
$numSerie = strtoupper(trim($_POST["num_serie"] ?? ""));

if ($idPropietario <= 0 || $numSerie === "") {
    header("Location: ../ciudadano/vehiculos.php?err=" . urlencode("Vehículo no válido."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();
    $stmt = $pdo->prepare(
        "UPDATE VEHICULO SET activo = 0 WHERE num_serie = ? AND id_propietario = ?"
    );
    $stmt->execute([$numSerie, $idPropietario]);

    header("Location: ../ciudadano/vehiculos.php?msg=" . urlencode("Vehículo dado de baja correctamente."));
    exit;
} catch (PDOException $e) {
    error_log("Error en vehiculo_eliminar.php: " . $e->getMessage());
    header("Location: ../ciudadano/vehiculos.php?err=" . urlencode("No se pudo dar de baja el vehículo."));
    exit;
}