# APP_Reservas_Paddel_PHP_MySQL

Aplicación web en PHP para un club de pádel, en la que se puede gestionar los usuarios del club, las pistas y las reservas.

La aplicación tiene una base de daatos en MYSQL llamada "padel" con las siguientes tablas:

USUARIO
		id_usuario int llave primaria,
nombre varchar(255),
		pass varchar(255),
		tipo int (0 para administradores, 1 para usuarios normales)
	PISTA
		id_pista int llave primaria,
		nombre varchar(255)
	RESERVA
		id_reserva int llave primaria,
usuario int llave secundaria de USUARIO
		pista int llave secundaria de PISTA
		turno int

La aplicación consta de las siguientes funcionalidades:

1.La página principal es una pantalla de validación de usuario. Consta de un formulario con un campo de texto para introducir el nombre de usuario, otro campo de texto para introducir la contraseña, y un botón de entrada. A las siguientes páginas de la aplicación no se puede acceder si no se ha hecho login previamente.
2.Según el tipo de usuario que se haya logueado, se le redirecciona a la página de administrador o a la pantalla de usuario.
3.La pantalla de administrador presenta las siguientes partes:
  -Gestión de usuarios: se muestra una tabla con los usuarios del sistema desde donde se podrán modificar sus datos o         borrarlos. También hay un formulario para dar de alta nuevos usuarios.
  -Gestión de pistas: se muestra una tabla con las pistas del sistema desde donde se podrán modificar sus datos o             borrarlos. También hay un formulario para dar de alta nuevas pistas.
  -Gestión de reservas: se muestra una tabla con las reservas que hay hechas. Se podrán borrar reservas (una a una o          varias) y también encontramos un botón para borrar todas las reservas que hay.

4.La pantalla de usuario muestra una tabla con las reservas que tiene hechas el usuario en cuestión, desde donde podrá borrar sus reservas. También hay un formulario para crear nuevas reservas. El sistema comprueba que no se pueden hacer reservas de pista en un turno en el que ya esté ocupada. 
5.Todas las pantallas tienen un botón desde el que el usuario puede hacer logout.
6.La aplicación incorpora un sistema mediante el cual se controla el acceso a las páginas según los tipos de usuarios. Es decir, un usuario no podrá acceder a las operaciones de un administrador si no es de este tipo, y viceversa.

 






