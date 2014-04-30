<?php



class tableDipExp {

  private $dip_exp = array();




// Protege des Failles XSS
  private function protectXss() {
    for ($i=0; $i < count($this->dip_exp); $i++) { 
      
      if (is_array($this->dip_exp[$i])) {
        foreach ($this->dip_exp[$i] as $key => $value) {
          $this->dip_exp[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
      } else {
        $this->dip_exp[$i] = htmlspecialchars($this->dip_exp[$i], ENT_QUOTES);
      }
    }
  }


/***************************************************************************   RECUPERATION DES DONNEES   **********************************************************************************/



  // Recupere et met dans la variable $dip_exp tous les elemnts de la table dip_exp
  public function recupDipExp(){
    $bdd = new BDD();


    $sql = "SELECT 
         dip_id,
         dip_descr,
         dip_periode,
        
        `fr2`.fr_id AS id_descr,
        `fr`.fr_id AS id_periode,
        `fr2`.fr_traduction AS `descr_fr`, 
        `fr`.fr_traduction AS `periode_fr`, 
        
        `en2`.en_traduction AS `descr_en`, 
        `en`.en_traduction AS `periode_en`
        
        FROM `fr` AS `fr2`
        
        INNER JOIN `dipl_exp` ON (dip_descr=`fr2`.fr_designation) 
        INNER JOIN `fr` ON (dip_periode=`fr`.fr_designation) 
        
        INNER JOIN `en` AS `en2` ON (dip_descr=`en2`.en_designation) 
        INNER JOIN `en` ON (dip_periode=`en`.en_designation) 
        
        ORDER BY periode_fr DESC;";

    $bdd->requete($sql);

    $this->dip_exp = $bdd->retourne_tableau();

    $this->protectXss();

    return $this->dip_exp;

    
  }








/***************************************************************************   AFFICHAGES DIPLOMES & EXPeRIENCES   **********************************************************************************/

  // Affiche tous les Diplomes et les experiences
  public function affiche(){
    $result = "<tr>\n<td colspan=\"5\"><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'><input type='submit' name='ajout_dip' value='Ajouter un nouvel Element'></form></td>\n</tr>\n";
    $result .= "<tr>\n<th></th>\n<th>Designation</th>\n<th>Période</th>\n</tr>\n";
    $i=1;
    foreach ($this->dip_exp as $element) {
      $result .= "<tr>\n";
      $result .= "<td>$i</td>\n";
      $result .= "<td>".$element["dip_descr"]."</td>\n";
      $result .= "<td>".$element["periode_fr"]."</td>\n";
      $result .= "<td><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
      $result .= "<input type='hidden' name='dip_id' value='".$element["dip_id"]."'>";
      $result .= "<input type='hidden' name='id_descr' value='".$element["id_descr"]."'>";
      $result .= "<input type='hidden' name='id_periode' value='".$element["id_periode"]."'>";
      $result .= "<input type='submit' name='detail_dip' value='Details'>";
      $result .= "<input type='submit' name='suppr_dip' value='Supprimer'></form></td>\n";
      $result .= "</tr>\n";
      $i++;
    }

    return $result;
  }



public function afficheDetail($id){

    foreach ($this->dip_exp as $element) {
      if($element["dip_id"] == $id) {


        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='dip_id' value='".$element["dip_id"]."'>";
        $result .= "<input type='hidden' name='id_descr' value='".$element["id_descr"]."'>";
        $result .= "<input type='hidden' name='id_periode' value='".$element["id_periode"]."'>";

        // Affichage Realisation
        $result .= "<fieldset>\n<legend> Diplôme ou Expérience : </legend>\n";
        $result .= "<label for='dip_descr'>Description : </label><br/>";
        $result .= "<p name='dip_descr'>".$element["dip_descr"]."</p>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<label for='periode_fr'>Période : </label><br/>";
        $result .= "<p name='periode_fr'>".$element["periode_fr"]."</p>\n";
        $result .= "<label for='descr_fr'>Description : </label><br/>";
        $result .= "<p name='descr_fr'>".$element["descr_fr"]."</p>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<label for='periode_en'>Période : </label><br/>";
        $result .= "<p name='periode_en'>".$element["periode_en"]."</p>\n";
        $result .= "<label for='descr_en'>Description : </label><br/>";
        $result .= "<p name='descr_en'>".$element["descr_en"]."</p>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='modifier_dip' value='Modifier'><input type='submit' name='suppr_dip' value='Supprimer'>\n";
        $result .= "</form>\n";


        return $result;

      }
    }

}


public function afficheModif($id){

    foreach ($this->dip_exp as $element) {
      if($element["dip_id"] == $id) {


        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='dip_id' value='".$element["dip_id"]."'>";
        $result .= "<input type='hidden' name='id_descr' value='".$element["id_descr"]."'>";
        $result .= "<input type='hidden' name='id_periode' value='".$element["id_periode"]."'>";

        // Affichage Diplome ou Experience
        $result .= "<fieldset>\n<legend> Diplôme ou Expérience : </legend>\n";
        $result .= "<label for='dip_descr'>Description : </label><br/>";
        $result .= "<input type='text' name='dip_descr' value='".$element["dip_descr"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<label for='periode_fr'>Période : </label><br/>";
        $result .= "<input type='text' name='periode_fr' value='".$element["periode_fr"]."'><br/>\n";
        $result .= "<label for='descr_fr'>Description : </label><br/>";
        $result .= "<input type='text' name='descr_fr' value='".$element["descr_fr"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<label for='periode_en'>Période : </label><br/>";
        $result .= "<input type='text' name='periode_en' value='".$element["periode_en"]."'><br/>\n";
        $result .= "<label for='descr_en'>Description : </label><br/>";
        $result .= "<input type='text' name='descr_en' value='".$element["descr_en"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrModif_dip' value='Valider Modifications'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";

        return $result;

      }
    }

}




public function modifier(){

    if(is_numeric($_POST["dip_id"]) && is_numeric($_POST["id_periode"]) && is_numeric($_POST["id_descr"])) {

      $bdd = new BDD();

      $dip_id = $bdd->secureRqt($_POST["dip_id"]);
      $id_periode = $bdd->secureRqt($_POST["id_periode"]);
      $id_descr = $bdd->secureRqt($_POST["id_descr"]);


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $dip_descr = $bdd->secureRqt($_POST["dip_descr"]);
      $dip_periode = $dip_descr."_date";
      $periode_fr = $bdd->secureRqt($_POST["periode_fr"]);
      $descr_fr = $bdd->secureRqt($_POST["descr_fr"]);
      $periode_en = $bdd->secureRqt($_POST["periode_en"]);
      $descr_en = $bdd->secureRqt($_POST["descr_en"]);

      //met a jour la table dipl_exp
      $sql = "UPDATE `dipl_exp` 
                SET 
                `dip_descr` = \"$dip_descr\",
                `dip_periode` = \"$dip_periode\" 
                WHERE `dip_id` = \"$dip_id\" ;";
      $req1 = $bdd->requete($sql);

      // Met a jour la periode dans la table FR
      $sql = "UPDATE `fr` 
                SET  
                `fr_designation` = \"$dip_periode\", 
                `fr_traduction` = \"$periode_fr\"
                WHERE `fr_id` = \"$id_periode\" ;";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "UPDATE `fr` 
                SET  
                `fr_designation` = \"$dip_descr\", 
                `fr_traduction` = \"$descr_fr\"
                WHERE `fr_id` = \"$id_descr\" ;";
      $req3 = $bdd->requete($sql);

      // Met a jour la periode dans la table EN
      $sql = "UPDATE `en` 
                SET  
                `en_designation` = \"$dip_periode\", 
                `en_traduction` = \"$periode_en\"
                WHERE `en_id` = \"$id_periode\" ;";
      $req4 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "UPDATE `en` 
                SET  
                `en_designation` = \"$dip_descr\", 
                `en_traduction` = \"$descr_en\"
                WHERE `en_id` = \"$id_descr\" ;";
      $req5 = $bdd->requete($sql);


      return array($req1,$req2,$req3,$req4,$req5);

    }
 
}


public function afficheAjout(){



        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";

        // Affichage Diplome ou Experience
        $result .= "<fieldset>\n<legend> Diplôme ou Expérience : </legend>\n";
        $result .= "<label for='dip_descr'>Description : </label><br/>";
        $result .= "<input type='text' name='dip_descr' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<label for='periode_fr'>Période : </label><br/>";
        $result .= "<input type='text' name='periode_fr' value=''><br/>\n";
        $result .= "<label for='descr_fr'>Description : </label><br/>";
        $result .= "<input type='text' name='descr_fr' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<label for='periode_en'>Période : </label><br/>";
        $result .= "<input type='text' name='periode_en' value=''><br/>\n";
        $result .= "<label for='descr_en'>Description : </label><br/>";
        $result .= "<input type='text' name='descr_en' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrAjout_dip' value='Ajouter ce nouvel Element'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;


}



public function ajouter(){


      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $dip_descr = $bdd->secureRqt($_POST["dip_descr"]);
      $dip_periode = $dip_descr."_date";
      $periode_fr = $bdd->secureRqt($_POST["periode_fr"]);
      $descr_fr = $bdd->secureRqt($_POST["descr_fr"]);
      $periode_en = $bdd->secureRqt($_POST["periode_en"]);
      $descr_en = $bdd->secureRqt($_POST["descr_en"]);

      //met a jour la table dipl_exp
      $sql = "INSERT INTO `dipl_exp` (
              `dip_id` ,
              `dip_descr` ,
              `dip_periode`
              )
              VALUES (
              NULL ,
              \"$dip_descr\",
              \"$dip_periode\");";
      $req1 = $bdd->requete($sql);

      // Met a jour la periode dans la table FR
      $sql = "INSERT INTO `fr` (
              `fr_id` ,
              `fr_designation` ,
              `fr_traduction` 
              )
              VALUES (
              NULL ,
              \"$dip_periode\",
              \"$periode_fr\");";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "INSERT INTO `fr` (
              `fr_id` ,
              `fr_designation` ,
              `fr_traduction` 
              )
              VALUES (
              NULL ,
              \"$dip_descr\",
              \"$descr_fr\");";
      $req3 = $bdd->requete($sql);

      // Met a jour la periode dans la table EN
      $sql = "INSERT INTO `en` (
              `en_id` ,
              `en_designation` ,
              `en_traduction` 
              )
              VALUES (
              NULL ,
              \"$dip_periode\",
              \"$periode_en\");";
      $req4 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "INSERT INTO `en` (
              `en_id` ,
              `en_designation` ,
              `en_traduction` 
              )
              VALUES (
              NULL ,
              \"$dip_descr\",
              \"$descr_en\");";
      $req5 = $bdd->requete($sql);


      return array($req1,$req2,$req3,$req4,$req5);

 
}



public function supprimer(){

    if(is_numeric($_POST["dip_id"]) && is_numeric($_POST["id_descr"]) && is_numeric($_POST["id_periode"])) {

      $bdd = new BDD();

      $dip_id = $bdd->secureRqt($_POST["dip_id"]);
      $id_periode = $bdd->secureRqt($_POST["id_periode"]);
      $id_descr = $bdd->secureRqt($_POST["id_descr"]);


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      //met a jour la table diplomes et experiences
      $sql = "DELETE FROM `dipl_exp` WHERE `dip_id` = \"$dip_id\";";
      $req1 = $bdd->requete($sql);

      // Met a jour la periode dans la table FR
      $sql = "DELETE FROM `fr` WHERE `fr_id` = \"$id_periode\";";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "DELETE FROM `fr` WHERE `fr_id` = \"$id_descr\";";
      $req3 = $bdd->requete($sql);

      // Met a jour la periode dans la table EN
      $sql = "DELETE FROM `en` WHERE `en_id` = \"$id_periode\";";
      $req4 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "DELETE FROM `en` WHERE `en_id` = \"$id_descr\";";
      $req5 = $bdd->requete($sql);


      return array($req1,$req2,$req3,$req4,$req5);

    }
 
}




















}














?>