<?php

require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

$pdo = Conexion::obtenerConexion();
$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);

$modelos = $pdo->query("SELECT id_modelo, marca, nombre_modelo FROM MODELO ORDER BY marca, nombre_modelo")->fetchAll();

$stmt = $pdo->prepare(
    "SELECT v.num_serie, v.placa_actual, v.num_motor, v.anio, m.marca, m.nombre_modelo
     FROM VEHICULO AS v
     INNER JOIN MODELO AS m ON m.id_modelo = v.id_modelo
     WHERE v.id_propietario = ? AND v.activo = 1
     ORDER BY v.anio DESC"
);
$stmt->execute([$idPropietario]);
$vehiculos = $stmt->fetchAll();

$msg = $_GET["msg"] ?? "";
$err = $_GET["err"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Vehículos — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Mis Vehículos</h1>
        <nav>
            <a href="panel.php">Panel</a>
            <a href="vehiculos.php" class="activo">Mis Vehículos</a>
            <a href="tramites.php">Solicitar Trámite</a>
            <a href="citas.php">Agendar Cita</a>
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

        <h2>Registrar nuevo vehículo</h2>
        <form method="POST" action="../api/vehiculo_guardar.php">
            <label for="num_serie">Número de serie (NIV / VIN):</label>
            <input type="text" id="num_serie" name="num_serie" maxlength="17" required>

            <label for="placa_actual">Placa actual:</label>
            <input type="text" id="placa_actual" name="placa_actual" maxlength="15" required>

            <label for="num_motor">Número de motor:</label>
            <input type="text" id="num_motor" name="num_motor" maxlength="30" required>

            <label for="anio">Año:</label>
            <input type="number" id="anio" name="anio" min="1950" max="2027" required>

            <label for="id_modelo">Marca y modelo:</label>
            <select id="id_modelo" name="id_modelo" required>
                <option value="">Selecciona un modelo</option>
                <?php foreach ($modelos as $modelo): ?>
                    <option value="<?= (int) $modelo["id_modelo"] ?>">
                        <?= htmlspecialchars($modelo["marca"] . " " . $modelo["nombre_modelo"], ENT_QUOTES, "UTF-8") ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Guardar vehículo</button>
        </form>

        <h2>Vehículos registrados</h2>
        <?php if (empty($vehiculos)): ?>
            <div class="estado-vacio">
                <h3>Sin vehículos activos</h3>
                <p>Completa el formulario superior para registrar tu primer vehículo y poder iniciar tus trámites.</p>
            </div>
        <?php else: ?>
            <div class="tabla-contenedor">
                <table>
                    <tr>
                        <th>Serie</th>
                        <th>Placa actual</th>
                        <th>Motor</th>
                        <th>Año</th>
                        <th>Modelo</th>
                        <th>Acción</th>
                    </tr>
                    <?php foreach ($vehiculos as $v): ?>
                        <tr>
                            <td><small><?= htmlspecialchars($v["num_serie"], ENT_QUOTES, "UTF-8") ?></small></td>
                            <td><strong><?= htmlspecialchars($v["placa_actual"], ENT_QUOTES, "UTF-8") ?></strong></td>
                            <td><?= htmlspecialchars($v["num_motor"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= (int) $v["anio"] ?></td>
                            <td><?= htmlspecialchars($v["marca"] . " " . $v["nombre_modelo"], ENT_QUOTES, "UTF-8") ?></td>
                            <td>
                                <form method="POST" action="../api/vehiculo_eliminar.php">
                                    <input type="hidden" name="num_serie" value="<?= htmlspecialchars($v["num_serie"], ENT_QUOTES, "UTF-8") ?>">
                                    <button type="submit">Dar de baja</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>