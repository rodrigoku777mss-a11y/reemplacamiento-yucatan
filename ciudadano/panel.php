<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

$pdo = Conexion::obtenerConexion();
$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);

$stmtVehiculos = $pdo->prepare("SELECT COUNT(*) FROM VEHICULO WHERE id_propietario = ? AND activo = 1");
$stmtVehiculos->execute([$idPropietario]);
$totalVehiculos = (int) $stmtVehiculos->fetchColumn();

$stmtTramites = $pdo->prepare(
    "SELECT COUNT(*)
     FROM TRAMITE AS t
     INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
     WHERE v.id_propietario = ?"
);
$stmtTramites->execute([$idPropietario]);
$totalTramites = (int) $stmtTramites->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Ciudadano — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Módulo del Ciudadano</h1>
        <p>Bienvenido, <?= htmlspecialchars((string) $_SESSION["nombre_usuario"], ENT_QUOTES, "UTF-8") ?></p>
        <nav>
            <a href="panel.php">Panel</a> |
            <a href="vehiculos.php">Mis Vehículos</a> |
            <a href="tramites.php">Solicitar Trámite</a> |
            <a href="citas.php">Agendar Cita</a> |
            <a href="seguimiento.php">Seguimiento</a> |
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Resumen de tu cuenta</h2>
        <p>Vehículos activos registrados: <strong><?= $totalVehiculos ?></strong></p>
        <p>Trámites iniciados: <strong><?= $totalTramites ?></strong></p>
    </main>
</body>
</html>