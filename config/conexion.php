<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Conexion
{
    private static string $host = "localhost";
    private static string $dbname = "reemplacamiento";
    private static string $user = "root";
    private static string $pass = "";
    private static string $charset = "utf8mb4";

    private static ?PDO $instancia = null;

    private function __construct()
    {
    }

    public static function obtenerConexion(): PDO
    {
        if (self::$instancia === null) {
            $dsn = "mysql:host=" . self::$host
                 . ";dbname=" . self::$dbname
                 . ";charset=" . self::$charset;

            try {
                self::$instancia = new PDO(
                    $dsn,
                    self::$user,
                    self::$pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log("Error de conexión a reemplacamiento: " . $e->getMessage());
                die("No se pudo conectar a la base de datos. Intente más tarde.");
            }
        }

        return self::$instancia;
    }
}