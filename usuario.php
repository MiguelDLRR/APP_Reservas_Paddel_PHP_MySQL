<?php
session_start();
include 'database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo"] != 1) {
    header("Location: index.php");  // Redirigir a la página de inicio de sesión si no ha iniciado sesión como usuario
    exit();
}

$nombre_usuario = $_SESSION["nombre"];

// Cerrar sesión si se ha hecho clic en el botón
if (isset($_POST["cerrar_sesion"])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Conectar a la base de datos
$conexion = conectar();

// Obtener las reservas del usuario
$id_usuario = $_SESSION["id_usuario"];
$result_reservas_usuario = obtener_reservas_usuario($conexion, $id_usuario);

// Procesamiento del formulario para borrar reservas del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrar_reserva"])) {
    $id_reserva = $_POST["id_reserva"];
    borrar_reserva_usuario($conexion, $id_reserva, $id_usuario);

    // Redirigir a la misma página después de borrar la reserva
    header("Location: usuario.php");
    exit();
}

// Procesamiento del formulario para crear nueva reserva
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["crear_reserva"])) {
    $id_pista = $_POST["id_pista"];
    $turno = $_POST["turno"];
    
    crear_reserva($conexion, $id_usuario, $id_pista, $turno);

    // Redirigir a la misma página después de crear la reserva
    header("Location: usuario.php");
    exit();
}


?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido, Usuario</title>
</head>
<body>
    <h2>Bienvenido, <?php echo $nombre_usuario; ?> (Usuario)</h2>
    <!-- Contenido específico para usuarios -->
    <hr>
    <h2>Mis Reservas</h2>

    <!-- Tabla para mostrar las reservas del usuario -->
    <table border="1">
        <thead>
            <tr>
                <th>ID Reserva</th>
                <th>Pista</th>
                <th>Turno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($reserva_usuario = mysqli_fetch_assoc($result_reservas_usuario)) {
                echo "<tr>";
                echo "<td>{$reserva_usuario['id_reserva']}</td>";
                echo "<td>{$reserva_usuario['pista']}</td>";
                echo "<td>{$reserva_usuario['turno']}</td>";
                echo "<td>
                        <form method='post' action='{$_SERVER["PHP_SELF"]}'>
                            <input type='hidden' name='id_reserva' value='{$reserva_usuario['id_reserva']}'>
                            <button type='submit' name='borrar_reserva'>Borrar Reserva</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <br>

    <!-- Formulario para crear nueva reserva -->
    <h3>Crear Nueva Reserva</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="id_pista">Seleccionar Pista:</label>
        <select id="id_pista" name="id_pista" required>

            <!-- Lista de pistas desde la base de datos -->
            <?php
            $result_pistas = mysqli_query($conexion, "SELECT * FROM pista");
            while ($pista = mysqli_fetch_assoc($result_pistas)) {
                echo "<option value='{$pista['id_pista']}'>{$pista['nombre']}</option>";
            }
            ?>
        </select>
        <br>
        <label for="turno">Seleccionar Turno:</label>
        <select id="turno" name="turno" required>
            <option value="1">Turno 1</option>
            <option value="2">Turno 2</option>
            
        </select>
        <br>
        <button type="submit" name="crear_reserva">Crear Reserva</button>
    </form>

    <!-- boton para cerrar sesion y volver al index.php -->
    <form method="post" action="logout.php">
        <button type="submit" name="cerrar_sesion">Cerrar Sesión</button>
    </form>
</body>
</html>
