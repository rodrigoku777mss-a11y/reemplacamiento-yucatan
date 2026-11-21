<?php
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../registro.php");
    exit;
}

$curp = strtoupper(trim($_POST["curp"] ?? ""));
$nombre = trim($_POST["nombre"] ?? "");
$primerApellido = trim($_POST["primer_apellido"] ?? "");
$segundoApellido = trim($_POST["segundo_apellido"] ?? "");
$segundoApellido = $segundoApellido !== "" ? $segundoApellido : null;
$telefono = trim($_POST["telefono"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (
    strlen($curp) !== 18 ||
    $nombre === "" ||
    $primerApellido === "" ||
    $telefono === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($password) < 8
) {
    header("Location: ../registro.php?err=" . urlencode("Verifica que todos los campos obligatorios sean válidos."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();

    // Verificar si ya existe el correo o la CURP
    $stmtCheck = $pdo->prepare(
        "SELECT 1 FROM USUARIOS WHERE email = ?
         UNION
         SELECT 1 FROM PROPIETARIO WHERE curp = ? OR email = ?
         LIMIT 1"
    );
    $stmtCheck->execute([$email, $curp, $email]);

    if ($stmtCheck->fetch()) {
        header("Location: ../registro.php?err=" . urlencode("Ya existe una cuenta registrada con ese correo o CURP."));
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $pdo->beginTransaction();

    $stmtUsuario = $pdo->prepare(
        "INSERT INTO USUARIOS (email, password_hash, rol) VALUES (?, ?, 'ciudadano')"
    );
    $stmtUsuario->execute([$email, $passwordHash]);
    $nuevoUsuarioId = (int) $pdo->lastInsertId();

    $stmtPropietario = $pdo->prepare(
        "INSERT INTO PROPIETARIO (id_usuario, curp, nombre, primer_apellido, segundo_apellido, telefono, email)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmtPropietario->execute([
        $nuevoUsuarioId,
        $curp,
        $nombre,
        $primerApellido,
        $segundoApellido,
        $telefono,
        $email
    ]);

    $pdo->commit();

    header("Location: ../index.php?msg=" . urlencode("Registro completado. Ya puedes iniciar sesión."));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error en registro_procesar.php: " . $e->getMessage());
    header("Location: ../registro.php?err=" . urlencode("No se pudo completar el registro. Intenta nuevamente."));
    exit;
}

