<?php
require_once "../config/conexion.php";
require_once "../config/auth.php";
requerirRol("admin");

$pdo = Conexion::obtenerConexion();
$termino = trim($_GET["q"] ?? "");
$resultados = [];

if ($termino !== "") {
    $like = "%" . $termino . "%";
    $sql = "SELECT t.folio_tramite, t.tipo_tramite, t.estado_tramite,
                   v.num_serie, v.placa_actual,
                   p.curp, p.nombre, p.primer_apellido, p.segundo_apellido
            FROM TRAMITE AS t
            INNER JOIN VEHICULO AS v ON v.num_serie = t.num_serie
            INNER JOIN PROPIETARIO AS p ON p.id_propietario = v.id_propietario
            WHERE t.folio_tramite LIKE ?
               OR v.placa_actual LIKE ?
               OR p.curp LIKE ?
            ORDER BY t.fecha_inicio DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$like, $like, $like]);
    $resultados = $stmt->fetchAll();
}

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
    <title>Búsqueda — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Búsqueda por Folio, Placa o CURP</h1>
        <nav>
            <a href="panel.php">Panel</a>
            <a href="tramites.php">Revisión de Trámites</a>
            <a href="citas_modulos.php">Citas y Módulos</a>
            <a href="busqueda.php" class="activo">Búsqueda</a>
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Consultar expedientes</h2>
        <form method="GET" action="busqueda.php">
            <label for="q">Buscar (folio, placa o CURP):</label>
            <input type="text" id="q" name="q" placeholder="Ej. FOL-001, YUC-123-A o CURP..." value="<?= htmlspecialchars($termino, ENT_QUOTES, "UTF-8") ?>" required>
            <button type="submit">Buscar</button>
        </form>

        <?php if ($termino !== ""): ?>
            <h2>Resultados para "<?= htmlspecialchars($termino, ENT_QUOTES, "UTF-8") ?>"</h2>
            <?php if (empty($resultados)): ?>
                <div class="estado-vacio">
                    <h3>Sin coincidencias</h3>
                    <p>No se encontró ningún expediente que coincida con <strong><?= htmlspecialchars($termino, ENT_QUOTES, "UTF-8") ?></strong>. Verifica los datos e intenta nuevamente.</p>
                </div>
            <?php else: ?>
                <div class="tabla-contenedor">
                    <table>
                        <tr>
                            <th>Folio</th>
                            <th>CURP</th>
                            <th>Propietario</th>
                            <th>Placa</th>
                            <th>Serie</th>
                            <th>Estado</th>
                        </tr>
                        <?php foreach ($resultados as $r): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($r["folio_tramite"], ENT_QUOTES, "UTF-8") ?></strong></td>
                                <td><small><?= htmlspecialchars($r["curp"], ENT_QUOTES, "UTF-8") ?></small></td>
                                <td><?= htmlspecialchars(trim($r["nombre"] . " " . $r["primer_apellido"] . " " . ($r["segundo_apellido"] ?? "")), ENT_QUOTES, "UTF-8") ?></td>
                                <td><strong><?= htmlspecialchars($r["placa_actual"], ENT_QUOTES, "UTF-8") ?></strong></td>
                                <td><small><?= htmlspecialchars($r["num_serie"], ENT_QUOTES, "UTF-8") ?></small></td>
                                <td>
                                    <span class="badge <?= claseBadgeEstado($r["estado_tramite"]) ?>">
                                        <?= htmlspecialchars($r["estado_tramite"], ENT_QUOTES, "UTF-8") ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>