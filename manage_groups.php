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
        $group_id = $_POST['group_id'];
        $group_name = $_POST['group_name'];
        $group_status = $_POST['group_status'];

        if ($action == "new") {
            /* inserisco i dati del nuovo gruppo e ne prendo l'id */
            $new_group_id = insert_group($group_name, $group_status);
            if ($new_group_id) {
                echo "Gruppo <b>$group_name</b> inserito con successo";
            }
        }
        else if ($action=="edit")
        {
            /* modifico il gruppo selezionato con i dati del form presente in fondo a questa pagina */
            $group_id = edit_group($group_id, $group_name, $group_status, $group_old_status);
            echo "Gruppo <b>$group_name</b> modificato con successo";
        }
        else if ($action=="group_delete" && trim ($group_id!=""))
        {
            /* cancellazione di un gruppo */
            // prendo il nome del gruppo
            $groups = get_groups($group_id);
            foreach ($groups as $group) $group_name = $group->get_name();
            // mi connetto al db e cancello
            $risu_del = delete_groups($group_id);
            if ($risu_del) {
                echo "Gruppo <b>$group_name</b> cancellato";
            } else {
                echo "Errore durante la cancellazione del Gruppo <b>$group_id</b>";
            }
        }
        else if ($action=="group_edit")
        {
            /* form per modificare il gruppo, devo recuperare i dati dal db */
            $groups = get_groups($group_id);
            foreach ($groups as $group) {
                $group_name = $group->get_name();
                $group_status = $group->get_status();
            }
            ?>
            <h2>Modifica Gruppo</h2>
            <form action="manage_groups.php" method="POST" name="group">

            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
            <input type="hidden" name="group_old_status" value="<?php echo $group_status; ?>">

            name: <input type="text" name="group_name" value="<?php echo $group_name ?>" /><br />
            status: <select name="group_status">
                    <option>1</option>
                    <option<?php if($group_status==0) echo " selected=\"selected\"" ?>>0</option>
                </select><br />

            <input type="submit" value="Submit" />
        </form>
        <?php
        }
        ?>
        <p><a href="index.php">Torna indietro</a></p>
    </body>
</html>
