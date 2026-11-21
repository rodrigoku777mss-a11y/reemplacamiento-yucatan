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
$placaActual = strtoupper(trim($_POST["placa_actual"] ?? ""));
$numMotor = strtoupper(trim($_POST["num_motor"] ?? ""));
$anio = (int) ($_POST["anio"] ?? 0);
$idModelo = (int) ($_POST["id_modelo"] ?? 0);

if ($idPropietario <= 0 || $numSerie === "" || $placaActual === "" || $numMotor === "" || $anio < 1950 || $anio > 2027 || $idModelo <= 0) {
    header("Location: ../ciudadano/vehiculos.php?err=" . urlencode("Completa todos los campos del vehículo con datos válidos."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();
    $stmt = $pdo->prepare(
        "INSERT INTO VEHICULO (num_serie, placa_actual, num_motor, anio, id_propietario, id_modelo)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$numSerie, $placaActual, $numMotor, $anio, $idPropietario, $idModelo]);

    header("Location: ../ciudadano/vehiculos.php?msg=" . urlencode("Vehículo registrado correctamente."));
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        header("Location: ../ciudadano/vehiculos.php?err=" . urlencode("Ya existe un vehículo con ese número de serie, placa o número de motor."));
    } else {
        error_log("Error en vehiculo_guardar.php: " . $e->getMessage());
        header("Location: ../ciudadano/vehiculos.php?err=" . urlencode("No se pudo guardar el vehículo."));
    }
    exit;
}