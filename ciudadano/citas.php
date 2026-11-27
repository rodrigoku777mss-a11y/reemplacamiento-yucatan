<?php

require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

$pdo = Conexion::obtenerConexion();
$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);

$stmtTramites = $pdo->prepare(
    "SELECT t.folio_tramite, t.tipo_tramite, t.estado_tramite, v.placa_actual
     FROM TRAMITE AS t
     INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
     WHERE v.id_propietario = ?
     ORDER BY t.fecha_inicio DESC"
);
$stmtTramites->execute([$idPropietario]);
$tramites = $stmtTramites->fetchAll();

$modulos = $pdo->query(
    "SELECT id_modulo, nombre_modulo, municipio FROM MODULO WHERE activo = 1 ORDER BY municipio, nombre_modulo"
)->fetchAll();

$msg = $_GET["msg"] ?? "";
$err = $_GET["err"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Agendar Cita en Módulo</h1>
        <nav>
            <a href="panel.php">Panel</a>
            <a href="vehiculos.php">Mis Vehículos</a>
            <a href="tramites.php">Solicitar Trámite</a>
            <a href="citas.php" class="activo">Agendar Cita</a>
            <a href="seguimiento.php">Seguimiento</a>
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <?php if ($msg !== ""): ?>
            <p class="alerta-exito"><?= htmlspecialchars($msg, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>
        <?php if ($err !== ""): ?>
            <p class="alerta-error"><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <h2>Programar atención presencial</h2>
        <?php if (empty($tramites)): ?>
            <div class="estado-vacio">
                <h3>No tienes trámites disponibles</h3>
                <p>Antes de agendar una cita en módulo necesitas generar al menos una solicitud de trámite.</p>
                <a href="tramites.php" class="btn-primario">Solicitar trámite →</a>
            </div>
        <?php else: ?>
            <form method="POST" action="../api/cita_agendar.php">
                <label for="folio_tramite">Folio de trámite:</label>
                <select id="folio_tramite" name="folio_tramite" required>
                    <option value="">Selecciona un trámite</option>
                    <?php foreach ($tramites as $t): ?>
                        <option value="<?= htmlspecialchars($t["folio_tramite"], ENT_QUOTES, "UTF-8") ?>">
                            <?= htmlspecialchars($t["folio_tramite"] . " (" . $t["placa_actual"] . " — " . $t["estado_tramite"] . ")", ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="id_modulo">Módulo de atención:</label>
                <select id="id_modulo" name="id_modulo" required>
                    <option value="">Selecciona un módulo</option>
                    <?php foreach ($modulos as $mod): ?>
                        <option value="<?= (int) $mod["id_modulo"] ?>">
                            <?= htmlspecialchars($mod["municipio"] . " — " . $mod["nombre_modulo"], ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="fecha_programada">Fecha:</label>
                <input type="date" id="fecha_programada" name="fecha_programada" min="<?= date("Y-m-d") ?>" required>

                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" required>

                <button type="submit">Confirmar cita</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>