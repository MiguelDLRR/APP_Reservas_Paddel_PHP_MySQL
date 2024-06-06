<?php
session_start();
include 'database.php';

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo"] != 0) {
    header("Location: index.php");
    exit();
}

// Verificar si se proporciona un ID de pista válido en la URL
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: administrador.php");
    exit();
}

$id_pista = $_GET["id"];

// Conectar a la base de datos
$conexion = conectar();

// Borrar la pista
borrar_pista($conexion, $id_pista);

// Cerrar la conexión
cerrar_conexion($conexion);

// Redirigir a la página de administrador
header("Location: administrador.php");
exit();
?>