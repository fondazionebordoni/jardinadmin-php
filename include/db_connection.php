<?php

/* Collegamento al database
 * prende i valori dalle variabili di sessione impostate su index.php
 * per cambiare i valori di default (casomai servisse) vedi index.php
 */

function db_get_connection() {

	$mysql_host = $_SESSION['mysql_host'];
	$mysql_user = $_SESSION['mysql_user'];
	$mysql_database = $_SESSION['mysql_database'];
	$mysql_password = $_SESSION['mysql_password'];

	$_SESSION['dbhost'] = $mysql_host;
	$_SESSION['dbname'] = $mysql_database;


	$link = mysql_connect($mysql_host, $mysql_user, $mysql_password);
	if (!$link) {
		session_unset();
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($mysql_database) or die('Could not select database');

	return $link;
}

?>
