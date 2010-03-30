<?php
session_start();
include 'include/db_utils.php';

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
                        if (elements[i].type == 'checkbox' && elements[i].disabled == false)
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

            function setReadOnly() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox') {
                            var check_type = elements[i].name.split("_");
                            if (check_type[2] == 'r') {
                                elements[i].checked = true;
                            }
                        }
                    }
                }
            }

            function setDeleteOnly() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox') {
                            var check_type = elements[i].name.split("_");
                            if (check_type[2] == 'w') {
                                elements[i].checked = true;
                            }
                        }
                    }
                }
            }

            function setModifyOnly() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox') {
                            var check_type = elements[i].name.split("_");
                            if (check_type[2] == 'm') {
                                elements[i].checked = true;
                            }
                        }
                    }
                }
            }

            function setInsertOnly() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox') {
                            var check_type = elements[i].name.split("_");
                            if (check_type[2] == 'i') {
                                elements[i].checked = true;
                            }
                        }
                    }
                }
            }

            function setHeaderOnly() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox') {
                            var check_type = elements[i].name.split("_");
                            if (check_type[2] == 'h') {
                                elements[i].checked = true;
                            }
                        }
                    }
                }
            }

            function setSearchOnly() {
                with (document.Users) {
                    for (var i=0; i < elements.length; i++) {
                        if (elements[i].type == 'checkbox') {
                            var check_type = elements[i].name.split("_");
                            if (check_type[2] == 's') {
                                elements[i].checked = true;
                            }
                        }
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
//            $resource_fields = get_fields_from_query($resultset_name);

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
        $resultsets = get_resultsets();
        //        if (count($groups) <= 0) {
        //            echo "Non esiste nessun gruppo che non abbia il resultset già in management. ".
        //                "<a href='index.php'>Torna indietro</a>";
        //        }
        //        else {
//        var_dump($group_id);
        if ($action == "update") {
            $group_id = $_POST['group_id'];
            $tools = strtoupper($_POST['tools']);
            $resultset_id = $_POST['resultset_id'];
            if (!$group_id) {
                die ("Errore: id del gruppo <pre><b>$group_id</b></pre> non impostato");
            }
            $resource_list_UPDATE = array();

            foreach($_REQUEST AS $key => $value) {
                if (preg_match('/^c_(\d*)_(\w+)/', $key, $pin)) {
                    ${'res_'.$pin[1]}['id'] = $pin[1];
                    ${'res_'.$pin[1]}[$pin[2]] = $value;
                    $resource_list_UPDATE[$pin[1]] = ${'res_'.$pin[1]};
                }
            }

            /*
            * per controllare se tutti i permessi sono impostati a 0,
            * caso in cui devo cancellare tutte le righe in __system_management,
            * imposto un flag e creo un array dove inserire gli id delle righe
            * appena create per poterle (eventualmente) cancellare alla fine
            */

            $permissions = 0;
            $array_insert = array();

            echo "<pre>Inserting permissions... ";
            foreach ($resource_list_UPDATE as $resource) {
                $id = $resource['id'];
                $r = 0 + $resource['r'];
                $w = 0 + $resource['w'];
                $m = 0 + $resource['m'];
                $i = 0 + $resource['i'];
                $alias = $resource['a'];
                $header = $resource['h'];
                $search = $resource['s'];
                $grouping=$resource['g'];
                if($search=="") $search = 0;
                if($header=="") $header = 0;
//                echo "<pre>Inserting $id ($alias) permissions [$r$w$m$i] ... ";
                // cancello eventuali permessi inseriti precedentemente
                $connection = db_get_connection();
                $query_del = "delete from $T_MANAGEMENT where id_resource = $id and id_group = $group_id";
                $risu_del = mysql_query($query_del);
                // modifico l'alias della risorsa
                $query_upd = "update $T_RESOURCE set alias = '$alias' where id = $id";
                $risu_upd = mysql_query($query_upd);
                mysql_close($connection);
                // inserisco i permessi
                $array_insert[] = insert_management_permissions($group_id, $id, $r, $w, $m, $i);
                // inserisco le proprietà della risorsa in _field
                if($header!="" || $search!="" || $grouping!="") edit_field($id, $header, $search, $grouping);
//
                if($r==1||$w==1||$m==1||$i==1) $permissions = 1;
            }
            echo "<b>done</b></pre>";
            //       	echo insert_management_toolbar($group_id, $resultset_id, $tools);
            // INSERIRE CODICE SALVATAGGIO TOOLBAR
            $toolbar = new toolbar($group_id, $resultset_id, $tools);
            insert_tool($toolbar);
    //print_r($_POST);
            // CODICE SALVATAGGIO ASSOCIAZIONI PLUGIN
            $plugins = get_plugins();
            foreach ($plugins as $plugin) {
                if ( $_POST['plugin_'.$plugin->get_id().'_ass'] == 'on') {
                    insert_pluginassociation($plugin->get_id(), $resultset_id, $group_id);
                } else {
                    delete_pluginassociation($plugin->get_id(), $resultset_id, $group_id);
                }
            }
            //

            // se i permessi sono tutti 0 e i tools sono vuoti, cancello le righe da system_management
            if($permissions==0 && trim($tools)=="") {
                foreach($array_insert as $id_insert) {
                    $connection = db_get_connection();
                    $query_del = "delete from $T_MANAGEMENT where id = $id_insert";
                    $risu_del = mysql_query($query_del);
                }
                echo "<pre><b>Permessi e tools vuoti, elimino i record relativi ai permessi</b></pre>";
            }
        }

        ?>

        <form action="manage_resources.php" method="POST" name="Group">
            <input type="hidden" name="action" value="old">
            <table>
                <tr>
                    <td>Seleziona resultset:</td>
                    <td><select name="resultset_id" onchange="this.form.submit()">
                <?php foreach ($resultsets as $resultset) {
                        $id = $resultset->get_id();
                        $name = $resultset->get_alias();
                        ?>
                    <option value="<?php echo $id ?>" <?php if ($_POST['resultset_id']==$id) echo "selected"; ?>>
                            <?php echo $name; ?>
                    </option>
                    <?php } ?>
            </select></td>
            <td rowspan="2"><input type="submit" value="aggiorna selezione"></td>
            
                </tr>
                <tr>
                    <td>Seleziona gruppo:</td>
                    <td><select name="group_id" onchange="this.form.submit()">
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
            </select></td>
                </tr>
            
        </table>
        </form>
        

        <form action="manage_resources.php" method="POST" name="Users">

            <input type="hidden" name="action" value="update">

            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>"
            <input type="submit" value="Submit" />
            <input type=button onclick="checkTutti()" value="Seleziona tutti" />
            <input type=button onclick="uncheckTutti()" value="Deseleziona tutti" />
            <input type=button onclick="setReadOnly()" value="sola lettura" />
            <input type=button onclick="setModifyOnly()" value="sola modifica" />
            <input type=button onclick="setDeleteOnly()" value="sola cancellazione" />
            <input type=button onclick="setInsertOnly()" value="solo inserimento" />
            <input type=button onclick="setHeaderOnly()" value="solo header" />
            <input type=button onclick="setSearchOnly()" value="sola ricerca" />
                   <table>
                <tr>
                    <td colspan="11">Gruppo: <b><?php echo $group_name; ?></b></td>
                </tr>
                <tr class="title">
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

                if ( isset ($_POST['resultset_id'])) {
                    $tab_name = get_resultset_name($_POST['resultset_id']);
                    $table_type = get_table_type($tab_name);
                }


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
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_w" value="1" <?php if($deleteperm==1) print "checked=\"checked\""; ?> <?php if($table_type=='View') print " disabled"; ?> /></td>
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_m" value="1" <?php if($modifyperm==1) print "checked=\"checked\""; ?> <?php if($table_type=='View') print " disabled"; ?> /></td>
                    <td><input type="checkbox" name="c_<?= $resource_id ?>_i" value="1" <?php if($insertperm==1) print "checked=\"checked\""; ?> <?php if($table_type=='View') print " disabled"; ?> /></td>
                    <td><?php if($resource_id != $_POST['resultset_id']) { ?><input type="checkbox" name="c_<?= $resource_id ?>_h" value="1" <?php if($resource_header==1) print "checked=\"checked\""; ?> /> <?php } ?></td>
                    <td><?php if($resource_id != $_POST['resultset_id']) { ?><input type="checkbox" name="c_<?= $resource_id ?>_s" value="1" <?php if($resource_search==1) print "checked=\"checked\""; ?> /> <?php } ?></td>
                    
                    <td><?php
                    if($resource_id != $_POST['resultset_id']) {
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
                

            </table>

             <table>
            <tr>
                    <td colspan="4">Tools (ALL MODIFY EXPORT IMPORT PREFERENCE ANALISYS)
                        <input type="hidden" name="resultset_id" value="<? echo $resultset_id; ?>">
                    </td>
                    <td colspan="4"><input type="text" name="tools" size="50" value="<?php echo $toolbar->get_tools(); ?>" /></td>
                </tr>
             </table>
            <table>
                <tr>
                    <td>Plugins</td>
                    <td>
                        <?php $plugins = get_plugins(); ?>
                        <table>
                                <tr class="title">
                                    <td>id</td>
                                    <td>name</td>
                                    <td>configurationfile</td>
                                    <td>type</td>
                                    <td>note</td>
                                    <td>abilita per questo gruppo</td>
                                </tr>
                                <?php

                                foreach ($plugins as $plugin) {
                                    $id_plugin = $plugin->get_id();
                                    $name = $plugin->get_name();
                                    $configurationfile = $plugin->get_configurationfile();
                                    $type = $plugin->get_type();
                                    $note = $plugin->get_note();

                                    $pluginassociation = get_pluginassociation($id_plugin, $id_resultset, $group_id);
                                ?>
                                <tr>
                                    <td><?php echo $id_plugin ?></td>
                                    <td><?php echo $name ?></td>
                                    <td><?php echo $configurationfile ?></td>
                                    <td><?php echo $type ?></td>
                                    <td><?php echo $note ?></td>
                                    <td><input type="checkbox" name="plugin_<?= $id_plugin ?>_ass" <?php if($pluginassociation==1) print "checked=\"checked\""; ?> /></td>
                                </tr>
                                <?php
                                }
                                ?>
                        </table>

                    </td>
                </tr>
        </table>
            <input type="submit" value="Submit" />
            <input type=button onclick="checkTutti()" value="Seleziona tutti" />
            <input type=button onclick="uncheckTutti()" value="Deseleziona tutti" />
            <input type=button onclick="setReadOnly()" value="sola lettura" />
            <input type=button onclick="setModifyOnly()" value="sola modifica" />
            <input type=button onclick="setDeleteOnly()" value="sola cancellazione" />
            <input type=button onclick="setInsertOnly()" value="solo inserimento" />
            <input type=button onclick="setHeaderOnly()" value="solo header" />
            <input type=button onclick="setSearchOnly()" value="sola ricerca" />
        </form>
        <?php
//        mysql_close($connection);
        //        }
        ?>
    </body>
</html>
