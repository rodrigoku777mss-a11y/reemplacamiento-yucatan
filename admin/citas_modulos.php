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

function claseBadgeCita(string $estado): string {
    return match ($estado) {
        "Programada"   => "badge-revision",
        "Atendida"     => "badge-aprobado",
        "Cancelada"    => "badge-rechazado",
        "Reprogramada" => "badge-pendiente",
        default        => "badge-finalizado",
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas y Módulos — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Control de Citas y Módulos</h1>
        <nav>
            <a href="panel.php">Panel</a>
            <a href="tramites.php">Revisión de Trámites</a>
            <a href="citas_modulos.php" class="activo">Citas y Módulos</a>
            <a href="busqueda.php">Búsqueda</a>
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
        <?php if (empty($citas)): ?>
            <div class="estado-vacio">
                <h3>No hay citas programadas</h3>
                <p>Las citas agendadas por los ciudadanos aparecerán listadas en esta sección.</p>
            </div>
        <?php else: ?>
            <div class="tabla-contenedor">
                <table>
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
                            <td>#<?= (int) $cita["id_cita"] ?></td>
                            <td><strong><?= htmlspecialchars($cita["folio_tramite"], ENT_QUOTES, "UTF-8") ?></strong></td>
                            <td><?= htmlspecialchars($cita["nombre"] . " " . $cita["primer_apellido"], ENT_QUOTES, "UTF-8") ?></td>
                            <td>
                                <strong><?= htmlspecialchars($cita["municipio"], ENT_QUOTES, "UTF-8") ?></strong><br>
                                <small><?= htmlspecialchars($cita["nombre_modulo"], ENT_QUOTES, "UTF-8") ?></small>
                            </td>
                            <td><?= htmlspecialchars($cita["fecha_programada"] . " " . $cita["hora"], ENT_QUOTES, "UTF-8") ?></td>
                            <td>
                                <div style="margin-bottom: 0.45rem;">
                                    <span class="badge <?= claseBadgeCita($cita["estado_cita"]) ?>">
                                        <?= htmlspecialchars($cita["estado_cita"], ENT_QUOTES, "UTF-8") ?>
                                    </span>
                                </div>
                                <form method="POST" action="citas_modulos.php">
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
            </div>
        <?php endif; ?>

        <h2>Módulos de atención activos</h2>
        <?php if (empty($modulos)): ?>
            <div class="estado-vacio">
                <h3>Sin módulos activos</h3>
                <p>No se encontraron módulos habilitados en el catálogo.</p>
            </div>
        <?php else: ?>
            <ul>
                <?php foreach ($modulos as $mod): ?>
                    <li>
                        <strong><?= htmlspecialchars($mod["nombre_modulo"], ENT_QUOTES, "UTF-8") ?></strong>
                        <?= htmlspecialchars($mod["municipio"], ENT_QUOTES, "UTF-8") ?> —
                        <?= htmlspecialchars($mod["direccion"], ENT_QUOTES, "UTF-8") ?><br>
                        <small>Tel: <?= htmlspecialchars($mod["tel_modulo"], ENT_QUOTES, "UTF-8") ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
</body>
</html>