<?php
session_start();
include 'database.php';

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo"] != 0) {
    header("Location: index.php");  // Redirigir a la página de inicio de sesión si no ha iniciado sesión como administrador
    exit();
}

// Conectar a la base de datos
$conexion = conectar();

// Verificar si se ha proporcionado un ID de usuario válido
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id_usuario_a_borrar = $_GET["id"];

    // Llamar a la función para borrar usuario
    borrar_usuario($conexion, $id_usuario_a_borrar);

    // Redirigir de nuevo a administrador.php después de borrar el usuario
    header("Location: administrador.php");
    exit();
} else {
    // Si no se proporciona un ID de usuario válido, redirigir a administrador.php
    header("Location: administrador.php");
    exit();
}

// Cerrar la conexión
cerrar_conexion($conexion);
?>