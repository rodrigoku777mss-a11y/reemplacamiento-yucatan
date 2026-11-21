<?php

require_once "config/conexion.php";
require_once "config/auth.php";

redirigirSiAutenticado();

$error = $_GET["err"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Ciudadano — Reemplacamiento Yucatán</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <main class="contenedor-auth">
        <h1>Registro de Ciudadano</h1>
        <p>Crea tu cuenta y registra tus datos como propietario de vehículo.</p>

        <?php if ($error !== ""): ?>
            <p class="alerta-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <form method="POST" action="api/registro_procesar.php">
            <label for="curp">CURP (18 caracteres):</label>
            <input type="text" id="curp" name="curp" maxlength="18" required>

            <label for="nombre">Nombre(s):</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="primer_apellido">Primer apellido:</label>
            <input type="text" id="primer_apellido" name="primer_apellido" required>

            <label for="segundo_apellido">Segundo apellido (opcional):</label>
            <input type="text" id="segundo_apellido" name="segundo_apellido">

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" maxlength="15" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña (mínimo 8 caracteres):</label>
            <input type="password" id="password" name="password" minlength="8" required>

            <button type="submit">Crear cuenta</button>
        </form>

        <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
    </main>
</body>
</html>