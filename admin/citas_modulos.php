<?php

require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("admin");

$pdo = Conexion::obtenerConexion();
$estadosCita = ["Programada", "Atendida", "Cancelada", "Reprogramada"];
$msg = $_GET["msg"] ?? "";
$err = $_GET["err"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idCita = (int) ($_POST["id_cita"] ?? 0);
    $nuevoEstado = trim($_POST["estado_cita"] ?? "");

    if ($idCita > 0 && in_array($nuevoEstado, $estadosCita, true)) {
        $stmtUpd = $pdo->prepare("UPDATE CITA SET estado_cita = ? WHERE id_cita = ?");
        $stmtUpd->execute([$nuevoEstado, $idCita]);
        header("Location: citas_modulos.php?msg=" . urlencode("Estado de cita actualizado."));
        exit;
    } else {
        header("Location: citas_modulos.php?err=" . urlencode("Datos de cita inválidos."));
        exit;
    }
}

$sqlCitas = "SELECT c.id_cita, c.fecha_programada, c.hora, c.estado_cita, c.folio_tramite,
                    m.nombre_modulo, m.municipio,
                    p.nombre, p.primer_apellido
             FROM CITA AS c
             INNER JOIN MODULO AS m ON m.id_modulo = c.id_modulo
             INNER JOIN TRAMITE AS t ON t.folio_tramite = c.folio_tramite
             INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
             INNER JOIN PROPIETARIO AS p ON p.id_propietario = v.id_propietario
             ORDER BY c.fecha_programada ASC, c.hora ASC";
$citas = $pdo->query($sqlCitas)->fetchAll();

$modulos = $pdo->query("SELECT * FROM MODULO WHERE activo = 1 ORDER BY municipio, nombre_modulo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas y Módulos — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Control de Citas y Módulos</h1>
        <nav>
            <a href="panel.php">Panel</a> |
            <a href="tramites.php">Revisión de Trámites</a> |
            <a href="citas_modulos.php">Citas y Módulos</a> |
            <a href="busqueda.php">Búsqueda</a> |
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

        <h2>Citas programadas</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Folio</th>
                <th>Ciudadano</th>
                <th>Módulo</th>
                <th>Fecha y hora</th>
                <th>Estado</th>
            </tr>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?= (int) $cita["id_cita"] ?></td>
                    <td><?= htmlspecialchars($cita["folio_tramite"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($cita["nombre"] . " " . $cita["primer_apellido"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($cita["municipio"] . " — " . $cita["nombre_modulo"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($cita["fecha_programada"] . " " . $cita["hora"], ENT_QUOTES, "UTF-8") ?></td>
                    <td>
                        <form method="POST" action="citas_modulos.php" style="display:inline;">
                            <input type="hidden" name="id_cita" value="<?= (int) $cita["id_cita"] ?>">
                            <select name="estado_cita">
                                <?php foreach ($estadosCita as $est): ?>
                                    <option value="<?= htmlspecialchars($est, ENT_QUOTES, "UTF-8") ?>" <?= $cita["estado_cita"] === $est ? "selected" : "" ?>>
                                        <?= htmlspecialchars($est, ENT_QUOTES, "UTF-8") ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Guardar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Módulos de atención activos</h2>
        <ul>
            <?php foreach ($modulos as $mod): ?>
                <li>
                    <strong><?= htmlspecialchars($mod["nombre_modulo"], ENT_QUOTES, "UTF-8") ?></strong>
                    (<?= htmlspecialchars($mod["municipio"], ENT_QUOTES, "UTF-8") ?>) —
                    <?= htmlspecialchars($mod["direccion"], ENT_QUOTES, "UTF-8") ?> —
                    Tel: <?= htmlspecialchars($mod["tel_modulo"], ENT_QUOTES, "UTF-8") ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>