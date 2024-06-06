<?php


session_start();  // Inicia la sesión para mantener la información del usuario entre páginas
include 'database.php';//Incluir el archivo para tener acceso a todas las funciones

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conectar a la base de datos
    $con = conectar();

    // Consultar la base de datos para verificar las credenciales
    $result = mysqli_query($con, "SELECT * FROM usuario WHERE nombre='$username'");
    
    if ($result) {
        $usuario = mysqli_fetch_assoc($result);

        // Verificar la contraseña
        if ($usuario && password_verify($password, $usuario["pass"])) {
            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["tipo"] = $usuario["tipo"];

            // Redirigir según el tipo de usuario
            if ($usuario["tipo"] == 1) {
                header("Location: usuario.php");
                exit();
            } elseif ($usuario["tipo"] == 0) {
                header("Location: administrador.php");
                exit();
            }
        } else {
            $error_message = "Usuario o contraseña no válido";
        }
    } else {
        die("Error en la consulta: " . mysqli_error($con));
    }

    // Cerrar la conexión
    cerrar_conexion($con);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Usuario</title>
</head>
<body>
    <h2>Inicio de Sesión</h2>
    
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>
