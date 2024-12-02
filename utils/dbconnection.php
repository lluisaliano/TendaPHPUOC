<?php
	$user = "tenda_uoc";
	$password = "lalianouoc";
	$server = "localhost";
	$database = "tenda";
		
	$connection = mysqli_connect( $server, $user, $password ) or die ("Error conectant al servidor");
	$db = mysqli_select_db ( $connection, $database ) or die ("Error conectant a la base de dades");
?>
