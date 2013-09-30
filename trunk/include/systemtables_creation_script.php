<?php
session_start();
include 'db_utils.php';

$connection = db_get_connection();
import_file("jardin_systemtable_creation.sql", $connection);
mysql_close($connection);

echo "tabelle di sistema create correttamente...redirect in corso...";
header('Refresh: 3;url=../index.php');
//echo "<a href=../index.php>Login</a>";
?>
