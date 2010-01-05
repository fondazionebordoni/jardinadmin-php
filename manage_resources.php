<?php
session_start();
include_once 'include/db_utils.php';

$connection = db_get_connection();
// recupero le info sui grouping
$query_grouping = "select * from $T_GROUPING";
$res_grouping = mysql_query($query_grouping);
$array_grouping = array();
while($arr_gr = mysql_fetch_array($res_grouping)) {
    $array_grouping[$arr_gr['id']] = $arr_gr['alias'];
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript">
            function checkTutti() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox')
                            elements[i].checked = true;
                    }
                }
            }

            function uncheckTutti() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox')
                            elements[i].checked = false;
                    }
                }
            }
        </script>
    </head>
    <body>
        <a href="index.php">torna all'inizio</a>
        <?php
        $action = $_POST['action'];

        if (isset ($_POST['group_id'])) {
            $group_id = $_POST['group_id'];
        } else $group_id = 1;
        if (isset ($_POST['resultset_id'])) {
            $id_resultset = $_POST['resultset_id'];
        }
//        var_dump($group_id);

        if ($action == "new") {

            /* Prendi le variabili del resultset dalle variabili di POST */
            $resultset_name = $_POST['resultset_name'];
            $resultset_alias = $_POST['resultset_alias'];
            $resultset_statement = stripslashes($_POST['resultset_statement']);

            /* Esegui query per prendere i campi dal resultset */
            $resource_fields = get_fields_from_query($resultset_statement);

            /*
            * TODO questa parte deve essere eseguita dopo la fase che permette
            * all'utente di scegliere l'alias e default_header
            */

            /* Inserisci il resultset in resources e prendine l'id */
            $resultset_id = insert_resource($resultset_name, $resultset_alias);

            $resultset = new Resultset($resultset_id, $resultset_name,
                $resultset_alias, $resultset_statement);

            /* Salva il resultset nella tabella resultset */
            insert_resultset($resultset);

            /* Inserisci nella tabella resource
             * e field tutte le risorse con alias = name */
            $resource_list = array();
            $i = 0;
            $resource_list[$i++] = $resultset;
            foreach ($resource_fields as $resource_field) {
                $resource_name = $resource_field->get_name();
                $resource_type = $resource_field->get_type();
                $resource_def  = $resource_field->get_def();
                $resource_header=$resource_field->get_header();
                $resource_search=$resource_field->get_search();
                $resource_grouping=$resource_field->get_grouping();
                $resource_id   = insert_resource($resource_name, $resource_name);
                insert_field($resource_id, $resultset_id, $resource_type, $resource_def);
                $resource_list[$i++] =
                    new Resource($resource_id, $resource_name, $resource_name,
                    $resource_type, $resource_def, $resource_header, $resource_search, $resource_grouping);
            }
            
        // modifica per cercare di eliminare il bug sulla creazione di resultset + permessi
        $toolbar = get_toolbar_from_ids($resultset_id, $group_id);
        $_POST['resultset_id'] = $resultset_id;

        }
        else { // action = old

            $resultset_id = $_POST['resultset_id'];

            if (!$resultset_id) {
                die ("Errore: id del resultset <pre><b>$resultset_id</b></pre> non impostato");
            }

            $resource_list = get_fields_from_resultsetid($resultset_id);
            $resource_list[] = get_resource_from_id($resultset_id);
            $toolbar = get_toolbar_from_ids($resultset_id, $group_id);
        }

        //$groups = get_groups($resultset_id);
        $groups = get_groups();

        //        if (count($groups) <= 0) {
        //            echo "Non esiste nessun gruppo che non abbia il resultset già in management. ".
        //                "<a href='index.php'>Torna indietro</a>";
        //        }
        //        else {
//        var_dump($group_id);
        ?>

        <form action="manage_resources.php" method="POST" name="Group">

            <input type="hidden" name="action" value="old">
            <input type="hidden" name="resultset_id" value="<?php echo $_POST['resultset_id'] ?>">

            Seleziona gruppo:
            <select name="group_id" onchange="this.form.submit()">
                <?php foreach ($groups as $group) {
                    $id = $group->get_id();
                    $name = $group->get_name();
                    if($group_id=="") $group_id = $id;
                    if($group_id=="$id") $group_name = $name;
                    ?>
                <option value="<?php echo $id ?>" <?php if ($id==$group_id) echo "selected"; ?>>
                        <?php echo $name; ?>
                </option>
                <?php } ?>
            </select>
            
        <input type="submit" value="cambia gruppo">

        </form>

        <form action="manage_permissions.php" method="POST" name="Users">

            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>"

                   <table>
                <tr>
                    <td colspan="11">Gruppo: <b><?php echo $group_name; ?></b></td>
                </tr>
                <tr>
                    <td>name</td>
                    <td>alias</td>
                    <td>type</td>
                    <td>defval</td>
                    <td>read</td>
                    <td>delete</td>
                    <td>modify</td>
                    <td>insert</td>
                    <td>header</td>
                    <td>ricerca</td>
                    <td>raggruppamento</td>
                </tr>

                <?php

                $connection = db_get_connection();

                foreach ($resource_list as $resource) {
                    $resource_name = $resource->get_name();
                    $resource_alias = $resource->get_alias();
                    $resource_id = $resource->get_id();
                    $resource_type = $resource->get_type();
                    $resource_def = $resource->get_def();
                    $resource_header = $resource->get_header();
                    $resource_search = $resource->get_search();
                    $resource_grouping = $resource->get_grouping();

                    // recupero le info sui permessi
                    $query_management = "select * from $T_MANAGEMENT
                            where id_resource = $resource_id
                            and id_group = {$group_id}";
                    $res_management = mysql_query($query_management);
                    $arr_management = mysql_fetch_array($res_management);

                    $readperm = $arr_management['readperm'];
                    $deleteperm = $arr_management['deleteperm'];
                    $modifyperm = $arr_management['modifyperm'];
                    $insertperm = $arr_management['insertperm'];
                    ?>
                <tr>
                    <td><?= $resource_name ?></td>
                    <td><input type="text" name="c_<?= $resource_id ?>_a" value="<?= $resource_alias ?>"></td>
                    <td><?= $resource_type ?></td>
                    <td><?= $resource_def ?></td>
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_r" value="1" <?php if($readperm==1) print "checked=\"checked\""; ?> /></td>
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_w" value="1" <?php if($deleteperm==1) print "checked=\"checked\""; ?> /></td>
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_m" value="1" <?php if($modifyperm==1) print "checked=\"checked\""; ?> /></td>
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_i" value="1" <?php if($insertperm==1) print "checked=\"checked\""; ?> /></td>
						  <td><?php if($resource_header!="") { ?><input type="checkbox" name="c_<?= $resource_id ?>_h" value="1" <?php if($resource_header==1) print "checked=\"checked\""; ?> /> <?php } ?></td>
                    <td><?php if($resource_search!="") { ?><input type="checkbox" name="c_<?= $resource_id ?>_s" value="1" <?php if($resource_search==1) print "checked=\"checked\""; ?> /> <?php } ?></td>
                    
                    <td><?php
                    if($resource_grouping!="") {
                        echo "<select name=\"c_{$resource_id}_g\">";
                        foreach ($array_grouping as $ke => $va) {
                            echo "<option value=\"$ke\"";
                            if($resource_grouping==$ke) echo " selected";
                            echo ">$va</option>";
                        }
                        echo "</select>";
                    }
                    ?></td>
                </tr>
                <?php
                }

                mysql_free_result($res_management);


                ?>
                <tr>
                    <td colspan="11">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">Tools (ALL MODIFY EXPORT IMPORT PREFERENCE ANALISYS)
                        <input type="hidden" name="resultset_id" value="<? echo $resultset_id; ?>">
                    </td>
                    <td colspan="4"><input type="text" name="tools" value="<?php echo $toolbar->get_tools(); ?>" /></td>
                </tr>

            </table>

            <input type="submit" value="Submit" />
            <input type=button onclick="checkTutti()" value="Seleziona tutti" />
            <input type=button onclick="uncheckTutti()" value="Deseleziona tutti" />
        </form>
        <?php
        mysql_close($connection);
        //        }
        ?>
    </body>
</html>
