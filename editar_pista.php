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

// Obtener información actual de la pista
$result_pista = mysqli_query($conexion, "SELECT * FROM pista WHERE id_pista=$id_pista");
$pista = mysqli_fetch_assoc($result_pista);

// Verificar si la pista existe
if (!$pista) {
    cerrar_conexion($conexion);
    header("Location: administrador.php");
    exit();
}

// Procesamiento del formulario de modificación de pista
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modificar_pista"])) {
    $nuevo_nombre = $_POST["nuevo_nombre"];

    modificar_pista($conexion, $id_pista, $nuevo_nombre);

    // Cerrar la conexión
    cerrar_conexion($conexion);

    // Redirigir a la página de administrador
    header("Location: administrador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pista</title>
</head>
<body>

    <h2>Editar Pista</h2>

    <!-- Formulario para editar la pista -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_pista); ?>">
        <label for="nuevo_nombre">Nuevo Nombre de la Pista:</label>
        <input type="text" id="nuevo_nombre" name="nuevo_nombre" value="<?php echo $pista['nombre']; ?>" required>
        <br>
        <button type="submit" name="modificar_pista">Guardar Cambios</button>
    </form>

</body>
</html>