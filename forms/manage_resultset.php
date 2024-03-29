<?php
session_start();
include_once '../include/db_utils.php';

$connection = db_get_connection();
$resultsets = get_resultsets();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
<!--        <a href="index.php">torna all'inizio</a>-->
        <?php
        if (! isset ($_POST['action'])) {
            ?>
            <form action="manage_resultset.php" method="POST">
                <table border="0">
                    <tr>
                        <td><select name="resultset_id">
                    <?php foreach ($resultsets as $resultset) {
                        $id = $resultset->get_id();
                        $name = $resultset->get_alias();
                        ?>
                    <option value="<?php echo $id ?>">
                            <?php echo $name; ?>
                    </option>
                    <?php } ?>
                </select></td>
                        <td><input type="submit" value="Submit" /></td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="delete" />
            </form>
<?php
        }
        $action = $_POST['action'];

        if ($action == "new") {

            /* Prendi le variabili del resultset dalle variabili di POST */
            $resultset_name = $_POST['resultset_name'];
            $resultset_alias = $_POST['resultset_alias'];
            $resultset_statement = $_POST['resultset_statement'];

            /* Esegui query per prendere i campi dal resultset */
            $resource_fields = get_fields_from_query($resultset_statement);
//            $resource_fields = get_fields_from_query($resultset_name);

            /* Inserisci il resultset in resources e prendine l'id */
            $resultset_id = insert_resource($resultset_name, $resultset_alias);

            $resultset = new Resultset($resultset_id, $resultset_name,
                $resultset_alias, $resultset_statement);

            /* Salva il resultset nella tabella resultset */
            insert_resultset($resultset);

            /* Inserisci nella tabella resource
             * e field tutte le risorse con alias = name */
            foreach ($resource_fields as $resource_field) {
                $resource_name = $resource_field->get_name();
                $resource_type = $resource_field->get_type();
                $resource_def  = $resource_field->get_def();
                $resource_id   = insert_resource($resource_name, $resource_name);
                insert_field($resource_id, $resultset_id, $resource_type, $resource_def);
            }

        } elseif ($action == "delete") {
            $resultset_id = $_POST['resultset_id'];
            remove_resultset_complete_by_id($resultset_id);
            ?>
<script type="javascript">
    window.location = "../index2.php"
</script>
            <?
        }

        ?>
<!--        <p>Operazione eseguita correttamente. <a href='index.php'>Torna indietro</a></p>-->

    </body>
</html>
