<?php

require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../index.php?err=" . urlencode("Ingresa un correo y contraseña válidos."));
    exit;
}

try {
    $pdo = Conexion::obtenerConexion();

    $sql = "SELECT u.id_usuario, u.email, u.password_hash, u.rol, u.activo,
                   p.id_propietario, p.nombre, p.primer_apellido, p.segundo_apellido
            FROM USUARIOS AS u
            LEFT JOIN PROPIETARIO AS p ON p.id_usuario = u.id_usuario
            WHERE u.email = ?
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || (int) $usuario["activo"] !== 1 || !password_verify($password, $usuario["password_hash"])) {
        header("Location: ../index.php?err=" . urlencode("Credenciales incorrectas o cuenta inactiva."));
        exit;
    }

    session_regenerate_id(true);

    $_SESSION["id_usuario"] = (int) $usuario["id_usuario"];
    $_SESSION["email"] = $usuario["email"];
    $_SESSION["rol"] = $usuario["rol"];
    $_SESSION["id_propietario"] = $usuario["id_propietario"] !== null ? (int) $usuario["id_propietario"] : null;

    $nombreCompleto = trim(
        ($usuario["nombre"] ?? "") . " " .
        ($usuario["primer_apellido"] ?? "") . " " .
        ($usuario["segundo_apellido"] ?? "")
    );
    $_SESSION["nombre_usuario"] = $nombreCompleto !== "" ? $nombreCompleto : $usuario["email"];

    if ($usuario["rol"] === "admin") {
        header("Location: ../admin/panel.php");
    } else {
        header("Location: ../ciudadano/panel.php");
    }
    exit;
} catch (PDOException $e) {
    error_log("Error en login_procesar.php: " . $e->getMessage());
    header("Location: ../index.php?err=" . urlencode("Ocurrió un error al iniciar sesión."));
    exit;
}