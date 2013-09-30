<?php
session_start();
include_once '../include/db_utils.php';


?>

        <?php
        $group_id = $_POST['group_id'];
        $tools = strtoupper($_POST['tools']);
        $resultset_id = $_POST['resultset_id'];
        if (!$group_id) {
            die ("Errore: id del gruppo <pre><b>$group_id</b></pre> non impostato");
        }
        $resource_list = array();

        foreach($_REQUEST AS $key => $value) {
            if (preg_match('/^c_(\d*)_(\w+)/', $key, $pin)) {
                ${'res_'.$pin[1]}['id'] = $pin[1];
                ${'res_'.$pin[1]}[$pin[2]] = $value;
                $resource_list[$pin[1]] = ${'res_'.$pin[1]};
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

        foreach ($resource_list as $resource) {
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
            echo "<pre>Inserting $id ($alias) permissions [$r$w$m$i] ... ";
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
            echo "<b>done</b></pre>";
            if($r==1||$w==1||$m==1||$i==1) $permissions = 1;
        }

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
        
        ?>

