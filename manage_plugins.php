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
        $plugin_id = $_POST['plugin_id'];
        $plugin_name = $_POST['plugin_name'];
        $plugin_type = $_POST['plugin_type'];
        $plugin_configurationfile = $_POST['plugin_configurationfile'];
        $plugin_note = $_POST['plugin_note'];

        if ($action == "new") {
            /* inserisco i dati del nuovo raggruppamento e ne prendo l'id */
            $new_plugin_id = insert_plugin($plugin_name, $plugin_configurationfile, $plugin_type, $plugin_note);
            if ($new_plugin_id) {
                echo "<br>Plugin <b>$plugin_name</b> inserito con successo";
            }
        }
        else if ($action=="edit")
        {
            /* modifico il raggruppamento selezionato con i dati del form presente in fondo a questa pagina */
            $plugin_id = edit_plugin($plugin_id, $plugin_name, $plugin_configurationfile, $plugin_type, $plugin_note);
            echo "<br>Raggruppamento <b>$plugin_name</b> modificato con successo";
        }
        else if ($action=="plugin_delete" && trim ($plugin_id!=""))
        {
            /* cancellazione di un raggruppamento */
            // prendo il nome del raggruppamento
            $plugins = get_plugins($plugin_id);
            foreach ($plugins as $plugin) $plugin_name = $plugin->get_name();
            // mi connetto al db e cancello
            $risu_del = delete_plugin($plugin_id);
            if ($risu_del) {
                echo "<br>Plugin <b>$plugin_name</b> cancellato";
            } else {
                echo "Errore durante la cancellazione del plugin <b>$plugin_id</b>";
            }
        }
        else if ($action=="plugin_edit")
        {
            /* form per modificare il raggruppamento, devo recuperare i dati dal db */
            $plugins = get_plugins($plugin_id);
            foreach ($plugins as $plugin) {
                $plugin_name = $plugin->get_name();
                $plugin_configurationfile = $plugin->get_configurationfile();
                $plugin_type = $plugin->get_type();
                $plugin_note = $plugin->get_note();
                
            }
            ?>
            <h2>Modifica plugin</h2>
            <form action="manage_plugins.php" method="POST" name="plugin">

            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="plugin_id" value="<?php echo $plugin_id; ?>">

            name: <input type="text" name="plugin_name" value="<?php echo $plugin_name ?>" /><br />
            configurationfile: <input type="text" name="plugin_configurationfile" value="<?php echo $plugin_configurationfile ?>" /><br/>
            type: <select name="plugin_type" >
                    <option  <?php if ($plugin_type=='link') echo 'selected'?>>link</option>
                    <option <?php if ($plugin_type=='single') echo 'selected'?>>single</option>
                  </select>
                <br />
                note: <input type="text" name="plugin_note" value="<?php echo $plugin_note ?>" /><br />

            <input type="submit" value="Submit" />
        </form>
        <?php
        }
        ?>
        <p><a href="index.php">Torna indietro</a></p>
    </body>
</html>
