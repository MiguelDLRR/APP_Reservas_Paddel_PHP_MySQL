<?php
session_start();
include 'database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit();
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST["id_usuario"];
    $nombre = $_POST["nombre"];
    $pass = $_POST["pass"];
    $tipo = $_POST["tipo"];

    // Conectar a la base de datos
    $conexion = conectar();

    // Llamar a la función para modificar el usuario
    modificar_usuario($con, $id_usuario, $nombre, $pass, $tipo);

    // Cerrar la conexión
    cerrar_conexion($conexion);

    // Redirigir a la página de administrador o usuario (según el tipo)
    if ($_SESSION["tipo"] == 0) {
        header("Location: administrador.php");
    } else {
        header("Location: usuario.php");
    }
    exit();
}

// Obtener el ID del usuario a editar desde la URL
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id_usuario_a_editar = $_GET["id"];

    // Conectar a la base de datos
    $conexion = conectar();

    // Obtener la información del usuario a editar
    $result_usuario = mysqli_query($conexion, "SELECT * FROM usuario WHERE id_usuario = $id_usuario_a_editar");

    if (!$result_usuario || mysqli_num_rows($result_usuario) == 0) {
        // Usuario no encontrado, redirigir a la página de administrador o usuario
        if ($_SESSION["tipo"] == 0) {
            header("Location: administrador.php");
        } else {
            header("Location: usuario.php");
        }
        exit();
    }

    $usuario = mysqli_fetch_assoc($result_usuario);

    // Verificar si el usuario tiene permisos para editar
    if ($_SESSION["tipo"] != 0 && $_SESSION["id_usuario"] != $usuario["id_usuario"]) {
        // Usuario no tiene permisos, redirigir a la página de administrador o usuario
        if ($_SESSION["tipo"] == 0) {
            header("Location: administrador.php");
        } else {
            header("Location: usuario.php");
        }
        exit();
    }

    // Cerrar la conexión
    cerrar_conexion($conexion);
} else {
    // Si no se proporciona un ID de usuario válido, redirigir a la página de administrador o usuario
    if ($_SESSION["tipo"] == 0) {
        header("Location: administrador.php");
    } else {
        header("Location: usuario.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>
<body>
    <h2>Editar Usuario</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
        
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
        <br>
        
        <label for="pass">Contraseña:</label>
        <input type="password" id="pass" name="pass" required>
        <br>

        <label for="tipo">Tipo (0=admin, 1=usuario):</label>
        <input type="number" id="tipo" name="tipo" min="0" max="1" value="<?php echo $usuario['tipo']; ?>" required>
        <br>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>