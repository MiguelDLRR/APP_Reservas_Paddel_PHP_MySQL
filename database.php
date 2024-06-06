<?php
$host = "localhost";
$user = "root";
$pass = "admin";
$db_name = "padel";


function conectar(){
	$con = mysqli_connect($GLOBALS["host"], $GLOBALS["user"], $GLOBALS["pass"]) or die("Error al conectar con la base de datos");
	crear_bdd($con);
	mysqli_select_db($con, $GLOBALS["db_name"]);
	crear_tabla_usuario($con);
    
	crear_tabla_pista($con);
    crear_tabla_reserva($con);
	return $con;
}

function crear_bdd($con){
	$query= mysqli_query($con, "create database if not exists padel;");
    if (!$query) {
        die("Error al crear la base de datos: " . mysqli_error($con));
    }
}

function crear_tabla_usuario($con){
	mysqli_query($con, "create table if not exists usuario(
        id_usuario int primary key auto_increment, 
        nombre varchar(255), 
        pass varchar(255), 
        tipo int not null default 1 check (tipo IN (0, 1))
    );");
	if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM usuario")) == 0) {
        crear_usuario($con, 'Miguel', '1234', 1); // Usuario normal
        crear_usuario($con, 'Pedro', '1234', 0);  // Administrador
    }
}
//funcion para que no se cree un usuario si ya existe
function existe_usuario($con, $nombre) {
    $query = mysqli_query($con, "SELECT * FROM usuario WHERE nombre='$nombre'");
    return mysqli_num_rows($query) > 0;
}

function crear_usuario($con, $nombre, $pass, $tipo){
    if (existe_usuario($con, $nombre)) {
        die("El usuario ya existe. Por favor, elige otro nombre.");
    }
    
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    mysqli_query($con, "insert into usuario (nombre, pass, tipo) values ('$nombre', '$pass_hash', $tipo);");
    
}


function obtener_usuarios($con){

    $query= mysqli_query($con, "SELECT * FROM usuario");
    if(!$query){
        die("Error al obtener usuarios: " . mysqli_error($con));
    }
    return $query;
}

function modificar_usuario($con, $id_usuario, $nombre, $pass, $tipo){
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    $query = mysqli_query($con, "UPDATE usuario SET nombre='$nombre', pass='$pass_hash', tipo=$tipo WHERE id_usuario=$id_usuario");
    if (!$query) {
        die("Error al modificar usuario: " . mysqli_error($con));
    }
}

function borrar_usuario($con, $id_usuario) {
    $query = mysqli_query($con, "DELETE FROM usuario WHERE id_usuario=$id_usuario");
    if (!$query) {
        die("Error al borrar usuario: " . mysqli_error($con));
    }
}

function crear_tabla_pista($con){

    mysqli_query($con, "create table if not exists pista(
        id_pista int primary key auto_increment,
        nombre varchar(255)

    );");

    if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM pista")) == 0) {
        crear_pista($con, 'Roja'); 
        crear_pista($con, 'Azul');  
    }
    
}

function existe_pista($con, $nombre){

    $query = mysqli_query($con, "SELECT * FROM pista WHERE nombre='$nombre'");
    return mysqli_num_rows($query) > 0;

}
function crear_pista($con, $nombre){
    if (existe_pista($con, $nombre)) {
        die("El nombre de la pista ya existe. Por favor, elige otro nombre.");
    }
      
    mysqli_query($con, "insert into pista (nombre) values ('$nombre');");

}

function obtener_pistas($con){
    $query= mysqli_query($con, "SELECT * FROM pista");
    if(!$query){
        die("Error al obtener las pistas: " . mysqli_error($con));
    }
    return $query;

}

function modificar_pista($con, $id_pista, $nombre) {
    if (existe_pista($con, $nombre, $id_pista)) {
        die("El nombre de la pista ya existe. Por favor, elige otro nombre.");
    }

    mysqli_query($con, "UPDATE pista SET nombre='$nombre' WHERE id_pista=$id_pista");

    if (mysqli_error($con)) {
        die("Error al modificar la pista: " . mysqli_error($con));
    }
}

function borrar_pista($con, $id_pista) {
    mysqli_query($con, "DELETE FROM pista WHERE id_pista=$id_pista");

    if (mysqli_error($con)) {
        die("Error al borrar la pista: " . mysqli_error($con));
    }
}

function crear_tabla_reserva($con){

    mysqli_query($con, "create table if not exists reserva(
        id_reserva int primary key auto_increment, 
        usuario int,
        pista int,
        turno int,
        FOREIGN KEY (usuario) REFERENCES usuario(id_usuario),
        FOREIGN KEY (pista) REFERENCES pista(id_pista) 
    );");

        
}

function obtener_reservas($con){

    $query = mysqli_query($con, "SELECT r.id_reserva, u.nombre as usuario, p.nombre as pista, r.turno
    FROM reserva r
    JOIN usuario u ON r.usuario = u.id_usuario
    JOIN pista p ON r.pista = p.id_pista");

    if (!$query) {

        die("Error al obtener reservas: " . mysqli_error($con));

    }

    return $query; 
}

function borrar_reserva($con, $id_reserva){

    $query = mysqli_query($con, "DELETE FROM reserva WHERE id_reserva=$id_reserva");
    if (!$query) {

        die("Error al borrar reserva: " . mysqli_error($con));

    }
}

function borrar_todas_las_reservas($con){

    $query = mysqli_query($con, "DELETE FROM reserva");
    if (!$query) {
        die("Error al borrar todas las reservas: " . mysqli_error($con));
    }
}

function obtener_reservas_usuario($con, $id_usuario) {
    $query = mysqli_query($con, "SELECT r.id_reserva, p.nombre as pista, r.turno
                                  FROM reserva r
                                  JOIN pista p ON r.pista = p.id_pista
                                  WHERE r.usuario = $id_usuario");
    
    if (!$query) {
        die("Error al obtener reservas del usuario: " . mysqli_error($con));
    }

    return $query;
}

function crear_reserva($con, $id_usuario, $id_pista, $turno) {
    // Comprobar si la pista está disponible en el turno seleccionado
    $query_disponible = mysqli_query($con, "SELECT * FROM reserva
                                            WHERE pista = $id_pista
                                            AND turno = $turno");

    if (!$query_disponible || mysqli_num_rows($query_disponible) > 0) {
        echo "La pista ya está reservada en ese turno. Por favor, elige otro turno o pista.";
        header("refresh:2; url=usuario.php");
        exit();
    }

    // Insertar la reserva
    $query_insertar = mysqli_query($con, "INSERT INTO reserva (usuario, pista, turno)
                                           VALUES ($id_usuario, $id_pista, $turno)");

    if (!$query_insertar) {
        die("Error al crear reserva: " . mysqli_error($con));
    }
}

function borrar_reserva_usuario($con, $id_reserva, $id_usuario) {
    $query = mysqli_query($con, "DELETE FROM reserva WHERE id_reserva=$id_reserva AND usuario=$id_usuario");

    if (!$query) {
        die("Error al borrar reserva: " . mysqli_error($con));
    }
}


function cerrar_conexion($con){
	mysqli_close($con);
}
?>