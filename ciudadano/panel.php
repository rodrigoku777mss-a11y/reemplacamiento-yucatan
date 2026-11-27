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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Ciudadano — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Módulo del Ciudadano</h1>
        <p>Bienvenido, <?= htmlspecialchars((string) $_SESSION["nombre_usuario"], ENT_QUOTES, "UTF-8") ?></p>
        <nav>
            <a href="panel.php" class="activo">Panel</a>
            <a href="vehiculos.php">Mis Vehículos</a>
            <a href="tramites.php">Solicitar Trámite</a>
            <a href="citas.php">Agendar Cita</a>
            <a href="seguimiento.php">Seguimiento</a>
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Resumen de tu cuenta</h2>
        <section class="grid-tarjetas">
            <article class="tarjeta-kpi">
                <span class="kpi-icono">🚗</span>
                <p class="kpi-etiqueta">Vehículos activos registrados</p>
                <p class="kpi-valor"><?= $totalVehiculos ?></p>
            </article>
            <article class="tarjeta-kpi">
                <span class="kpi-icono">📄</span>
                <p class="kpi-etiqueta">Trámites iniciados</p>
                <p class="kpi-valor"><?= $totalTramites ?></p>
            </article>
        </section>

        <h2>¿Qué deseas hacer hoy?</h2>
        <section class="grid-tarjetas">
            <a href="vehiculos.php" class="tarjeta-acceso">
                <h3>Mis Vehículos</h3>
                <p>Da de alta nuevos automóviles o gestiona los que ya tienes registrados.</p>
                <span>Ver vehículos →</span>
            </a>
            <a href="tramites.php" class="tarjeta-acceso">
                <h3>Solicitar Trámite</h3>
                <p>Inicia tu solicitud de reemplacamiento seleccionando uno de tus vehículos.</p>
                <span>Iniciar trámite →</span>
            </a>
            <a href="citas.php" class="tarjeta-acceso">
                <h3>Agendar Cita</h3>
                <p>Elige fecha, hora y el módulo de atención más cercano en Yucatán.</p>
                <span>Programar cita →</span>
            </a>
            <a href="seguimiento.php" class="tarjeta-acceso">
                <h3>Seguimiento</h3>
                <p>Revisa el estado de tus trámites, observaciones del gestor y tus citas.</p>
                <span>Consultar estado →</span>
            </a>
        </section>
    </main>
</body>
</html>