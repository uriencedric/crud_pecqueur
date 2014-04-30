<?php
if(!isset($_SESSION))
    session_start();
/********************************* ACCES DIRECT AUX TABLES **************************************/


/************************** Variables à personnaliser avec le nom de la base ******************************/

/* !!!!! ATTENTION SEULE OBLIGATION : LA CLE PRIMAIRE DOIT ETRE LA PREMIERE COLONNE DE CHAQUE TABLE !!!!! */

/* Exemple : $base = "Ma_BDD" */

$base = "persofolio";


/**********************************************************************************************************/

if(!isset($_SESSION["login"])){
  header('Location:login.php'); //
}

// Appel BDD
require_once ("class/bdd.php");

$page=(isset($_GET["page"])) ? $_GET["page"] : "";
$ligne=false;

// Recupere les titre des colonnes d'une table
function recup_Titre($base,$table) {
  $mod_ligne = new BDD($base);


  $mod_ligne->requete("DESC $table");

  $result = $mod_ligne->retourne_tableau();

  $col_name = array();
  $col_type = array();
  foreach ($result as $value) {
      $col_name[] = $value["Field"];
      $col_type[] = $value["Type"];
  }

  return array($col_name,$col_type);  
}


// MENU
// Recupere les tables de la base
function Menu($base) {
  $menu_bdd = new BDD($base);


  $menu_bdd->requete("SHOW TABLES IN $base");
    
  $menu = $menu_bdd->retourne_tableau();

  $tab_menu = array();

      foreach($menu as $element_menu) {
        foreach ($element_menu as $value) {
            $tab_menu[] = $value;
        }

      }
  return $tab_menu;
}

// Affiche les tables de la base

function affiche_Menu($tab) {
	$result ="<ul class='nav nav-pills nav-justified'>";
	foreach ($tab as $value) {
		$result .= "<li class='active'><a class='btn btn-default btn-lg' href='".$_SERVER['PHP_SELF']."?page=".$value."'>".$value."</a></li>";
	}
	return $result."</ul>";
}



// Table
// Recupere toutes les infos d'une table
function Table($base,$table) {
  $table_bdd = new BDD($base);


  $table_bdd->requete("SELECT * FROM $table");
    
  return $table_bdd->retourne_tableau();
  
}

// Affiche toutes les infos d'une table sous forme d'un tableau
function affiche_Table($tab) {
  $first = true;
  $id_name = true;
  $result = "";
  $nbcol = 1;
  foreach ($tab as $element) {
    if($first) {
      $result .="<tr>";
      foreach ($element as $key => $value) {
        if($id_name===true) $id_name = $key;
        $result .= "<th class='info'>".$key."</th>";

      }
      $result.="<th class='info'>action</th>";
      $result .= "</tr>";
      $first = false;
    }


    $nbcol += count($element);

    $result .="<tr>";
    $id_value = true;
    foreach ($element as $key => $value) {
      $result .= "<td>".$value."</td>";
    }

    $result .="<td>
                  <form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."#newline'>
                    <input type='hidden' name='id_val' value='".$id_name."/".$element[$id_name]."'> 
                    <input type='submit' name='modifier' value='Modifier' class='btn btn-info btn-block'>
                    <input type='submit' class='btn btn-danger btn-block ' name='confirmSuppr' value='Supprimer' title='Supprimer' >
                  </form>
                  </td>
          </tr>";  
  }

  $result.= " <tr>
                <td colspan='$nbcol'>
                  <form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."#newline'>
                    <input type='submit' name='ajouter' class='btn btn-success btn-lg' value='Ajouter une nouvelle ligne'>
                  </form>
                </td>
              </tr>";


  return $result;
  
}



//recupere une ligne d'une table en fonction de l'id
function ligne($base,$table,$id_name,$id){
  $table_bdd = new BDD($base);


  $table_bdd->requete("SELECT * FROM $table WHERE $id_name = \"$id\"");

  $result = $table_bdd->retourne_tableau();
    
  return $result[0];
}

// Affiche cette ligne
function affiche_ligne($tab,$id_name){
  $result = "<div class='panel panel-danger'> 
                <div class='panel-body bg-danger'>
                  <form id='newline' style='width:40%;margin:0 auto;' class='form-horizontal'method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
  foreach ($tab as $key => $value) {
    if($key != $id_name){
      $result .= "<div class='newline form-group'>
                    <label class= 'col-sm-2 control-label' for='$key'>$key : </label>
                  <div class='col-sm-10'><input class= 'form-control' type='text' name='$key' value='$value'></div></div>";
    }else {
      $result .= "<div class='newline'>
                    <input type='hidden' name='$key' value='$value'>
                      <label>$key : </label>
                      <span>$value</span>
                  </div>";
    }
  }

  $result .= "<div class='panel-default '> 
                <div class='panel-body'>
                  <input class='btn btn-block btn-primary btn-sm' style='width:40%;margin:2px auto;' type='submit' name='valider' value='Appliquer les Modifications'>
                  <input class='btn btn-block btn-warning btn-sm' type='reset' value='reset'  style='width:40%;margin:0 auto;'></div></div>";

  return $result."</form>
              </div>
            </div>";
}



// Formulaire de nouvelle ligne
function affiche_new_ligne($base,$table){

    $mod_ligne = new BDD($base);

    $titre = recup_Titre($base,$table);
    $col_name = $titre[0];
    $col_type = $titre[1];


    $id_name = array_shift($col_name);

  $result = "<form id='newline' method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";

  // Primary key
  $result .= "<div class='newline'><label for='$id_name'>$id_name : </label><input id='primaryKey' type='text' name='$id_name' value='' disabled ><input id='primaryKeyCheck' type='checkbox' name='id' onchange='changerId(this);'>Cocher pour modifier l'ID (Réservé aux Utilisateurs Avertis).</div>";

  foreach ($col_name as $value) {
      $result .= "<div class='newline'><label for='$value'>$value : </label><input type='text' name='$value' value=''></div>";
  }

  $result .= "<input type='submit' name='addLigne' value='Ajouter cette ligne'><input type='reset' value='reset'>";

  return $result."</form>";
}



// Modification d'une ligne
function modifier_ligne($base,$table) {
    $mod_ligne = new BDD($base);

    $titre = recup_Titre($base,$table);
    $col_name = $titre[0];
    $col_type = $titre[1];

    // Debut SQL
    $sql = "UPDATE $table SET ";

    for ($i=1; $i < count($col_name) ; $i++) { 
      if($i==1){
        $sql .= " `".$col_name[$i]."` = '".$_POST[$col_name[$i]]."'";
      }else {
        $sql .= ", `".$col_name[$i]."` = '".$_POST[$col_name[$i]]."'";
      }
    }

    $sql .= " WHERE `".$col_name[0]."` = '".$_POST[$col_name[0]]."' ;";
    $mod_ligne->requete($sql);
}



// Insertion d'une nouvelle ligne
function inserer_ligne($base,$table) {
    $mod_ligne = new BDD($base);

    $titre = recup_Titre($base,$table);
    $col_name = $titre[0];
    $col_type = $titre[1];

    // Debut SQL
    $sql = "INSERT INTO $table (";

    // Keys
    for ($i=0; $i < count($col_name) ; $i++) { 
      if($i==0){
          $sql .= "`".$col_name[$i]."`";

      } else {
          $sql .= ", `".$col_name[$i]."`";
     
      }
    }

    // VALUES
    for ($i=0; $i < count($col_name) ; $i++) { 
      if($i==0){
          $value = (isset($_POST[$col_name[$i]])) ? $_POST[$col_name[$i]] : "";
          $sql .= ') VALUES ( "'.$value.'"';

      } else {
          $sql .= ', "'.$_POST[$col_name[$i]].'"';
     
      }
    }
    $sql .= ");";

    $mod_ligne->requete($sql);

}


// Suppression d'une ligne
function supprimer_ligne($base,$table,$id_name,$id) {
  $suppr_ligne = new BDD($base);

  $sql = "DELETE FROM $table WHERE $id_name = \"$id\"";

  $suppr_ligne->requete($sql);


}



// Insere une nouvelle ligne
if(isset($_POST["addLigne"])) {
  inserer_ligne($base,$page);
}


// Supprime une ligne
if(isset($_POST["supprimer"])) {
  $exp = explode("/", $_POST["id_val"]);
  supprimer_ligne($base,$page,$exp[0],$exp[1]);
}



// Modifie ligne dans BDD
if(isset($_POST["valider"])) {
  modifier_ligne($base,$page);
}


?>


<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Back Office Base "<?php echo $base ?>"</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  </head>
  <body>
  <!-- Top site Panel -->
  <div class="panel-body bg-info">

    <header>

      <h1 class="text-center text-primary">BackOffice - Accès direct aux tables</h1>

    </header>

    <div class="text-center ">

      <p>Nom de la Base Selectionnée : <span><?php echo $base; ?></span></p>
      <p>Tables Disponibles :</p>

    </div>

  </div> 
  <!-- End panel top site -->


      <?php echo affiche_Menu(Menu($base)); ?>

  <!-- Table page part -->

    
  <!-- top table -->
   
  <div class="panel panel-default panel-body">

    <p class="text-center"><?php if($page != "") echo "Nom de la table en cours : <span>".$page."</span>"; ?></p>

  </div>

  <!-- End top table -->

  <!-- Data base row showed -->
    
  <div class="panel panel-primary table-responsive">

    <table class='table table-bordered table-hover '>
      <?php if($page != "") echo (affiche_Table(Table($base,$page))); ?>
    </table>
    
  </div>
      
  <!-- End Data base row showed -->

  <!-- Table Page part end -->




  <?php 
   //Perform actions on data base table



  if(isset($_POST["modifier"])) {

    echo "<p><a href='".$_SERVER['PHP_SELF']."?page=".$page."'>Annuler</a></p>";
    $exp = explode("/", $_POST["id_val"]);
    echo affiche_ligne(ligne($base,$page,$exp[0],$exp[1]),$exp[0]);

  }

  if(isset($_POST["ajouter"])){
    echo "<p><a href='".$_SERVER['PHP_SELF']."?page=".$page."'>Annuler</a></p>";
    echo affiche_new_ligne($base,$page);
  }

  if(isset($_POST["confirmSuppr"])){
    echo "<p>Confirmer la suppression : ".$_POST["id_val"]."</p>";
    echo "<div class='panel panel-default' style='width:20%;'>
            <form id='newline' method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>
              <input type='hidden' name='id_val' value='".$_POST["id_val"]."'>
              <input class='btn btn-info btn-danger btn-block' type='submit' name='supprimer' value='Confirmer la suppression'>
              <input class='btn btn-warning btn-block' type='submit' name='annulSuppr' value='Annuler la suppression'>
            </form>
          </div>";
  }

   ?>


    <script type="text/javascript">

    function changerId(e) {
      if(e.checked) document.getElementById("primaryKey").disabled = false;
      else document.getElementById("primaryKey").disabled = true;
    }

    </script>

     <a href="login.php?Deco" class='btn btn-primary btn-lg'>Retour vers login</a><!-- Le ? = variables GET -->


  </body>
</html>
