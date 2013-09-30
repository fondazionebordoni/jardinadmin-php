<?php
session_start();

$DIR = dirname(__FILE__);
$INCLUDE_DIR = $DIR."/include/";
$IMAGE_DIR = $DIR."/icons/";
//require_once($INCLUDE_DIR."header.php");
require_once($INCLUDE_DIR."db_connection.php");
//require_once($INCLUDE_DIR.'check.php');
//require_once($INCLUDE_DIR.'menu_admin.php');

if ( isset($_GET['spec'])) {
  $_SESSION['specifica'] = $_GET['spec'];
}
else { ?>
<script type="text/javascript">
  location.replace("index2.php");
</script>
<? }

/* specifica corrisponde alla tabella d'interesse a
 * meno che non si debbano modificare transizioni e coperture */
$specifica = $_SESSION['specifica'];
/*
$risorsa = get_resource_id($specifica);
if (!(isset($_SESSION['level']) && check_permission($_SESSION['user'], $risorsa, "lettura"))) {	?>
<script type="text/javascript">
  location.replace("index2.php");
</script>
<?php
  } */
?>

<div id="main">
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
  </script> <script type="text/javascript">
    function editList(){
      ruleWin = window.open('newList.php?rulekey=<? echo $specifica ?>', 'modWin','scrollbars,resizable,width=600,height=600')
    }
  </script> <script type="text/javascript">
    function editSingle(){
      ruleWin = window.open('newSingle.php?rulekey=<? echo $specifica ?>', 'modWin','scrollbars,resizable,width=600,height=600')
    }
  </script> <?php

  $link = db_connect();
  $direction = $_GET['direction'];
  if ($direction =="DESC")
    $direction ="ASC";
  else
    $direction ="DESC";
  $orderingFilter = $_GET['filter'];
  if ($orderingFilter =='')
    $orderBy ="" ;
  else
    $orderBy ="order by ".$orderingFilter." ".$direction;

  $qMain = "SELECT * FROM `".$specifica."` ".$orderBy;
  $result = mysql_query($qMain);
  $field = mysql_num_fields($result);

  $qMainTotalExport = $qMain;

  $i = 0;
  while ($i < mysql_num_fields($result)) {
    $fields_name[] = mysql_field_name( $result, $i ); //l'array contiene i nomi dei campi della tabella richiesta
    $meta = mysql_fetch_field($result,$i);
    if ($meta->primary_key)	$pKey[$i] = $fields_name[$i];//individuazione della primary key della tabella per le modifiche
    //	if ($meta->multiple_key) $pKey[$i] = $fields_name[$i];//l'array pKey contiene i nomi dei campi che formano la chiave per la tabella
    $i++;
  }
  if ( isset($_POST["submit_mult"]) && strcmp($_POST["submit_mult"], "restore") == 0) {
    $row = mysql_fetch_array($result);
    while ($row) {
      $kSV = $row['id'];
      if (strcmp($_POST['check_'.$kSV], "on") == 0) {
        $res = restore($kSV); // ('id',valore_di_id)
      }
      $row = mysql_fetch_array($result);
    }
  }

  if ( isset($_POST["submit_mult"]) && strcmp($_POST["submit_mult"], "delete") == 0) {
    $keyStringName = $_POST['pkeys'];
    $keyArrayName = explode(":", $keyStringName);

    $row = mysql_fetch_array($result);
    while ($row) {
      foreach ($keyArrayName as $kn) {
        $kSV .= $row[$kn].":";
      }
      $kSV = substr($kSV, 0, -1); //devo ricostruire la stringa dai valori delle chiavi prelevati dal DB

      if (strcmp($_POST['check_'.$kSV], "on") == 0) {
        $keyArrayValue = explode(":", $kSV);//metto in un'array i valori delle chiavi e lo passo alla funzione di delete

        $res = delete($specifica, $keyArrayName, $keyArrayValue);//devo gestire il risultato della funzione
      }

      unset($kSV);

      $row = mysql_fetch_array($result);
    }
  }


  // COMMIT MODIFICA RECORDS //////////////
  //

  if ( isset($_POST["submit_mult"]) && strcmp($_POST["submit_mult"], "modify_commit") == 0) {
    $num_modified_records = $_POST['numrecords']; // numero record da modificare -1
    $keyStringName = $_POST['kan'];// contiene i nomi delle chiavi...buoni per tutti i record
    $keyArrayName = explode(":", $keyStringName);

    for ($j=0;$j<=$num_modified_records;$j++) {
      $keyStringValue = explode(":",$_POST['kav_'.$j]);
      for ($i=0;$i<count($keyArrayName);$i++) {
        $keyArray[$j][$keyArrayName[$i]] = $keyStringValue[$i];
      }
    }

    for ($j=0;$j<=$num_modified_records;$j++) {
      for ($i=0;$i<$field;$i++) {
        $meta = mysql_fetch_field($result,$i);
        if (! $meta->primary_key) $newArray[$j][$fields_name[$i]]=$_POST[$fields_name[$i].":".$j];
      }
      $res = commit_modify($specifica, $keyArray[$j], $newArray[$j]);
      if (! $res) mysql_error();
    }
  }
  // FINE COMMIT MODIFICA RECORDS //////////////

  // GESTIONE RICHIESTA MODIFICA RECORDS //////////////
  //
  if ( isset($_REQUEST["submit_mult"]) && strcmp($_REQUEST["submit_mult"], "modify_request") == 0) {
    $keyStringName = $_POST['pkeys'];
    $keyArrayName = explode(":", $keyStringName);

    $result = mysql_query($qMain);
    $row = mysql_fetch_array($result);
    print "<form name=\"Users\" action=\"modulo.php?spec=".$specifica."&pa=".$_GET['pa']."\" method=\"post\">";
    print "<table>";
    //print_r($_POST);
    $record_id = 0;// tiene traccia del numero di record da modificare
    while ($row) {
      foreach ($keyArrayName as $kn) {
        $kSV .= $row[$kn].":";
      }
      $kSV = substr($kSV, 0, -1); //devo ricostruire la stringa dai valori delle chiavi prelevati dal DB
      //		echo $_POST['check_'.$kSV];
      if (strcmp($_POST['check_'.$kSV], "on") == 0) {
        print "<tr><td>";
        $keyArrayValue = explode(":", $kSV);//metto in un'array i valori delle chiavi e lo passo alla funzione di delete

        $res = modify_request($specifica, $keyArrayName, $keyArrayValue,$record_id);//devo gestire il risultato della funzione
        $keyStringValue = $kSV;
        print "<td><input type=hidden value=\"".$kSV."\" name=\"kav_".$record_id."\"></td>";
        $record_id++;
        print "</tr></td>";
      }
      unset($kSV);

      $row = mysql_fetch_array($result);
    }
    print "<td><input type=hidden value=\"".($record_id-1)."\" name=\"numrecords\"></td>";
    print "<td><input type=hidden value=\"".$keyStringName."\" name=\"kan\"></td>";
    print "<tr><td><input type=hidden name=submit_mult value=modify_commit /><button class=\"mult_submit\" type=\"submit\" value=\"Invia Modifiche\">Invia Modifiche</button></td></tr>";
    print "</table>";
    print "</form>";
  //	print_r($_POST);
  // FINE GESTIONE RICHIESTA MODIFICA RECORDS //////////////
  }

  else {

  // LISTA RECORDS DELLA TABELLA RICHIESTA...viene stampata a meno che non si debbano modificare dei record
  // In questo caso, si stampano i form per le modifiche
  //

    $result = mysql_query($qMain);
    $field = mysql_num_fields($result);

    $i = 0;
    while ($i < mysql_num_fields($result)) {
      $fields_name[] = mysql_field_name( $result, $i ); //l'array contiene i nomi dei campi della tabella richiesta
      $meta = mysql_fetch_field($result,$i);
      if ($meta->primary_key)	$pKey[$i] = $fields_name[$i];//individuazione della primary key della tabella per le modifiche
      $i++;
    }

    foreach ($pKey as $pk) {
      $pks .= $pk . ":";
    }
    $pks = substr($pks, 0, -1);
    //	echo "pkey ".$pks;
    ?> <?
//    $q_ris = "SELECT * FROM `s_resources` WHERE nome='".$specifica."'";
//    $q_res = mysql_query($q_ris);
//    $risorsa = mysql_fetch_array($q_res);
//    if (isset($_SESSION['level']) && check_permission($_SESSION['user'],$risorsa['id'],"inserimento")) {
      ?>
  <h3 class="center"><a href="javascript:editSingle();">Aggiungi <?php echo $specifica; ?></a>
    / <a href="javascript:editList();">Aggiungi Lista <? echo $specifica; ?></a>
  </h3>
    <?
//    }
    ?>

    <?php
    // INIZIO MENU DI NAVIGAZIONE MULTIPAGINA /////////////////////

    // numero di record visualizzabili per pagina
    $arr_recPag = array(30=>30,50=>50,100=>100,1500=>1500);
    // controllo quanti record mostrare
    if(isset($_POST['recPag'])) $_SESSION['recPag'] = $_POST['recPag'];
    if(!isset($_SESSION['recPag'])) $_SESSION['recPag'] = 30;
    $recPag = $_SESSION['recPag'];
    // controllo pagina attuale (se è stata selezionata)
    $_GET['pa'] = ceil($_GET['pa']);
    if(!isset($_GET['pa']) || !is_numeric($_GET['pa'])) $_GET['pa'] = 0;

    $num_rows = mysql_num_rows($result);
    // se i record ottenuti sono più di quelli che posso mostrare in una sola pagina, ripeto la query aggiungendo limit e preparo il menu di navigazione
    if($num_rows>$recPag) {
      $qMain .= " limit ".($recPag*$_GET['pa']).", $recPag";
      $result = mysql_query($qMain);

      // pagine necessarie per visualizzare tutti i risultati
      $pag_tot = ceil($num_rows / $recPag);
      // inizio a preparare il menu multipagina
      foreach ($_GET as $key => $val) {
        if($key!="pa") {
          if($link_multi == "") $link_multi = "?{$key}={$val}";
          else $link_multi .= "&{$key}={$val}";
        }
      }
      $link_multi = $_SERVER['PHP_SELF'].$link_multi;

      // se ci sono più di 11 pagine, metto il link alle prime 2, alle ultime 2 e poi alle 9 pagine circostanti
      if($pag_tot > 11) {
        if($_GET['pa']<6) {
        // sto nelle prime pagine, stampo i primi 10 link, poi gli ultimi 2
          for($mm=0;$mm<10;$mm++) {
            if($mm!=$_GET['pa']) $menu_multi .= "<a href=\"{$link_multi}&pa={$mm}\">".($mm+1)."</a> - ";
            else $menu_multi .= "<strong>".($mm+1)."</strong> - ";
          }
          $menu_multi .= "... - ";
          $menu_multi .= "<a href=\"{$link_multi}&pa=".($pag_tot-2)."\">".($pag_tot-1)."</a> - ";
          $menu_multi .= "<a href=\"{$link_multi}&pa=".($pag_tot-1)."\">".($pag_tot)."</a> - ";
        } else if ($_GET['pa']>($pag_tot - 8)) {
          // sto nelle ultime pagine, stampo i primi 2 e gli ultimi 10 link
            $menu_multi .= "<a href=\"{$link_multi}&pa=0\">1</a> - ";
            $menu_multi .= "<a href=\"{$link_multi}&pa=1\">2</a> - ";
            $menu_multi .= "... - ";
            for($mm=($pag_tot-10);$mm<$pag_tot;$mm++) {
              if($mm!=$_GET['pa']) $menu_multi .= "<a href=\"{$link_multi}&pa={$mm}\">".($mm+1)."</a> - ";
              else $menu_multi .= "<strong>".($mm+1)."</strong> - ";
            }
          } else {
          // mi trovo in mezzo al menu, stampo i link alle prime pagine, alle ultime due, poi alle pagine più vicine a quella attuale
            $menu_multi .= "<a href=\"{$link_multi}&pa=0\">1</a> - ";
            $menu_multi .= "<a href=\"{$link_multi}&pa=1\">2</a> - ";
            if($_GET['pa']!=6) $menu_multi .= "... - ";
            for($mm=($_GET['pa']-4);$mm<($_GET['pa']+5);$mm++) {
              if($mm!=$_GET['pa']) $menu_multi .= "<a href=\"{$link_multi}&pa={$mm}\">".($mm+1)."</a> - ";
              else $menu_multi .= "<strong>".($mm+1)."</strong> - ";
            }
            $menu_multi .= "... - ";
            $menu_multi .= "<a href=\"{$link_multi}&pa=".($pag_tot-2)."\">".($pag_tot-1)."</a> - ";
            $menu_multi .= "<a href=\"{$link_multi}&pa=".($pag_tot-1)."\">".($pag_tot)."</a> - ";
          }
      }
      else {
        for($mm=0;$mm<$pag_tot;$mm++) {
          if($mm!=$_GET['pa']) $menu_multi .= "<a href=\"{$link_multi}&pa={$mm}\">".($mm+1)."</a> - ";
          else $menu_multi .= "<strong>".($mm+1)."</strong> - ";
        }
      }
      $menu_multi = substr($menu_multi,0,-2);
    }
    // preparo la visualizzazione del numero di record trovati
    if($menu_multi=="") {
    // c'è una sola pagina di risultati
      if($num_rows==0) $menu_risu = "Risultati: 0";
      else $menu_risu = "Risultati 1 - $num_rows su $num_rows";
    }
    else {
    // ci sono più pagine
      $num_rows2 = mysql_num_rows($result);
      if($num_rows2 == 0) {
      // ci si è spostati troppo avanti col menu multipagina, riporto il navigatore sulla prima pagina di risultati
        print "<script type=\"text/javascript\">location.replace(\"{$link_multi}&pa=0\")</script>";

      }
      $rec_ini = ($_GET['pa']*$recPag)+1;
      $rec_fin = $rec_ini + $num_rows2 - 1;
      $menu_risu = "Risultati $rec_ini - $rec_fin su $num_rows";
    }

    print "<form name=\"record_pagina\" action=\"{$link_multi}&pa=0\" method=\"post\">
	<table class=\"result\"><tr>
	<td>$menu_risu &nbsp; &nbsp; </td>";
    print "<td align=\"right\"><select name=\"recPag\">";

    foreach ($arr_recPag as $key => $val) {
      if($key!=$recPag) print "<option value=$key>$val</option>";
      else print "<option value=$key selected>$val</option>";
    }

    print "</select> <input type=\"submit\" value=\"Record per pagina\"></td></tr>";
    if($menu_multi != "") print "<tr><td colspan=\"2\" align=\"center\">Pagine trovate: $menu_multi</td></tr>";
    print "</table></form><br>";

    // FINE MENU DI NAVIGAZIONE MULTIPAGINA /////////////////////
    ?>

  <form name="Users" action="modulo.php?spec=<? echo $specifica; ?>&pa=<?php echo $_GET['pa']; ?>"
        method="post"><input type=hidden name=pkeys value="<?php echo $pks ?>">

    <table class="result" style="border-bottom: 1px solid #878787">
      <tr>
          <?php
	/* I nomi dei campi della tabella richiesta vengono stampati nella prima riga */
          for ( $i = 0; $i < $field; $i++ ) { ?>
        <th><a
            href='modulo.php?spec=<? echo $specifica; ?>&filter=<? echo $fields_name[$i] ?>&direction=<?echo $direction ?>'><? echo $fields_name[$i] ?></a></th>
            <?php } ?>
        <th>Op.</th>
      </tr>

        <?php
        $num_rows2 = mysql_num_rows($result);
        for ($t=0;$t<$num_rows2;$t++) {
          $row = mysql_fetch_array($result);

          // alterno il colore delle righe
          if($t%2==0) $color_tr = "#eeeeec";
          else $color_tr = "#ffffff";
          // se è l'ultima riga, aggiungo un bordo inferiore
          if($t==($num_rows2-1)) $border_bottom = "style=\"border-bottom: 2px solid #878787\"";
          print "<tr bgcolor=\"$color_tr\" onMouseOver=this.style.backgroundColor=\"#ffe84e\" onMouseOut=this.style.backgroundColor=\"$color_tr\" $border_bottom>";

		/* per ogni riga vengono stampati tutti i campi, identificati coll'indice */
          for ( $i = 0; $i < $field; $i++ ) { ?>
      <td style="border-left: 1px solid #878787; border-right: 1px solid #878787"><?php
              if (strcmp($fields_name[$i],"codice_cluster")==0) {
                $q = "SELECT descrizione FROM `cluster` WHERE codice='".$row[$i]."'";
                $q_res = mysql_query($q);
                $row_clus = mysql_fetch_array($q_res);
                echo $row_clus['descrizione'];
              }
              else if (strcmp($fields_name[$i],"id_stazione")==0) {
                  $q = "SELECT nome FROM `stazione` WHERE id='".$row[$i]."'";
                  $q_res = mysql_query($q);
                  $row_clus = mysql_fetch_array($q_res);
                  echo $row_clus['nome'];
                }
                else if (strcmp($fields_name[$i],"codice_comune")==0) {
                    $q = "SELECT nome FROM `comune` WHERE codice_co_istat='".$row[$i]."'";
                    $q_res = mysql_query($q);
                    $row_com = mysql_fetch_array($q_res);
                    echo $row_com['nome'];
                  }
                  else {
                    echo $row[$i];	// poi vengono stampati i record veri e propri
                  }
              ?></td>
              <?php
              $meta = mysql_fetch_field($result,$i);
              if ($meta->primary_key) $pKeyValue[$i] = $row[$i];//individuazione dei valori della primary key della tabella per il checkbox
            }

            foreach ($pKeyValue as $pk) {
              $pksv .= $pk . ":";
            }
            $pksv = substr($pksv, 0, -1); ?>
            <?
            if (isset($_SESSION['level']) && (check_permission($_SESSION['user'],$risorsa['id'],"modifica") || check_permission($_SESSION['user'],$risorsa['id'],"cancellazione"))) {
              ?>
      <td style="border-right: 1px solid #878787"><input type="checkbox" name="check_<?php echo $pksv ?>" /></td>
            <?
            }
            ?>


      </tr>
          <?php
          unset($pKeyValue);
          unset($pksv);
        }
        unset($num_rows);
        ?>
    </table>

    <div id="modify"><?php
        if (isset($_SESSION['level']) && (check_permission($_SESSION['user'],$risorsa['id'],"modifica") || check_permission($_SESSION['user'],$risorsa['id'],"cancellazione"))) {
          ?>
      <input type=button onclick="checkTutti()" value="Seleziona tutti" />
      <input type=button onclick="uncheckTutti()" value="Deseleziona tutti" />
      Se selezionati: <?php
        }
        if (isset($_SESSION['level']) && check_permission($_SESSION['user'],$risorsa['id'],"modifica")) {
          ?> <input type=submit class=modify name="submit_mult"
             value="modify_request" title="Modifica" alt="Modifica"> <!--  	    <button class="mult_submit" type="submit" name="submit" value="modify_request" title="Modifica">
                         <img class="icon" src="images/b_edit.png" title="Modifica" alt="Modifica" width="16" height="16" /> </button>-->
        <?
        }
        if (isset($_SESSION['level']) && check_permission($_SESSION['user'],$risorsa['id'],"cancellazione")) {
          ?> <input type=submit class=delete name="submit_mult" value="delete"
             title="Cancella" alt="Cancella">
               <?php
               }
               if ($specifica == "storico_modifiche") {
                 if (isset($_SESSION['level']) && check_permission($_SESSION['user'],$risorsa['id'],"modifica")) {
            ?> <input type=submit class=restore name="submit_mult"
             value="restore" title="Ripristina Valore" alt="Ripristina Valore">
                 <?php
                 }
               }
               ?>
      </form>
      <form name="exp" action="/include/export.php" method="post">
        <input type="hidden" name="query" value="<?php echo $qMain; ?>">
        <input type="hidden" name="nomefile" value="<? echo $specifica; ?>">
        esporta il risultato in formato excel (csv): <input type=submit value="esporta pagina">
      </form>
     <form name="exptot" action="/include/export.php" method="post">
        <input type="hidden" name="query" value="<?php echo $qMainTotalExport; ?>">
        <input type="hidden" name="nomefile" value="<? echo $specifica; ?>">
        esporta il risultato in formato excel (csv): <input type=submit value="esporta tutto">
      </form>
        <?php if($menu_multi != "") print "<p align=\"center\">Pagine trovate: $menu_multi</p>"; ?>
    </div>

    <?php } // LISTA RECORDS DELLA TABELLA RICHIESTA ?>
</div>

<?php
//echo $qMain;
//require_once($INCLUDE_DIR.'footer.php'); ?>