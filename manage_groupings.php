<?php
session_start();
include_once 'include/db_utils.php';
//PRINT_R($_POST);
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
        $grouping_id = $_POST['grouping_id'];
        $grouping_name = $_POST['grouping_name'];
        $grouping_alias = $_POST['grouping_alias'];

        if ($action == "new") {
            /* inserisco i dati del nuovo raggruppamento e ne prendo l'id */
            $new_grouping_id = insert_grouping($grouping_name, $grouping_alias);
            if ($new_grouping_id) {
                echo "<br>Raggruppamento <b>$grouping_alias</b> inserito con successo";
            }
        }
        else if ($action=="edit")
        {
            /* modifico il raggruppamento selezionato con i dati del form presente in fondo a questa pagina */
            $grouping_id = edit_grouping($grouping_id, $grouping_name, $grouping_alias);
            echo "<br>Raggruppamento <b>$grouping_alias</b> modificato con successo";
        }
        else if ($action=="grouping_delete" && trim ($grouping_id!=""))
        {
            /* cancellazione di un raggruppamento */
            // prendo il nome del raggruppamento
            $groupings = get_groupings($grouping_id);
            foreach ($groupings as $group) $grouping_alias = $group->get_alias();
            // mi connetto al db e cancello
            $risu_del = delete_grouping($grouping_id);
            if ($risu_del) {
                echo "<br>Raggruppamento <b>$grouping_alias</b> cancellato";
            } else {
                echo "Errore durante la cancellazione del raggruppamento <b>$grouping_id</b>";
            }
        }
        else if ($action=="grouping_edit")
        {
            /* form per modificare il raggruppamento, devo recuperare i dati dal db */
            $groupings = get_groupings($grouping_id);
            foreach ($groupings as $group) {
                $grouping_name = $group->get_name();
                $grouping_alias = $group->get_alias();
            }
            ?>
            <h2>Modifica raggruppamento</h2>
            <form action="manage_groupings.php" method="POST" name="group">

            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="grouping_id" value="<?php echo $grouping_id; ?>">

            name: <input type="text" name="grouping_name" value="<?php echo $grouping_name ?>" /><br />
            alias: <input type="text" name="grouping_alias" value="<?php echo $grouping_alias ?>" /><br />

            <input type="submit" value="Submit" />
        </form>
        <?php
        }
        ?>
        <p><a href="index.php">Torna indietro</a></p>
    </body>
</html>
