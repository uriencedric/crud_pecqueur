<?php



class tableDesignations {

  private $designations = array();





// Protege des Failles XSS
  private function protectXss() {
    for ($i=0; $i < count($this->designations); $i++) { 
      
      if (is_array($this->designations[$i])) {
        foreach ($this->designations[$i] as $key => $value) {
          $this->designations[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
      } else {
        $this->designations[$i] = htmlspecialchars($this->designations[$i], ENT_QUOTES);
      }
    }
  }


/***************************************************************************   RECUPERATION DES DONNEES   **********************************************************************************/



  // Recupere et met dans la variable $realisations tous les elements de la table realisation
  public function recupDesignations(){
    $bdd = new BDD();


    $sql = "SELECT 
            `des_id`,
            `des_name`,
            `des_page`,

            `fr_id`,
            `fr_traduction`,

            `en_id`,
            `en_traduction`

            FROM `designations`
            INNER JOIN `fr` ON (`des_name` = `fr_designation`)
            INNER JOIN `en` ON (`des_name` = `en_designation`)
            ORDER BY `des_page`;";

    $bdd->requete($sql);

    $this->designations = $bdd->retourne_tableau();

    $this->protectXss();

    return $this->designations;

  }




/***************************************************************************   AFFICHAGES DESIGNATIONS   **********************************************************************************/



  // Affiche toutes les designations
  public function affiche(){
    $result = "<tr>\n<td colspan=\"5\"><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'><input type='submit' name='ajout_des' value='Ajouter une nouvelle designation'></form></td>\n</tr>\n";
    $result .= "<tr>\n<th></th>\n<th>Designation</th>\n<th>Page Concernée</th>\n<th>Traduction FR</th>\n<th>Traduction EN</th>\n</tr>\n";
    $i=1;
    foreach ($this->designations as $element) {
      $result .= "<tr>\n";
      $result .= "<td>$i</td>\n";
      $result .= "<td>".$element["des_name"]."</td>\n";
      $result .= "<td>".$element["des_page"]."</td>\n";
      $result .= "<td>".$element["fr_traduction"]."</td>\n";
      $result .= "<td>".$element["en_traduction"]."</td>\n";
      $result .= "<td><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
      $result .= "<input type='hidden' name='des_id' value='".$element["des_id"]."'>";
      $result .= "<input type='hidden' name='id_trad' value='".$element["fr_id"]."'>";
      $result .= "<input type='submit' name='modif_des' value='Modifier'>";
      $result .= "<input type='submit' name='suppr_des' value='Supprimer'></form></td>\n";
      $result .= "</tr>\n";
      $i++;
    }


    return $result;
  }



public function afficheModif($id){

    foreach ($this->designations as $element) {
      if($element["des_id"] == $id) {



        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='des_id' value='".$id."'>";
        $result .= "<input type='hidden' name='id_trad' value='".$element["fr_id"]."'>";

        // Affichage Designation
        $result .= "<fieldset>\n<legend> Designation : </legend>\n";
        $result .= "<label for='des_name'>Nom : </label><br/>";
        $result .= "<input type='text' name='des_name' value='".$element["des_name"]."'><br/>\n";
        $result .= "<label for='des_page'>Page Concernée : </label><br/>";
        $result .= "<input type='text' name='des_page' value='".$element["des_page"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<textarea name='fr_traduction'>".$element["fr_traduction"]."</textarea>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<textarea name='en_traduction'>".$element["en_traduction"]."</textarea>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrModif_des' value='Valider Modifications'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;

      }
    }

}


public function modifier(){

    if(is_numeric($_POST["des_id"]) && is_numeric($_POST["id_trad"])) {

      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $des_id = $bdd->secureRqt($_POST["des_id"]);
      $id_trad = $bdd->secureRqt($_POST["id_trad"]);

      $des_name = $bdd->secureRqt($_POST["des_name"]);
      $des_page = $bdd->secureRqt($_POST["des_page"]);
      $fr_traduction = $bdd->secureRqt($_POST["fr_traduction"]);
      $en_traduction = $bdd->secureRqt($_POST["en_traduction"]);

      //met a jour la table designations
      $sql = "UPDATE `designations` 
                SET 
                `des_name` = \"$des_name\",
                `des_page` = \"$des_page\" 
                WHERE `des_id` = \"$des_id\" ;";
      $req1 = $bdd->requete($sql);

      // Met a jour la designation dans la table FR
      $sql = "UPDATE `fr` 
                SET  
                `fr_designation` = \"$des_name\", 
                `fr_traduction` = \"$fr_traduction\"
                WHERE `fr_id` = \"$id_trad\" ;";
      $req2 = $bdd->requete($sql);

      // Met a jour la designation dans la table EN
      $sql = "UPDATE `en` 
                SET  
                `en_designation` = \"$des_name\", 
                `en_traduction` = \"$en_traduction\"
                WHERE `en_id` = \"$id_trad\" ;";
      $req3 = $bdd->requete($sql);


      return array($req1,$req2,$req3);

    }
 
}

    

  

public function afficheAjout(){

        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";

        // Affichage Designation
        $result .= "<fieldset>\n<legend> Designation : </legend>\n";
        $result .= "<label for='des_name'>Nom : </label><br/>";
        $result .= "<input type='text' name='des_name' value=''><br/>\n";
        $result .= "<label for='des_page'>Page Concernée : </label><br/>";
        $result .= "<input type='text' name='des_page' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<textarea name='fr_traduction'></textarea>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<textarea name='en_traduction'></textarea>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrAjout_des' value='Créer Cette Designation'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;


}



public function ajouter(){


      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $des_name = $bdd->secureRqt($_POST["des_name"]);
      $des_page = $bdd->secureRqt($_POST["des_page"]);
      $fr_traduction = $bdd->secureRqt($_POST["fr_traduction"]);
      $en_traduction = $bdd->secureRqt($_POST["en_traduction"]);


      //met a jour la table realisation
      $sql = "INSERT INTO `designations` (
              `des_id` ,
              `des_name` ,
              `des_page` 
              )
              VALUES (
              NULL ,
              \"$des_name\",
              \"$des_page\");";
      $req1 = $bdd->requete($sql);

      // Met a jour le titre dans la table FR
      $sql = "INSERT INTO `fr` (
              `fr_id` ,
              `fr_designation` ,
              `fr_traduction` 
              )
              VALUES (
              NULL ,
              \"$des_name\",
              \"$fr_traduction\");";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "INSERT INTO `en` (
              `en_id` ,
              `en_designation` ,
              `en_traduction` 
              )
              VALUES (
              NULL ,
              \"$des_name\",
              \"$en_traduction\");";
      $req3 = $bdd->requete($sql);


      return array($req1,$req2,$req3);

 
}



public function supprimer(){

    if(is_numeric($_POST["des_id"]) && is_numeric($_POST["id_trad"])) {

      $bdd = new BDD();


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      $des_id = $bdd->secureRqt($_POST["des_id"]);
      $id_trad = $bdd->secureRqt($_POST["id_trad"]);



      //met a jour la table realisation
      $sql = "DELETE FROM `designations` WHERE `des_id` = \"$des_id\";";
      $req1 = $bdd->requete($sql);

      // Met a jour le titre dans la table FR
      $sql = "DELETE FROM `fr` WHERE `fr_id` = \"$id_trad\";";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "DELETE FROM `en` WHERE `en_id` = \"$id_trad\";";
      $req3 = $bdd->requete($sql);


      return array($req1,$req2,$req3);

    }
 
}
















}














?>