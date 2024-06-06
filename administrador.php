<?php
session_start();
include 'database.php';

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo"] != 0) {
    header("Location: index.php");  // Redirigir a la página de inicio de sesión si no ha iniciado sesión como administrador
    exit();
}

$nombre_administrador = $_SESSION["nombre"];


//conectar a la base de datos

$conexion = conectar();

// Obtener la lista de usuarios
$result_usuarios = obtener_usuarios($conexion);

// Obtener las pistas
$result_pistas = obtener_pistas($conexion);

// Obtener las reservas
$result_reservas = obtener_reservas($conexion);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido, Administrador</title>
</head>
<body>
    <h2>Bienvenido, <?php echo $nombre_administrador; ?> (Administrador)</h2>
    <!-- Contenido específico para administradores -->
    <h2>Administración de Usuarios</h2>

    <!-- Mostrar la tabla de usuarios -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Acciones</th>
        </tr>
        <?php
        while ($usuario = mysqli_fetch_assoc($result_usuarios)) {
            echo "<tr>";
            echo "<td>{$usuario['id_usuario']}</td>";
            echo "<td>{$usuario['nombre']}</td>";
            echo "<td>{$usuario['tipo']}</td>";
            echo "<td><a href='editar_usuario.php?id={$usuario['id_usuario']}'>Editar</a> | <a href='borrar_usuario.php?id={$usuario['id_usuario']}'>Borrar</a></td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Formulario para dar de alta nuevos usuarios -->
    <h3>Dar de alta nuevo usuario</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>
        <label for="pass">Contraseña:</label>
        <input type="password" id="pass" name="pass" required>
        <br>
        <label for="tipo">Tipo (0=admin, 1=usuario):</label>
        <input type="number" id="tipo" name="tipo" min="0" max="1" required>
        <br>
        <button type="submit" name="alta_usuario">Dar de Alta</button>
    </form>
    
    <?php
    // Procesamiento del formulario para dar de alta nuevos usuarios
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["alta_usuario"])) {
        $nombre_nuevo = $_POST["nombre"];
        $pass_nuevo = $_POST["pass"];
        $tipo_nuevo = $_POST["tipo"];

        crear_usuario($conexion, $nombre_nuevo, $pass_nuevo, $tipo_nuevo);

        // Refrescar la página para mostrar la nueva información
        header("Location: administrador.php");
        exit();
    }
    ?>

    <hr>
    <h2>Gestión de las pistas</h2>
    <!-- Tabla para mostrar las pistas -->
    <table border="1">
        <thead>
            <tr>
                <th>ID Pista</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($pista = mysqli_fetch_assoc($result_pistas)) {
                echo "<tr>";
                echo "<td>{$pista['id_pista']}</td>";
                echo "<td>{$pista['nombre']}</td>";
                echo "<td><a href='editar_pista.php?id={$pista['id_pista']}'>Editar</a> | <a href='borrar_pista.php?id={$pista['id_pista']}'>Borrar</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <br>
    <!-- Formulario para dar de alta nuevas pistas -->
    <h3>Dar de alta nueva pista</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="nombre_pista">Nombre de la pista:</label>
    <input type="text" id="nombre_pista" name="nombre_pista" required>
    <br>
    <button type="submit" name="alta_pista">Agregar Pista</button>
</form>

    <?php
    // Procesamiento del formulario para dar de alta nuevas pistas
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["alta_pista"])) {
        $nombre_nueva_pista = $_POST["nombre_pista"];

        crear_pista($conexion, $nombre_nueva_pista);

        // Refrescar la página para mostrar la nueva información
        header("Location: administrador.php");
        exit();
    }
    ?>
<hr>


<h2>Gestión de reservas</h2>

<!-- Tabla para mostrar las reservas -->


<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table border="1">
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Usuario</th>
                    <th>Pista</th>
                    <th>Turno</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($reserva = mysqli_fetch_assoc($result_reservas)) {
                    echo "<tr>";
                    echo "<td>{$reserva['id_reserva']}</td>";
                    echo "<td>{$reserva['usuario']}</td>";
                    echo "<td>{$reserva['pista']}</td>";
                    echo "<td>{$reserva['turno']}</td>";
                    echo "<td><input type='checkbox' name='reservas_seleccionadas[]' value='{$reserva['id_reserva']}'> Seleccionar</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Botón para borrar reservas seleccionadas -->
        <button type="submit" name="borrar_reservas">Borrar Reservas Seleccionadas</button>

        <br>

        <!-- Botón para borrar todas las reservas -->
        <button type="submit" name="borrar_todas">Borrar Todas las Reservas</button>
    </form>

    <?php
    // Procesamiento del formulario para borrar reservas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrar_reservas"])) {
    if (isset($_POST["reservas_seleccionadas"]) && is_array($_POST["reservas_seleccionadas"])) {
        foreach ($_POST["reservas_seleccionadas"] as $id_reserva) {
            borrar_reserva($conexion, $id_reserva);
        }
    }
}

// Procesamiento del formulario para borrar todas las reservas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrar_todas"])) {
    borrar_todas_las_reservas($conexion);
}
    
    
    
    
    
    ?>

<hr>    
<!-- boton para cerrar sesion y volver al index.php -->
<form method="post" action="logout.php">
    <button type="submit" name="cerrar_sesion">Cerrar Sesión</button>
</form>
</body>
</html>