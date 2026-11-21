<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

$pdo = Conexion::obtenerConexion();
$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);

$stmtVehiculos = $pdo->prepare(
    "SELECT v.num_serie, v.placa_actual, m.marca, m.nombre_modelo
     FROM VEHICULO AS v
     INNER JOIN MODELO AS m ON m.id_modelo = v.id_modelo
     WHERE v.id_propietario = ? AND v.activo = 1"
);
$stmtVehiculos->execute([$idPropietario]);
$vehiculos = $stmtVehiculos->fetchAll();

$msg = $_GET["msg"] ?? "";
$err = $_GET["err"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Trámite — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Solicitar Trámite de Reemplacamiento</h1>
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
        <?php if ($msg !== ""): ?>
            <p class="alerta-exito"><?= htmlspecialchars($msg, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>
        <?php if ($err !== ""): ?>
            <p class="alerta-error"><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <form method="POST" action="../api/tramite_crear.php">
            <label for="num_serie">Vehículo:</label>
            <select id="num_serie" name="num_serie" required>
                <option value="">Selecciona uno de tus vehículos</option>
                <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= htmlspecialchars($v["num_serie"], ENT_QUOTES, "UTF-8") ?>">
                        <?= htmlspecialchars($v["placa_actual"] . " — " . $v["marca"] . " " . $v["nombre_modelo"] . " (" . $v["num_serie"] . ")", ENT_QUOTES, "UTF-8") ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="tipo_tramite">Tipo de trámite:</label>
            <select id="tipo_tramite" name="tipo_tramite" required>
                <option value="Canje de Placas">Canje de Placas</option>
                <option value="Alta Vehicular">Alta Vehicular</option>
                <option value="Reposición de Placas">Reposición de Placas</option>
            </select>

            <button type="submit">Generar trámite</button>
        </form>
    </main>
</body>
</html>