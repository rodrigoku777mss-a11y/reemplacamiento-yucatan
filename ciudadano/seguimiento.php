<?php
// ciudadano/seguimiento.php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("ciudadano");

$pdo = Conexion::obtenerConexion();
$idPropietario = (int) ($_SESSION["id_propietario"] ?? 0);

$stmt = $pdo->prepare(
    "SELECT t.folio_tramite, t.tipo_tramite, t.estado_tramite, t.observaciones, t.fecha_inicio,
            v.placa_actual, m.marca, m.nombre_modelo,
            c.fecha_programada, c.hora, c.estado_cita, mod_cat.nombre_modulo
     FROM TRAMITE AS t
     INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
     INNER JOIN MODELO AS m ON m.id_modelo = v.id_modelo
     LEFT JOIN CITA AS c ON c.folio_tramite = t.folio_tramite
     LEFT JOIN MODULO AS mod_cat ON mod_cat.id_modulo = c.id_modulo
     WHERE v.id_propietario = ?
     ORDER BY t.fecha_inicio DESC"
);
$stmt->execute([$idPropietario]);
$registros = $stmt->fetchAll();

function claseBadgeEstado(string $estado): string {
    return match ($estado) {
        "Pendiente"   => "badge-pendiente",
        "En Revisión" => "badge-revision",
        "Aprobado"    => "badge-aprobado",
        "Rechazado"   => "badge-rechazado",
        "Finalizado"  => "badge-finalizado",
        default       => "badge-finalizado",
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Trámites — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Seguimiento de Trámites y Citas</h1>
        <nav>
            <a href="panel.php">Panel</a>
            <a href="vehiculos.php">Mis Vehículos</a>
            <a href="tramites.php">Solicitar Trámite</a>
            <a href="citas.php">Agendar Cita</a>
            <a href="seguimiento.php" class="activo">Seguimiento</a>
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Historial de solicitudes</h2>

        <?php if (empty($registros)): ?>
            <div class="estado-vacio">
                <h3>Sin trámites registrados</h3>
                <p>Cuando generes una solicitud de reemplacamiento podrás consultar aquí su avance y tu cita.</p>
                <a href="tramites.php" class="btn-primario">Solicitar trámite →</a>
            </div>
        <?php else: ?>
            <div class="tabla-contenedor">
                <table>
                    <tr>
                        <th>Folio</th>
                        <th>Vehículo</th>
                        <th>Tipo</th>
                        <th>Estado Trámite</th>
                        <th>Observaciones</th>
                        <th>Cita</th>
                        <th>Módulo</th>
                    </tr>
                    <?php foreach ($registros as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r["folio_tramite"], ENT_QUOTES, "UTF-8") ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($r["placa_actual"], ENT_QUOTES, "UTF-8") ?></strong><br>
                                <small><?= htmlspecialchars($r["marca"] . " " . $r["nombre_modelo"], ENT_QUOTES, "UTF-8") ?></small>
                            </td>
                            <td><?= htmlspecialchars($r["tipo_tramite"], ENT_QUOTES, "UTF-8") ?></td>
                            <td>
                                <span class="badge <?= claseBadgeEstado($r["estado_tramite"]) ?>">
                                    <?= htmlspecialchars($r["estado_tramite"], ENT_QUOTES, "UTF-8") ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($r["observaciones"] ?? "Sin observaciones", ENT_QUOTES, "UTF-8") ?></td>
                            <td>
                                <?php if ($r["fecha_programada"]): ?>
                                    <?= htmlspecialchars($r["fecha_programada"] . " " . $r["hora"], ENT_QUOTES, "UTF-8") ?><br>
                                    <small><?= htmlspecialchars($r["estado_cita"], ENT_QUOTES, "UTF-8") ?></small>
                                <?php else: ?>
                                    <small>Sin cita agendada</small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r["nombre_modulo"] ?? "—", ENT_QUOTES, "UTF-8") ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

