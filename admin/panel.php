<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("admin");

$pdo = Conexion::obtenerConexion();

$totalTramites = (int) $pdo->query("SELECT COUNT(*) FROM TRAMITE")->fetchColumn();
$tramitesPendientes = (int) $pdo->query("SELECT COUNT(*) FROM TRAMITE WHERE estado_tramite = 'Pendiente'")->fetchColumn();
$totalCitas = (int) $pdo->query("SELECT COUNT(*) FROM CITA")->fetchColumn();
$totalVehiculos = (int) $pdo->query("SELECT COUNT(*) FROM VEHICULO WHERE activo = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Módulo del Gestor / Administrador</h1>
        <p>Sesión activa: <?= htmlspecialchars((string) $_SESSION["nombre_usuario"], ENT_QUOTES, "UTF-8") ?></p>
        <nav>
            <a href="panel.php">Panel</a> |
            <a href="tramites.php">Revisión de Trámites</a> |
            <a href="citas_modulos.php">Citas y Módulos</a> |
            <a href="busqueda.php">Búsqueda</a> |
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Resumen general del sistema</h2>
        <p>Total de trámites registrados: <strong><?= $totalTramites ?></strong></p>
        <p>Trámites pendientes de revisión: <strong><?= $tramitesPendientes ?></strong></p>
        <p>Citas registradas: <strong><?= $totalCitas ?></strong></p>
        <p>Vehículos activos: <strong><?= $totalVehiculos ?></strong></p>
    </main>
</body>
</html>