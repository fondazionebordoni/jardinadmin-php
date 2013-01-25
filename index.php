<?php
session_start();
// se ricevo i dati del form, salvo in sessione i dati relativi al db a cui connettersi
if ($_POST['mysql_host']!="" && $_POST['mysql_user']!="" && $_POST['mysql_database']!="" && $_POST['mysql_password']!="")
{
    $_SESSION['mysql_host'] = $_POST['mysql_host'];
    $_SESSION['mysql_user'] = $_POST['mysql_user'];
    $_SESSION['mysql_database'] = $_POST['mysql_database'];
    $_SESSION['mysql_password'] = $_POST['mysql_password'];
}

// imposto alcuni valori di default riguardo il db di connessione
if($_SESSION['mysql_host']=="") $_SESSION['mysql_host'] = "";
if($_SESSION['mysql_user']=="") $_SESSION['mysql_user'] = "";
if($_SESSION['mysql_database']=="") $_SESSION['mysql_database'] = "";

// se ho un db a cui connettermi, includo i vari files e imposto un flag
if ($_SESSION['mysql_host']!="" && $_SESSION['mysql_user']!="" && $_SESSION['mysql_database']!="" && $_SESSION['mysql_password']!="")
{
    include_once 'include/db_utils.php';
    $db_conn = 1;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("div.section h2").next("form").slideToggle("slow");
                $("div.section h2").toggleClass("not_visible");

                $("div.section h2").click(function () {
                    $(this).next("form").slideToggle();
                    $(this).toggleClass("not_visible");
                });

            });

            function canc_user() {
                // conferma la richiesta di cancellazione di un utente
                conf = window.confirm('Confermi la cancellazione di questo utente? L\'operazione non può essere annullata');
                if(conf) return true;
            }

            function canc_group() {
                // conferma la richiesta di cancellazione di un gruppo 
                conf = window.confirm('Confermi la cancellazione di questo gruppo? L\'operazione non può essere annullata e tutti gli utenti del gruppo non avranno più accesso al sistema');
                if(conf) return true;
            }

            function canc_grouping() {
                // conferma la richiesta di cancellazione di un reggruppamento
                conf = window.confirm('Confermi la cancellazione di questo raggruppamento? L\'operazione non può essere annullata');
                if(conf) return true;
            }

            function canc_plugin() {
                // conferma la richiesta di cancellazione di un reggruppamento
                conf = window.confirm('Confermi la cancellazione di questo plugin? L\'operazione non può essere annullata');
                if(conf) return true;
            }
        </script>
    </head>
    <body>
        <?php
        if ($db_conn==1) $resultsets = get_resultsets();
        //echo "<h2 style='color:grey;'>connesso al db <span style='color:red;'>".$_SESSION['dbname']."</span> su <span style='color:red;'>".$_SESSION['dbhost']."<span></h2>";
        ?>
        <form name="db_selection" method="post">
            Host: <input type="text" name="mysql_host" value="<?php echo $_SESSION['mysql_host']; ?>">
            DB user: <input type="text" name="mysql_user" value="<?php echo $_SESSION['mysql_user']; ?>">
            Password: <input type="password" name="mysql_password" value="<?php echo $_SESSION['mysql_password']; ?>">
            Database: <input type="text" name="mysql_database" value="<?php echo $_SESSION['mysql_database']; ?>">
            <input type="submit" value="Connect">
        </form>
        <?php
        if($db_conn!="1")
        {
            // non è stato selezionato un db quindi non stampo il resto della pagina
            echo "</body></html>";
            exit();
        }
        ?>
        <div class="section">
            <h1>Gestione Resultset</h1>

            <h2>Creazione Resultset (con impostazione permessi ad un gruppo)</h2>
            <form action="manage_resources.php" method="POST">
                <input type="hidden" name="action" value="new" />
                name: <input type="text" name="resultset_name" /><br>
                alias: <input type="text" name="resultset_alias" /><br>
                statement: <input type="text" name="resultset_statement" /><br>
                note: <textarea name="resultset_note" /></textarea>
                </p>
                <input type="submit" value="Submit" />
            </form>

           <h2>Amministrazione Resultset/Gruppo</h2>
            <form action="manage_resources.php" method="POST">
                <input type="hidden" name="action" value="old" />
                <select name="resultset_id">
                    <?php foreach ($resultsets as $resultset) {
                        $id = $resultset->get_id();
                        $name = $resultset->get_alias();
                        ?>
                    <option value="<?php echo $id ?>">
                            <?php echo $name; ?>
                    </option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>

            <h2>Eliminazione Resultset</h2>
            <form action="manage_resultset.php" method="POST">
                <input type="hidden" name="action" value="delete" />
                <select name="resultset_id">
                    <?php foreach ($resultsets as $resultset) {
                        $id = $resultset->get_id();
                        $name = $resultset->get_alias();
                        ?>
                    <option value="<?php echo $id ?>">
                            <?php echo $name; ?>
                    </option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>
        </div>

        <div class="section">
        <?php
        $groups = get_groups();
        $users = get_users();
        ?>
            <h1>Gestione Utenti</h1>

            <h2>Creazione Utente</h2>
            <form action="manage_users.php" method="POST">
                <input type="hidden" name="action" value="new" />
                username: <input type="text" name="user_username" /><br />
                password: <input type="text" name="user_password" /><br />
                name: <input type="text" name="user_name" /><br />
                surname: <input type="text" name="user_surname" /><br />
                email: <input type="text" name="user_email" /><br />
                office: <input type="text" name="user_office" /><br />
                telephone: <input type="text" name="user_telephone" /><br />
                status: <select name="user_status">
                        <option>1</option>
                        <option>0</option>
                    </select><br />
                group: <select name="user_id_group">
                    <?php foreach ($groups as $group) {
                        $id = $group->get_id();
                        $name = $group->get_name();
                        $status = $group->get_status();
                        if($status==1) { ?>
                        <option value="<?php echo $id ?>"><?php echo $name; ?></option><?php
                        }
                    } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>

            <h2>Modifica Utente</h2>
            <form action="manage_users.php" method="POST">
                <input type="hidden" name="action" value="user_edit" />
                <select name="user_id">
                    <?php foreach ($users as $user) {
                        $id = $user->get_id();
                        $username = $user->get_username();
                        $status = $user->get_status();
                        $group_name = $user->get_group_name();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$username ($group_name) [$status]"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>

            <h2>Eliminazione Utente</h2>
            <form name="delete_user" action="manage_users.php" method="POST" onsubmit="return canc_user();">
                <input type="hidden" name="action" value="user_delete" />
                <select name="user_id">
                    <?php foreach ($users as $user) {
                        $id = $user->get_id();
                        $username = $user->get_username();
                        $status = $user->get_status();
                        $group_name = $user->get_group_name();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$username ($group_name) [$status]"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>
        </div>

        <div class="section">

            <h1>Gestione Gruppi</h1>

            <h2>Creazione Gruppo</h2>
            <form action="manage_groups.php" method="POST">
                <input type="hidden" name="action" value="new" />
                name: <input type="text" name="group_name" /><br />
                status: <select name="group_status">
                        <option>1</option>
                        <option>0</option>
                    </select><br />
                </p>
                <input type="submit" value="Submit" />
            </form>

            <h2>Modifica Gruppo</h2>
            <form action="manage_groups.php" method="POST">
                <input type="hidden" name="action" value="group_edit" />
                <select name="group_id">
                    <?php foreach ($groups as $group) {
                        $id = $group->get_id();
                        $name = $group->get_name();
                        $status = $group->get_status();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$name [$status]"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>

            <h2>Eliminazione Gruppo</h2>
            <form name="delete_group" action="manage_groups.php" method="POST" onsubmit="return canc_group();">
                <input type="hidden" name="action" value="group_delete" />
                <select name="group_id">
                    <?php foreach ($groups as $group) {
                        $id = $group->get_id();
                        $name = $group->get_name();
                        $status = $group->get_status();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$name [$status]"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>
        </div>

        <div class="section">
            <?php
            $plugins = get_plugins();
            ?>
            <h1>Gestione Plugin</h1>

            <h2>Creazione Plugin</h2>
            <form action="manage_plugins.php" method="POST">
                <input type="hidden" name="action" value="new" />
                name: <input type="text" name="plugin_name" /><br />
                configurationfile: <input type="text" name="plugin_configurationfile" /><br/>
                type: <select name="plugin_type" >
                    <option>link</option>
                    <option>single</option>
                </select>
                <br />
                note: <input type="text" name="plugin_note" /><br />
                </p>
                <input type="submit" value="Submit" />
            </form>

            <h2>Modifica Plugin</h2>
            <form action="manage_plugins.php" method="POST">
                <input type="hidden" name="action" value="plugin_edit" />
                <select name="plugin_id">
                    <?php foreach ($plugins as $plugin) {
                        $id = $plugin->get_id();
                        $name = $plugin->get_name();
//                        $configurationfile = $plugin->get_configurationfile();
//                        $type = $plugin->get_type();
//                        $note = $plugin->get_note();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$name"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>

            <h2>Eliminazione Plugin</h2>
            <form name="delete_plugin" action="manage_plugins.php" method="POST" onsubmit="return canc_plugin();">
                <input type="hidden" name="action" value="plugin_delete" />
                <select name="plugin_id">
                    <?php foreach ($plugins as $plugin) {
                        $id = $plugin->get_id();
                        $name = $plugin->get_name();
//                        $configurationfile = $plugin->get_configurationfile();
//                        $type = $plugin->get_type();
//                        $note = $plugin->get_note();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$name"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>
        </div>

        <div class="section">
        <?php
        $groupings = get_groupings();
        ?>
            <h1>Gestione Raggruppamenti</h1>

            <h2>Creazione Raggruppamento</h2>
            <form action="manage_groupings.php" method="POST">
                <input type="hidden" name="action" value="new" />
                name: <input type="text" name="grouping_name" /><br />
                alias: <input type="text" name="grouping_alias" /><br />
                </p>
                <input type="submit" value="Submit" />
            </form>

            <h2>Modifica Raggruppamento</h2>
            <form action="manage_groupings.php" method="POST">
                <input type="hidden" name="action" value="grouping_edit" />
                <select name="grouping_id">
                    <?php foreach ($groupings as $grouping) {
                        $id = $grouping->get_id();
                        $name = $grouping->get_name();
                        $alias = $grouping->get_alias();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$alias"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>

            <h2>Eliminazione Raggruppamento</h2>
            <form name="delete_grouping" action="manage_groupings.php" method="POST" onsubmit="return canc_grouping();">
                <input type="hidden" name="action" value="grouping_delete" />
                <select name="grouping_id">
                    <?php foreach ($groupings as $grouping) {
                        $id = $grouping->get_id();
                        $name = $grouping->get_name();
                        $alias = $grouping->get_alias();
                        ?>
                    <option value="<?php echo $id ?>"><?php echo "$alias"; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>
        </div>


		<div class="section">

		<h1>Gestione Notifiche</h1>
		
		<h2>Creazione notifica</h2>
            <form action="manage_notify.php" method="POST">
                <input type="hidden" name="action" value="new" />
                notificare operazioni sul resultset: <select name="resultset_id">
                    <?php foreach ($resultsets as $resultset) {
                        $id = $resultset->get_id();
                        $name = $resultset->get_alias();
                        ?>
                    <option value="<?php echo $id ?>">
                            <?php echo $name; ?>
                    </option>
                    <?php } ?>
                </select><br>
                nome del campo "chiave" per il record (pk del resultset): <input type="text" name="bmdid" /><br>
                oggetto mail: <input type="text" name="notify_name" /><br>
                query per recupero indirizzi mail a cui inviare la notifica: <input type="text" name="address_statement" /><br>
                query recupero dati da mostrare nella mail: <input type="text" name="data_statement" /><br>
                file di template della mail (xslt): <input type="text" name="xslt" />
				</p>
                <input type="submit" value="Submit" />
            </form>
		<?php $resultsets = get_notify();?>
            <h2>Eliminazione notifica</h2>
            <form action="manage_notify.php" method="POST">
                <input type="hidden" name="action" value="delete" />
                <select name="id">
                    <?php foreach ($resultsets as $resultset) {
                        $id = $resultset->get_id_notify();
                        $name = $resultset->get_notify_name();
                        ?>
                    <option value="<?php echo $id ?>">
                            <?php echo $name; ?>
                    </option>
                    <?php } ?>
                </select>
                <input type="submit" value="Submit" />
            </form>
		</div>
    </body>
</html>
