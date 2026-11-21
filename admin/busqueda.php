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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Búsqueda por Folio, Placa o CURP</h1>
        <nav>
            <a href="panel.php">Panel</a> |
            <a href="tramites.php">Revisión de Trámites</a> |
            <a href="citas_modulos.php">Citas y Módulos</a> |
            <a href="busqueda.php">Búsqueda</a> |
            <a href="../api/logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <form method="GET" action="busqueda.php">
            <label for="q">Buscar (folio, placa o CURP):</label>
            <input type="text" id="q" name="q" value="<?= htmlspecialchars($termino, ENT_QUOTES, "UTF-8") ?>" required>
            <button type="submit">Buscar</button>
        </form>

        <?php if ($termino !== ""): ?>
            <h2>Resultados para "<?= htmlspecialchars($termino, ENT_QUOTES, "UTF-8") ?>"</h2>
            <table border="1" cellpadding="8" cellspacing="0">
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
                        <td><?= htmlspecialchars($r["folio_tramite"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($r["curp"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($r["nombre"] . " " . $r["primer_apellido"] . " " . ($r["segundo_apellido"] ?? ""), ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($r["placa_actual"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($r["num_serie"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($r["estado_tramite"], ENT_QUOTES, "UTF-8") ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>