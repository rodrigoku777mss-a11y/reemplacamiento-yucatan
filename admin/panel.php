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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Módulo del Gestor / Administrador</h1>
        <p>Sesión activa: <?= htmlspecialchars((string) $_SESSION["nombre_usuario"], ENT_QUOTES, "UTF-8") ?></p>
        <nav>
            <a href="panel.php" class="activo">Panel</a>
            <a href="tramites.php">Revisión de Trámites</a>
            <a href="citas_modulos.php">Citas y Módulos</a>
            <a href="busqueda.php">Búsqueda</a>
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Resumen general del sistema</h2>
        <section class="grid-tarjetas">
            <article class="tarjeta-kpi">
                <span class="kpi-icono">📋</span>
                <p class="kpi-etiqueta">Total de trámites registrados</p>
                <p class="kpi-valor"><?= $totalTramites ?></p>
            </article>
            <article class="tarjeta-kpi">
                <span class="kpi-icono">⏳</span>
                <p class="kpi-etiqueta">Trámites pendientes de revisión</p>
                <p class="kpi-valor"><?= $tramitesPendientes ?></p>
            </article>
            <article class="tarjeta-kpi">
                <span class="kpi-icono">📅</span>
                <p class="kpi-etiqueta">Citas registradas</p>
                <p class="kpi-valor"><?= $totalCitas ?></p>
            </article>
            <article class="tarjeta-kpi">
                <span class="kpi-icono">🚗</span>
                <p class="kpi-etiqueta">Vehículos activos</p>
                <p class="kpi-valor"><?= $totalVehiculos ?></p>
            </article>
        </section>

        <h2>Accesos rápidos</h2>
        <section class="grid-tarjetas">
            <a href="tramites.php" class="tarjeta-acceso">
                <h3>Revisión de Trámites</h3>
                <p>Aprueba, rechaza o agrega observaciones a las solicitudes de reemplacamiento.</p>
                <span>Ir a trámites →</span>
            </a>
            <a href="citas_modulos.php" class="tarjeta-acceso">
                <h3>Citas y Módulos</h3>
                <p>Controla el estado de las citas programadas y consulta los módulos activos.</p>
                <span>Gestionar citas →</span>
            </a>
            <a href="busqueda.php" class="tarjeta-acceso">
                <h3>Búsqueda Avanzada</h3>
                <p>Localiza expedientes al instante por folio, placa actual o CURP del ciudadano.</p>
                <span>Abrir buscador →</span>
            </a>
        </section>
    </main>
</body>
</html>