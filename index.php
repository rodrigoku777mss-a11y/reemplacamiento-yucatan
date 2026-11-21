<?php

require_once "config/conexion.php";
require_once "config/auth.php";

redirigirSiAutenticado();

$mensaje = $_GET["msg"] ?? "";
$error = $_GET["err"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal de Reemplacamiento Vehicular de Yucatán — Iniciar Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <main class="contenedor-auth">
        <h1>Portal Web de Reemplacamiento Vehicular de Yucatán</h1>
        <p>Inicia sesión para acceder a tu módulo de ciudadano o gestor.</p>

        <?php if ($mensaje !== ""): ?>
            <p class="alerta-exito"><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <p class="alerta-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <form method="POST" action="api/login_procesar.php">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>

        <p>¿No tienes cuenta? <a href="registro.php">Regístrate como ciudadano</a></p>
    </main>
</body>
</html>
