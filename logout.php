<?php
	session_start();
	
	session_destroy();
	echo "Saliendo de la aplicación...";
	header("refresh:2;url=index.php");
?>