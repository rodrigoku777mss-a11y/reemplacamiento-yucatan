<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("admin");

$pdo = Conexion::obtenerConexion();

$sql = "SELECT t.folio_tramite, t.tipo_tramite, t.estado_tramite, t.observaciones, t.fecha_inicio,
               v.num_serie, v.placa_actual, m.marca, m.nombre_modelo,
               p.nombre, p.primer_apellido, p.segundo_apellido, p.curp
        FROM TRAMITE AS t
        INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
        INNER JOIN MODELO AS m ON m.id_modelo = v.id_modelo
        INNER JOIN PROPIETARIO AS p ON p.id_propietario = v.id_propietario
        ORDER BY t.fecha_inicio DESC";

$tramites = $pdo->query($sql)->fetchAll();
$estadosPermitidos = ["Pendiente", "En Revisión", "Aprobado", "Rechazado", "Finalizado"];

$msg = $_GET["msg"] ?? "";
$err = $_GET["err"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisión de Trámites — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Revisión y Gestión de Trámites</h1>
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

        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Folio</th>
                <th>Ciudadano / CURP</th>
                <th>Vehículo</th>
                <th>Tipo</th>
                <th>Estado y Observaciones</th>
            </tr>
            <?php foreach ($tramites as $t): ?>
                <?php
                $propietarioNombre = trim($t["nombre"] . " " . $t["primer_apellido"] . " " . ($t["segundo_apellido"] ?? ""));
                ?>
                <tr>
                    <td><?= htmlspecialchars($t["folio_tramite"], ENT_QUOTES, "UTF-8") ?></td>
                    <td>
                        <?= htmlspecialchars($propietarioNombre, ENT_QUOTES, "UTF-8") ?><br>
                        <small><?= htmlspecialchars($t["curp"], ENT_QUOTES, "UTF-8") ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($t["placa_actual"] . " — " . $t["marca"] . " " . $t["nombre_modelo"], ENT_QUOTES, "UTF-8") ?><br>
                        <small><?= htmlspecialchars($t["num_serie"], ENT_QUOTES, "UTF-8") ?></small>
                    </td>
                    <td><?= htmlspecialchars($t["tipo_tramite"], ENT_QUOTES, "UTF-8") ?></td>
                    <td>
                        <form method="POST" action="../api/tramite_actualizar.php">
                            <input type="hidden" name="folio_tramite" value="<?= htmlspecialchars($t["folio_tramite"], ENT_QUOTES, "UTF-8") ?>">
                            <select name="estado_tramite" required>
                                <?php foreach ($estadosPermitidos as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado, ENT_QUOTES, "UTF-8") ?>" <?= $t["estado_tramite"] === $estado ? "selected" : "" ?>>
                                        <?= htmlspecialchars($estado, ENT_QUOTES, "UTF-8") ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br><br>
                            <input type="text" name="observaciones" placeholder="Observaciones del gestor" value="<?= htmlspecialchars($t["observaciones"] ?? "", ENT_QUOTES, "UTF-8") ?>">
                            <button type="submit">Actualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>