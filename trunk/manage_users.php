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
        $user_id = $_POST['user_id'];
        $user_username = $_POST['user_username'];
        $user_password = $_POST['user_password'];
        $user_name = $_POST['user_name'];
        $user_surname = $_POST['user_surname'];
        $user_email = $_POST['user_email'];
        $user_office = $_POST['user_office'];
        $user_telephone = $_POST['user_telephone'];
        $user_status = $_POST['user_status'];
        $user_id_group = $_POST['user_id_group'];

        if ($action == "new") {
            /* inserisco i dati dell'utente e ne prendo l'id */
            $new_user_id = insert_user($user_username, $user_password, $user_name, $user_surname,
                $user_email, $user_office, $user_telephone, $user_status, $user_id_group);
            if ($new_user_id) {
                echo "Utente <b>$user_username</b> inserito con successo";
            }
        }
        else if ($action=="edit")
        {
            /* modifico l'utente selezionato con i dati del form presente in fondo a questa pagina */
            $user_id = edit_user($user_id, $user_username, $user_name, $user_surname,
                $user_email, $user_office, $user_telephone, $user_status, $user_id_group, $user_password);
            echo "Utente <b>$user_username</b> modificato con successo";
        }
        else if ($action=="user_delete" && trim ($user_id!=""))
        {
            /* cancellazione di un utente */
            // prendo lo username dell'utente
            $users = get_users($user_id);
            foreach ($users as $user) $user_username = $user->get_username();
            // procedo con la cancellazione
            $connection = db_get_connection();
            $query_del = "DELETE from $T_USER where `id` = '$user_id' LIMIT 1";
            $risu_del = mysql_query($query_del);
            if ($risu_del) {
                echo "Utente <b>$user_username</b> cancellato";
            } else {
                echo "Errore durante la cancellazione dell'utente <b>$user_id</b>";
            }
            mysql_close($connection);
        }
        else if ($action=="user_edit")
        {
            /* form per modificare l'utente, devo recuperare i dati dal db */
            $groups = get_groups();
            $users = get_users($user_id);
            foreach ($users as $user) {
                $user_username = $user->get_username();
                $user_name = $user->get_name();
                $user_surname = $user->get_surname();
                $user_email = $user->get_email();
                $user_office = $user->get_office();
                $user_telephone = $user->get_telephone();
                $user_status = $user->get_status();
                $user_id_group = $user->get_id_group();
            }
            ?>
            <h2>Modifica Utente</h2>
            <form action="manage_users.php" method="POST" name="User">

            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            username: <input type="text" name="user_username" value="<?php echo $user_username ?>" /><br />
            password: <input type="text" name="user_password" /> (nota: se il campo viene lasciato vuoto, verrà mantenuta la password esistente)<br />
            name: <input type="text" name="user_name" value="<?php echo $user_name ?>" /><br />
            surname: <input type="text" name="user_surname" value="<?php echo $user_surname ?>" /><br />
            email: <input type="text" name="user_email" value="<?php echo $user_email ?>" /><br />
            office: <input type="text" name="user_office" value="<?php echo $user_office ?>" /><br />
            telephone: <input type="text" name="user_telephone" value="<?php echo $user_telephone ?>" /><br />
            status: <select name="user_status">
                    <option>1</option>
                    <option<?php if($user_status==0) echo " selected=\"selected\"" ?>>0</option>
                </select><br />
            group: <select name="user_id_group">
                <?php foreach ($groups as $group) {
                    $id_gr = $group->get_id();
                    $name_gr = $group->get_name();
                    $status_gr = $group->get_status();
                    if($status_gr==1) { ?>
                    <option value="<?php echo $id_gr ?>"
                        <?php if($user_id_group==$id_gr) echo "selected=\"selected\""; ?>><?php echo $name_gr; ?></option><?php
                    }
                } ?>
            </select><br />


            <input type="submit" value="Submit" />
        </form>
        <?php
        }
        ?>
        <p><a href="index.php">Torna indietro</a></p>
    </body>
</html>
