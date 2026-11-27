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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Trámite — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Solicitar Trámite de Reemplacamiento</h1>
        <nav>
            <a href="panel.php">Panel</a>
            <a href="vehiculos.php">Mis Vehículos</a>
            <a href="tramites.php" class="activo">Solicitar Trámite</a>
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

        <h2>Tipos de trámite disponibles</h2>
        <section class="grid-opciones">
            <article class="tarjeta-opcion seleccionada" data-tramite="Canje de Placas">
                <span class="opcion-icono">🔄</span>
                <h3>Canje de Placas</h3>
                <p>Renovación de placas vigentes conforme al calendario estatal de reemplacamiento.</p>
            </article>
            <article class="tarjeta-opcion" data-tramite="Alta Vehicular">
                <span class="opcion-icono">🚘</span>
                <h3>Alta Vehicular</h3>
                <p>Registro inicial de vehículo nuevo o proveniente de otra entidad federativa.</p>
            </article>
            <article class="tarjeta-opcion" data-tramite="Reposición de Placas">
                <span class="opcion-icono">🛡️</span>
                <h3>Reposición de Placas</h3>
                <p>Solicitud de reposición por robo, extravío o deterioro físico de las láminas.</p>
            </article>
        </section>

        <h2>Datos de la solicitud</h2>
        <?php if (empty($vehiculos)): ?>
            <div class="estado-vacio">
                <h3>Aún no tienes vehículos registrados</h3>
                <p>Para iniciar un trámite primero debes dar de alta al menos un vehículo en tu cuenta.</p>
                <a href="vehiculos.php" class="btn-primario">Registrar mi primer vehículo →</a>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </main>

    <script>
        const tarjetas = document.querySelectorAll(".tarjeta-opcion");
        const selectTramite = document.getElementById("tipo_tramite");

        if (selectTramite) {
            tarjetas.forEach(tarjeta => {
                tarjeta.addEventListener("click", () => {
                    selectTramite.value = tarjeta.dataset.tramite;
                    tarjetas.forEach(t => t.classList.remove("seleccionada"));
                    tarjeta.classList.add("seleccionada");
                });
            });

            selectTramite.addEventListener("change", () => {
                tarjetas.forEach(t => {
                    t.classList.toggle("seleccionada", t.dataset.tramite === selectTramite.value);
                });
            });
        }
    </script>
</body>
</html>