<?php



class tableLogiciels {

  private $logiciels = array();




// Protege des Failles XSS
  private function protectXss() {
    for ($i=0; $i < count($this->logiciels); $i++) { 
      
      if (is_array($this->logiciels[$i])) {
        foreach ($this->logiciels[$i] as $key => $value) {
          $this->logiciels[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
      } else {
        $this->logiciels[$i] = htmlspecialchars($this->logiciels[$i], ENT_QUOTES);
      }
    }
  }


/***************************************************************************   RECUPERATION DES DONNEES   **********************************************************************************/



  // Recupere et met dans la variable $logiciels tous les elemnts de la table dip_exp
  public function recupLogiciels(){
    $bdd = new BDD();


    $sql = "SELECT * FROM `log_icones`
            ORDER BY `log_cat` DESC, `log_index` ;";

    $bdd->requete($sql);

    $this->logiciels = $bdd->retourne_tableau();

    $this->protectXss();

    return $this->logiciels;

    
  }








/***************************************************************************   AFFICHAGES LOGICIELS   **********************************************************************************/

  // Affiche tous les Diplomes et les experiences
  public function affiche(){
    $result = "<tr>\n<td colspan=\"5\"><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'><input type='submit' name='ajout_log' value='Ajouter un nouveau Logiciel'></form></td>\n</tr>\n";
    $result .= "<tr>\n<th></th>\n<th>Categorie</th>\n<th>Designation</th>\n<th>Index</th>\n<th>Image</th>\n<th>Aperçu</th>\n</tr>\n";
    $i=1;
    foreach ($this->logiciels as $element) {
      $result .= "<tr>\n";
      $result .= "<td>$i</td>\n";
      $result .= "<td>".$element["log_cat"]."</td>\n";
      $result .= "<td>".$element["log_design"]."</td>\n";
      $result .= "<td>".$element["log_index"]."</td>\n";
      $result .= "<td>".$element["log_img"]."</td>\n";
      $result .= "<td><img src='../contents/img/contenu/logiciels/".$element["log_img"]."' alt='".$element["log_design"]."'/></td>\n";
      $result .= "<td><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
      $result .= "<input type='hidden' name='log_id' value='".$element["log_id"]."'>";

      $result .= "<input type='submit' name='modif_log' value='Modifier'>";
      $result .= "<input type='submit' name='suppr_log' value='Supprimer'></form></td>\n";
      $result .= "</tr>\n";
      $i++;
    }

    return $result;
  }



public function afficheModif($id){

    foreach ($this->logiciels as $element) {
      if($element["log_id"] == $id) {

        $web = ($element["log_cat"]=="web")? " selected" : "";
        $graph = ($element["log_cat"]=="graph")? " selected" : "";

        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='log_id' value='".$element["log_id"]."'>";

        // Affichage Logiciels
        $result .= "<fieldset>\n<legend> Logiciels : </legend>\n";
        $result .= "<label for='log_cat'>Catégorie : </label><br/>";
        $result .= "<select name='log_cat'>";
        $result .= "<option value='web' $web >Developpement Web</option>";
        $result .= "<option value='graph' $graph >Graphisme</option>";
        $result .= "</select><br />\n";
        $result .= "<label for='log_design'>Designation : </label><br/>";
        $result .= "<input type='text' name='log_design' value='".$element["log_design"]."'><br/>\n";
        $result .= "<label for='log_index'>Index : </label><br/>";
        $result .= "<input type='text' name='log_index' value='".$element["log_index"]."'><br/>\n";
        $result .= "<label for='log_img'>Image : </label><br/>";
        $result .= "<input type='text' name='log_img' value='".$element["log_img"]."'><br/>\n";
        $result .= "Aperçu : <img src='../contents/img/contenu/logiciels/".$element["log_img"]."' alt='".$element["log_design"]."'/>";
        $result .= "</fieldset>\n";
        
        
        $result .= "<input type='submit' name='enrModif_log' value='Valider Modifications'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";

        return $result;

      }
    }

}




public function modifier(){

    if(is_numeric($_POST["log_id"])) {

      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $log_id = $bdd->secureRqt($_POST["log_id"]);

      $log_cat = $bdd->secureRqt($_POST["log_cat"]);
      $log_design = $bdd->secureRqt($_POST["log_design"]);
      $log_index = $bdd->secureRqt($_POST["log_index"]);
      $log_img = $bdd->secureRqt($_POST["log_img"]);

      //met a jour la table log_icones
      $sql = "UPDATE `log_icones` 
                SET 
                `log_cat` = \"$log_cat\",
                `log_design` = \"$log_design\",
                `log_index` = \"$log_index\",
                `log_img` = \"$log_img\" 
                WHERE `log_id` = \"$log_id\" ;";
      return $bdd->requete($sql);

      

    }
 
}


public function afficheAjout(){



        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";

        // Affichage Logiciels
        $result .= "<fieldset>\n<legend> Logiciels : </legend>\n";
        $result .= "<label for='log_cat'>Catégorie : </label><br/>";
        $result .= "<select name='log_cat'>";
        $result .= "<option value='web' selected>Developpement Web</option>";
        $result .= "<option value='graph'>Graphisme</option>";
        $result .= "</select><br />\n";
        $result .= "<label for='log_design'>Designation : </label><br/>";
        $result .= "<input type='text' name='log_design' value=''><br/>\n";
        $result .= "<label for='log_index'>Index : </label><br/>";
        $result .= "<input type='text' name='log_index' value=''><br/>\n";
        $result .= "<label for='log_img'>Image : </label><br/>";
        $result .= "<input type='text' name='log_img' value=''><br/>\n";
        $result .= "Aperçu : <img src='' alt=''/>";
        $result .= "</fieldset>\n";
        
        
        $result .= "<input type='submit' name='enrAjout_log' value='Ajouter ce Logiciel'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;


}



public function ajouter(){


      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $log_cat = $bdd->secureRqt($_POST["log_cat"]);
      $log_design = $bdd->secureRqt($_POST["log_design"]);
      $log_index = $bdd->secureRqt($_POST["log_index"]);
      $log_img = $bdd->secureRqt($_POST["log_img"]);

      //met a jour la table log_icones
      $sql = "INSERT INTO `log_icones` (
              `log_id` ,
              `log_cat` ,
              `log_design` ,
              `log_index` ,
              `log_img`
              )
              VALUES (
              NULL ,
              \"$log_cat\",
              \"$log_design\",
              \"$log_index\",
              \"$log_img\");";
      return $bdd->requete($sql);


 
}



public function supprimer(){

    if(is_numeric($_POST["log_id"])) {

      $bdd = new BDD();

      $log_id = $bdd->secureRqt($_POST["log_id"]);


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      //met a jour la table log_icones
      $sql = "DELETE FROM `log_icones` WHERE `log_id` = \"$log_id\";";
      return $bdd->requete($sql);


    }
 
}




















}














?>