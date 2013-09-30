<?php
session_start();
include_once 'include/db_utils.php';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
        <a href="index.php">torna all'inizio</a>
        <?php
        $action = $_POST['action'];

        if ($action == "new") {

            /* Prendi le variabili del resultset dalle variabili di POST */
            $id_resultset = $_POST['resultset_id'];
        	$notify_name = $_POST['notify_name'];
        	$address_statement = $_POST['address_statement'];
            $data_statement = $_POST['data_statement'];
            $xslt = $_POST['xslt'];
            $bmdid = $_POST['bmdid'];

            $notify = new Notify($id_resultset, $notify_name, $address_statement, $data_statement, $xslt, "", $bmdid);
            
            /* Salva il resultset nella tabella resultset */
            insert_notify($notify);

        } elseif ($action == "delete") {
            $id = $_POST['id'];
            remove_notify_by_id($id);
        }

        ?>
        <p>Operazione eseguita correttamente. <a href='index.php'>Torna indietro</a></p>
    </body>
</html>
