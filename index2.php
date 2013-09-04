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
        <link rel="stylesheet" type="text/css" href="include/superfish/css/superfish.css" media="screen">
        <link rel="stylesheet" type="text/css" href="include/superfish/css/superfish-navbar.css" media="screen">
<!--        <link href="include/js/style.css" rel="stylesheet" type="text/css" media="screen" />-->
        <link href="style2.css" rel="stylesheet" type="text/css" media="screen" />
	<!--<script type="text/javascript" src="include/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="include/js/jquery/jquery.main.js"></script>
        <script type="text/javascript" src="include/superfish/js/jquery-1.2.6.min.js"></script>
        <script type="text/javascript" src="include/superfish/js/hoverIntent.js"></script>
        <script type="text/javascript" src="include/superfish/js/superfish.js"></script>
        <script type="text/javascript"> 

            $(document).ready(function(){ 
                $("ul.sf-menu").superfish(); 
            }); 

        </script>
        <script type="text/javascript">
            
            $(document).ready(function(){

             $('#menu a').click(function() {
               var page = $(this).attr('href');
               if ( page != '#') {
                   $('#content').load( page ).slideDown();
                   return false;
               }
             });

            });
        </script>
        -->
    </head>
    <body>
        <table>
            <tr><td>
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
            </td></tr>
        <?php
        if($db_conn!="1")
        {
            // non è stato selezionato un db quindi non stampo il resto della pagina
            echo "</td></tr></table></body></html>";
            exit();
        }
        ?>
<!--        <div class="section">
            <h1>Gestione Resultset</h1>
-->
            <tr><td>
    <div id="menu">
        <ul id="sample-menu-4" class="sf-menu sf-navbar">
                <li>
                        <a class="sf-with-ul" href="#">Gestione Resultset<span class="sf-sub-indicator"> &#187;</span></a>
                        <ul>
                            <li><a href="index2.php?spec=rs_creation_permissions">Creazione con permessi</a></li>
                            <li><a href="forms/rs_manage_resultset.php">Creazione</a></li>
                            <li><a href="forms/manage_resultset.php">Eliminazione</a></li>
                        </ul>
                </li>
                <li>
                        <a class="sf-with-ul" href="#">Gestione Permessi<span class="sf-sub-indicator"> &#187;</span></a>
                        <ul>
                            <li><a href="#">Permessi per Gruppo</a></li>
                        </ul>
                </li>
                <li>
                        <a class="sf-with-ul" href="#">Gestione Utenti<span class="sf-sub-indicator"> &#187;</span></a>
                        <ul>
                            <li><a href="#">Creazione</a>
                            <li><a href="#">Modifica</a>
                            <li><a href="#">Eliminazione</a>
                        </ul>
                </li>
                <li>
                        <a class="sf-with-ul" href="#">Gestione Gruppi<span class="sf-sub-indicator"> &#187;</span></a>
                        <ul>
                            <li><a href="#">Creazione</a></li>
                            <li><a href="#">Modifica</a></li>
                            <li><a href="#">Eliminazione</a></li>
                        </ul>
                </li>
                <li>
                        <a class="sf-with-ul" href="#">Gestione Raggruppamenti<span class="sf-sub-indicator"> &#187;</span></a>
                        <ul>
                            <li><a href="#">Creazione</a></li>
                            <li><a href="#">Modifica</a></li>
                            <li><a href="#">Eliminazione</a></li>
                        </ul>
                </li>
                <li>
                        <a class="sf-with-ul" href="#">Gestione Notifiche<span class="sf-sub-indicator"> &#187;</span></a>
                        <ul>
                            <li><a href="#">Creazione</a></li>
                            <li><a href="#">Modifica</a></li>
                            <li><a href="#">Eliminazione</a></li>
                        </ul>
                </li>
        </ul>
    </div>
            </td></tr>
            <tr><td>
<!--<div id="content"></div>-->
<?php
    if (isset ($_GET['spec'])) {
        include($_GET['spec'].".php");
    }
?>
            </td></tr>
        </table>
</body>

</html>
