<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaAutenticado(): bool
{
    return isset($_SESSION["id_usuario"], $_SESSION["rol"]);
}

function requerirSesion(string $loginUrl = "../index.php"): void
{
    if (!estaAutenticado()) {
        header("Location: " . $loginUrl);
        exit;
    }
}

function requerirRol(string $rolRequerido, string $loginUrl = "../index.php"): void
{
    requerirSesion($loginUrl);

    if ($_SESSION["rol"] !== $rolRequerido) {
        if ($_SESSION["rol"] === "admin") {
            header("Location: ../admin/panel.php");
        } else {
            header("Location: ../ciudadano/panel.php");
        }
        exit;
    }
}

function redirigirSiAutenticado(): void
{
    if (estaAutenticado()) {
        if ($_SESSION["rol"] === "admin") {
            header("Location: admin/panel.php");
        } else {
            header("Location: ciudadano/panel.php");
        }
        exit;
    }
}