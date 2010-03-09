<?php

include_once 'db_connection.php';
include_once 'db_table_names.php';
include_once 'resource.php';
include_once 'notify.php';
include_once 'resultset.php';
include_once 'group.php';
include_once 'grouping.php';
include_once 'user.php';
include_once 'toolbar.php';
include_once 'plugin.php';

function insert_management_permissions($group_id, $resource_id, $r = 0, $w = 0, $m = 0, $i = 0) {
    global $T_MANAGEMENT;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_MANAGEMENT ".
        "(`id_group`, `id_resource`, `readperm`, `deleteperm`, `modifyperm`, `insertperm`) ".
        "VALUES ('%d', '%d', '%d', '%d', '%d', '%d')",
        $group_id, $resource_id, $r, $w, $m, $i);

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $result = mysql_insert_id();
    return $result;

    mysql_close($connection);
}


function insert_tool($toolbar) {
    global $T_TOOLBAR;

    $connection = db_get_connection();

    $query = "INSERT INTO $T_TOOLBAR SET `id`='',`id_resultset`='".$toolbar->get_id_resultset()."',
                `id_group`='".$toolbar->get_id_group()."',`tools`='".$toolbar->get_tools()."'
                ON DUPLICATE KEY UPDATE `id_resultset`='".$toolbar->get_id_resultset()."',
                `id_group`='".$toolbar->get_id_group()."', `tools`='".$toolbar->get_tools()."'";
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());
    mysql_close($connection);
    return "<pre>Toolbar added!</pre>";
}


function insert_pluginassociation($id_plugin, $id_resultset, $id_group) {
    global $T_PLUGINASSOCIATION;

    $connection = db_get_connection();

    $query = "INSERT INTO $T_PLUGINASSOCIATION SET `id_plugin`='".$id_plugin."',`id_resultset`='".$id_resultset."',
                `id_group`='".$id_group."'
                ON DUPLICATE KEY UPDATE `id_plugin`='".$id_plugin."',`id_resultset`='".$id_resultset."',
                `id_group`='".$id_group."'";
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());
    mysql_close($connection);
    return "<pre>Plugin association added!</pre>";
}


function delete_pluginassociation($id_plugin, $id_resultset, $id_group) {
    global $T_PLUGINASSOCIATION;

    $connection = db_get_connection();

    $query = "DELETE FROM $T_PLUGINASSOCIATION WHERE `id_plugin`='".$id_plugin."' AND `id_resultset`='".$id_resultset."' AND
                `id_group`='".$id_group."'";
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());
    mysql_close($connection);
    return "<pre>Plugin association deleted</pre>";
}

function insert_management_toolbar($group_id, $id_resulteset, $tools) {
    global $T_TOOLBAR;
    if (trim($tools)!="") {
        $connection = db_get_connection();

        $query = sprintf("INSERT into $T_TOOLBAR ".
            "(`id_resultset`, `id_group`, `tools`) ".
            "VALUES ('%d', '%d', '%s') ",
            $id_resulteset, $group_id, $tools);

        $result = mysql_query($query)
            or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());
        mysql_close($connection);
        return "<pre>Toolbar added!</pre>";
    }
}


function insert_management($group_id, $resource_id) {
    global $T_MANAGEMENT;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_MANAGEMENT ".
        "(`id_group`, `id_resource`) VALUES ('%d', '%d')",
        $group_id, $resource_id);

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);
}

function insert_user($username, $password, $name, $surname, $email, $office, $telephone, $status, $id_group) {
    global $T_USER;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_USER ".
        "(`username`, `password`, `name`, `surname`, `email`, `office`, `telephone`, `status`, `id_group`)
            VALUES ('%s', PASSWORD('%s'), '%s','%s','%s','%s','%s','%s','%s')",
        $username, $password, addslashes($name), addslashes($surname), addslashes($email), addslashes($office), $telephone, $status, $id_group);

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $id = mysql_insert_id  ($connection);

    mysql_close($connection);

    return $id;
}

function edit_user($user_id, $username, $name, $surname, $email, $office, $telephone, $status, $id_group, $password="") {
    global $T_USER;
    $connection = db_get_connection();

    if($password!="") {
        $query = sprintf("UPDATE $T_USER SET `username`='%s', `password`=PASSWORD('%s'), `name`='%s',
            `surname`='%s', `email`='%s', `office`='%s', `telephone`='%s', `status`='%s', `id_group`='%s'
            WHERE id='%s'",
            $username, $password, $name, $surname, $email, $office, $telephone, $status, $id_group, $user_id);
    } else {
        $query = sprintf("UPDATE $T_USER SET `username`='%s',   `name`='%s',
            `surname`='%s', `email`='%s', `office`='%s', `telephone`='%s', `status`='%s', `id_group`='%s'
            WHERE id='%s'",
            $username,   $name, $surname, $email, $office, $telephone, $status, $id_group, $user_id);
    }

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $user_id;
}

function get_users($id_user=0) {
    global $T_USER, $T_GROUP;
    $connection = db_get_connection();

    if($id_user!=0) $WHERE_CLAUSE = "WHERE tu.`id` = '$id_user' ";

    $query = "SELECT tu.*, tg.name AS group_name
        FROM $T_USER tu
        LEFT JOIN $T_GROUP tg ON tu.`id_group` = tg.`id`
        $WHERE_CLAUSE
        ORDER BY tu.status DESC, tu.id";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $username = $row['username'];
        $password = $row['password'];
        $name = $row['name'];
        $surname = $row['surname'];
        $email = $row['email'];
        $office = $row['office'];
        $telephone = $row['telephone'];
        $status = $row['status'];
        $id_group = $row['id_group'];
        $group_name = $row['group_name'];
        $results[$i++] = new user($id, $username, $password, $name, $surname, $email, $office, $telephone, $status, $id_group, $group_name);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}

function insert_group($name, $status) {
    global $T_GROUP;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_GROUP (`name`, `status`) VALUES ('%s', '%s')",
        addslashes($name), $status);

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $id = mysql_insert_id  ($connection);

    mysql_close($connection);

    return $id;
}

function edit_group($group_id, $name, $status, $old_status ) {
    global $T_GROUP, $T_USER;
    $connection = db_get_connection();

    $query = sprintf("UPDATE $T_GROUP SET `name`='%s', `status`='%s' WHERE id='%s'",
        $name, $status, $group_id);
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    if($status!=$old_status) {
        /* lo stato del gruppo è stato modificato, quindi attivo o disattivo
         * tutti gli utenti che ne fanno parte */
        $query_usr = sprintf("UPDATE $T_USER SET `status`='%s'WHERE id_group='%s'",
            $status, $group_id);
        $result_usr = mysql_query($query_usr)
            or die("Query <pre><b>$query_usr</b></pre> failed: " . mysql_error());
    }

    mysql_close($connection);

    return $group_id;
}

function get_groups($id_group=0) {
    global $T_MANAGEMENT, $T_GROUP;
    $connection = db_get_connection();

    // la query commentata prendeva solo i dati dei gruppi che non sono hanno il resultset
    //$query = "SELECT * FROM $T_GROUP WHERE `status` = 1 ". "AND `id` NOT IN (SELECT DISTINCT `id_group` "." FROM $T_MANAGEMENT WHERE `id_resource` = $resultset_id)";

    if($id_group!=0) $WHERE_CLAUSE = "WHERE `id` = '$id_group' ";

    $query = "SELECT * FROM $T_GROUP $WHERE_CLAUSE ORDER BY status DESC, id";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $name = $row['name'];
        $status = $row['status'];
        $results[$i++] = new Group($id, $name, $status);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}

function delete_groups($id_group=0) {
    global $T_USER, $T_GROUP;
    $connection = db_get_connection();

    if($id_group!=0) {

    // cancello il gruppo
        $query = "DELETE from $T_GROUP where `id` = '$id_group' LIMIT 1";
        $result = mysql_query($query)
            or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

        if ($result) return TRUE;
        else return FALSE;
    } else {
        return FALSE;
    }

    mysql_free_result($result);
    mysql_close($connection);

}

function insert_grouping($name, $alias) {
    global $T_GROUPING;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_GROUPING (`name`, `alias`) VALUES ('%s', '%s')",
        addslashes($name), addslashes($alias));

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $id = mysql_insert_id  ($connection);

    mysql_close($connection);

    return $id;
}

function edit_grouping($grouping_id, $name, $alias ) {
    global $T_GROUPING;
    $connection = db_get_connection();

    $query = sprintf("UPDATE $T_GROUPING SET `name`='%s', `alias`='%s' WHERE id='%s'",
        $name, $alias, $grouping_id);
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $grouping_id;
}


function get_plugins($id_plugin=0) {
    global $T_PLUGIN;
    $connection = db_get_connection();

    if($id_plugin!=0) $WHERE_CLAUSE = "WHERE `id` = '$id_plugin' ";

    $query = "SELECT * FROM $T_PLUGIN $WHERE_CLAUSE ORDER BY name";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $name = $row['name'];
        $configurationfile = $row['configurationfile'];
        $type = $row['type'];
        $note = $row['note'];
        $results[$i++] = new Plugin($id, $name, $configurationfile, $type, $note);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}


function get_pluginassociation($id_plugin, $id_resultset, $id_group) {
    global $T_PLUGINASSOCIATION;
    $connection = db_get_connection();

    $query = "SELECT * FROM $T_PLUGINASSOCIATION WHERE `id_plugin`='".$id_plugin."' AND `id_resultset`='".$id_resultset."' AND `id_group`='".$id_group."'";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = mysql_num_rows($result);

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}

function delete_plugin($id_plugin=0) {
    global $T_PLUGIN;
    $connection = db_get_connection();

    if($id_plugin!=0) {

    // cancello il gruppo
        $query = "DELETE from $T_PLUGIN where `id` = '$id_plugin' LIMIT 1";
        $result = mysql_query($query)
            or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

        if ($result) return TRUE;
        else return FALSE;
    } else {
        return FALSE;
    }

    mysql_free_result($result);
    mysql_close($connection);

}


function edit_plugin($plugin_id, $name, $configurationfile, $type, $note ) {
    global $T_PLUGIN;
    $connection = db_get_connection();

    $query = sprintf("UPDATE $T_PLUGIN SET `name`='%s', `type`='%s', `configurationfile`='%s', `note`='%s' WHERE id='%s'",
        $name, $type, $configurationfile, $note, $plugin_id);
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $grouping_id;
}


function insert_plugin($name, $configurationfile, $type, $note) {
    global $T_PLUGIN;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_PLUGIN (`name`, `type`, `configurationfile`, `note`) VALUES ('%s', '%s', '%s', '%s')",
        addslashes($name), $type, addslashes($configurationfile), addslashes($note));

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $id = mysql_insert_id  ($connection);

    mysql_close($connection);

    return $id;
}

function get_groupings($id_grouping=0) {
    global $T_GROUPING;
    $connection = db_get_connection();

    if($id_grouping!=0) $WHERE_CLAUSE = "WHERE `id` = '$id_grouping' ";

    $query = "SELECT * FROM $T_GROUPING $WHERE_CLAUSE ORDER BY alias";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $name = $row['name'];
        $alias = $row['alias'];
        $results[$i++] = new Grouping($id, $name, $alias);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}

function delete_grouping($id_grouping=0) {
    global $T_GROUPING;
    $connection = db_get_connection();

    if($id_grouping!=0) {

    // cancello il gruppo
        $query = "DELETE from $T_GROUPING where `id` = '$id_grouping' LIMIT 1";
        $result = mysql_query($query)
            or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

        if ($result) return TRUE;
        else return FALSE;
    } else {
        return FALSE;
    }

    mysql_free_result($result);
    mysql_close($connection);

}


function insert_field($id, $resultset_id, $type, $def) {
    global $T_FIELD;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_FIELD ".
        "(`id`, `default_header`, `id_resultset`, `type`, `defaultvalue` ) VALUES ".
        "('%d', '1', '%d', '%s', '%s')",
        $id, $resultset_id, addslashes($type), addslashes($def));

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);
}

function edit_field($id, $default_header, $search_grouping, $id_grouping) {
    global $T_FIELD;
    $connection = db_get_connection();

    $query = sprintf("UPDATE $T_FIELD ".
        "SET `default_header`=%s, `search_grouping`=%s, `id_grouping`=%s ".
        "WHERE id=%s",
        $default_header, $search_grouping, $id_grouping, $id);

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);
}


function insert_resultset($resultset) {
    global $T_RESULTSET;
    $connection = db_get_connection();

    $id = $resultset->get_id();
    $statement = $resultset->get_statement();

    $query = sprintf("INSERT into $T_RESULTSET ".
        "(`id`, `statement`) VALUES ('%s', '%s')",
        $id, addslashes($statement));

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);
}

function insert_notify($notify) {
    global $T_NOTIFY;
    $connection = db_get_connection();

    $resultset_id = $notify->get_id_resultset();
    $notify_name = $notify->get_notify_name();
    $address_statement = $notify->get_address_statement();
    $data_statement = $notify->get_data_statement();
    $xslt = $notify->get_xslt();
    $bmdid = $notify->get_bmdid();

    $query = sprintf("INSERT into $T_NOTIFY ".
        "(`resultset_id`, `name`, `address_statement`, `data_statement`, `xslt`, `link_id`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
        $resultset_id, $notify_name, $address_statement, $data_statement,  $xslt, $bmdid);

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);
}

function insert_resource($name, $alias) {
    global $T_RESOURCE;
    $connection = db_get_connection();

    $query = sprintf("INSERT into $T_RESOURCE ".
        "(`name`, `alias`) VALUES ('%s', '%s')",
        addslashes($name), addslashes($alias));

    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $id = mysql_insert_id  ($connection);

    mysql_close($connection);

    return $id;
}

function get_resultsets() {
    global $T_RESOURCE, $T_RESULTSET;
    $connection = db_get_connection();

    $query = "SELECT * FROM $T_RESULTSET rs JOIN $T_RESOURCE r ".
        "ON (r.id = rs.id) WHERE 1";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $name = $row['name'];
        $alias = $row['alias'];
        $statement = $row['statement'];
        $results[$i++] = new Resultset($id, $name, $alias, $statement);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}


function get_resultset_name($resultset_id) {
    global $T_RESOURCE, $T_RESULTSET;
    $connection = db_get_connection();

    $query = "SELECT r.name as `name` FROM $T_RESULTSET rs JOIN $T_RESOURCE r ".
        "ON (r.id = rs.id) WHERE rs.id=".$resultset_id;

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $name = $row['name'];
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $name;
}


function get_toolbar_from_ids($resultset_id, $group_id) {
    global $T_TOOLBAR;

    $connection = db_get_connection();

    $query = "SELECT * FROM $T_TOOLBAR WHERE `id_resultset`='".$resultset_id."' AND `id_group`='".$group_id."'";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    if (mysql_num_rows($result) == 1) {
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $tool = new toolbar($row['id_resultset'], $row['id_group'], $row['tools']);
    } else $tool = new toolbar(null, $id_group, $id_resultset, null);

    mysql_free_result($result);
    mysql_close($connection);

    return $tool;
}

function get_notify() {
    global $T_NOTIFY;
    $connection = db_get_connection();

    $query = "SELECT * FROM $T_NOTIFY WHERE 1";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id_resultset = $row['resultset_id'];
        $notify_name = $row['name'];
        $address_statement = $row['address_statement'];
        $data_statement = $row['data_statement'];
        $xslt = $row['xslt'];
        $id_notify = $row['id'];
        $bmdid = $row['link_id'];

        $results[$i++] = new Notify($id_resultset, $notify_name, $address_statement, $data_statement, $xslt, $id_notify, $bmdid);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}

function get_fields_from_resultsetid($resultset_id) {
    global $T_FIELD, $T_RESOURCE;
    $connection = db_get_connection();

    $query = "SELECT * FROM $T_RESOURCE r JOIN $T_FIELD f USING (`id`) ".
        "WHERE f.`id_resultset` = $resultset_id";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $name = $row['name'];
        $alias = $row['alias'];
        $type = $row['type'];
        $def = $row['defaultvalue'];
        $header = $row['default_header'];
        $search = $row['search_grouping'];
        $grouping = $row['id_grouping'];
        $results[$i++] = new Resource($id, $name, $alias, $type, $def, $header, $search, $grouping);
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $results;
}


function get_fields_from_query($query) {
    $connection = db_get_connection();

    /* Definizione query -> uguale a statement */

    /* Esegui la query */
    $result = mysql_query($query . " LIMIT 0,1")
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $results = array();
    $i = 0;
    while ($i < mysql_num_fields($result)) {
        /*  Prendi il metadata */
        $meta = mysql_fetch_field($result, $i)
            or die('Metadata fetch failed: ' . mysql_error());

        /* Inserisci il nome del campo nei risultati */
        $results[$i++] =
            new Resource(null, $meta->name, null, $meta->type, $meta->def, $meta->header, $meta->search, $meta->grouping);
    }

    //mysql_free_result($result);
    mysql_close($connection);

    /* Ritorna i risultati */
    return $results;
}


function get_table_type($resultset_name) {
    $connection = db_get_connection();

    $query = "SHOW CREATE TABLE `".$resultset_name. "`";
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    //    $type = 'Table';
    // interessa solo il primo campo
    $row = mysql_fetch_field($result);
    if ($row) {
        $type = $row->name;
    }

    return $type;

}

//function get_fields_from_query($table_name) {
//    $connection = db_get_connection();
//
//    /* Definizione query -> uguale a statement */
//
//    /* Esegui la query */
//    $query = "SHOW COLUMNS FROM `". $table_name ."`";
//    $result = mysql_query($query)
//        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());
//
//    $results = array();
//    $i = 0;
//    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//
//        /* Inserisci il nome del campo nei risultati */
//        $results[$i++] =
//            new Resource(null, $row['Field'], null, $row['Type'], $row['Default'], null, null, null);
//    }
//
//    //mysql_free_result($result);
//    mysql_close($connection);
//
//    /* Ritorna i risultati */
//    return $results;
//}


function print_result_metadata($result) {
    $i = 0;
    while ($i < mysql_num_fields($result)) {
        echo "Information for column $i:<br />\n";
        $meta = mysql_fetch_field($result, $i);
        if (!$meta) {
            echo "No information available<br />\n";
        }
        echo "<pre>
blob:         $meta->blob
max_length:   $meta->max_length
multiple_key: $meta->multiple_key
name:         $meta->name
not_null:     $meta->not_null
numeric:      $meta->numeric
primary_key:  $meta->primary_key
table:        $meta->table
type:         $meta->type
default:      $meta->def
unique_key:   $meta->unique_key
unsigned:     $meta->unsigned
zerofill:     $meta->zerofill
</pre>";
        $i++;
    }
}

function get_resource_from_id($resource_id) {
    global $T_RESOURCE;
    $connection = db_get_connection();

    $query = "SELECT * FROM $T_RESOURCE WHERE `id` = $resource_id";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    $i = 0;
    $resource = null;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $name = $row['name'];
        $alias = $row['alias'];
        $resource = new Resource($id, $name, $alias);
    }
    if (!$resource || $row > 1) {
        die("Query <pre><b>$query</b></pre> failed: too much results");
    }

    mysql_free_result($result);
    mysql_close($connection);

    return $resource;
}

function remove_only_resultset_by_id($resultset_id) {
    global $T_RESULTSET;
    $connection = db_get_connection();

    $query = "DELETE FROM $T_RESULTSET WHERE `id` = $resultset_id LIMIT 1";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $resource;
}

function remove_notify_by_id($id) {
    global $T_NOTIFY;
    $connection = db_get_connection();

    $query = "DELETE FROM $T_NOTIFY WHERE `id` = $id LIMIT 1";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $resource;
}


function remove_resource_by_id($resource_id) {
    global $T_RESOURCE;
    $connection = db_get_connection();

    $query = "DELETE FROM $T_RESOURCE WHERE `id` = $resource_id LIMIT 1";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $resource;
}

function remove_fields_by_resultset_id($resultset_id) {
    global $T_FIELD;
    $connection = db_get_connection();

    $query = "DELETE FROM $T_FIELD WHERE `id_resultset` = $resultset_id ";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $resource;
}

function remove_management_permission_by_resource_id($resource_id) {
    global $T_MANAGEMENT;
    $connection = db_get_connection();

    $query = "DELETE FROM $T_MANAGEMENT WHERE `id_resource` = $resource_id ";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $resource;
}

function remove_resources_of_resultset_by_resultset_id($resultset_id) {
    $resource_list = get_fields_from_resultsetid($resultset_id);

    foreach($resource_list as $resource) {
        remove_resource_by_id($resource->get_id());
    }

            /* Il resultset _deve_ essere eliminato per ultimo */
    remove_resource_by_id($resultset_id);
}

function remove_management_permissions_by_resultset_id($resultset_id) {
    $resource_list = get_fields_from_resultsetid($resultset_id);

    foreach($resource_list as $resource) {
        remove_management_permission_by_resource_id($resource->get_id());
    }
}

function remove_toolbar_by_resultset_id($resultset_id) {
    global $T_TOOLBAR;
    $connection = db_get_connection();

    $query = "DELETE FROM $T_TOOLBAR WHERE `id_resultset` = $resultset_id";

    /* Esegui la query */
    $result = mysql_query($query)
        or die("Query <pre><b>$query</b></pre> failed: " . mysql_error());

    mysql_close($connection);

    return $resource;
}

function remove_resultset_complete_by_id($resultset_id) {
    remove_management_permissions_by_resultset_id ($resultset_id);
    remove_resources_of_resultset_by_resultset_id($resultset_id); //prima dei fields
    remove_fields_by_resultset_id($resultset_id);
    //remove_toolbar_by_resultset_id($resultset_id);
    remove_only_resultset_by_id($resultset_id);
}

?>