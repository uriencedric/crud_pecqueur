<?php



class tableRealisations {

  private $realisations = array();





// Protege des Failles XSS
  private function protectXss() {
    for ($i=0; $i < count($this->realisations); $i++) { 
      
      if (is_array($this->realisations[$i])) {
        foreach ($this->realisations[$i] as $key => $value) {
          $this->realisations[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
      } else {
        $this->realisations[$i] = htmlspecialchars($this->realisations[$i], ENT_QUOTES);
      }
    }
  }


/***************************************************************************   RECUPERATION DES DONNEES   **********************************************************************************/



  // Recupere et met dans la variable $realisations tous les elements de la table realisation
  public function recupRealisations(){
    $rea_bdd = new BDD();


    $sql = "SELECT 
        rea_id, 
        rea_lienImage, 
        rea_lienRealisation, 
        rea_titre,
        rea_type,
        
        `fr`.fr_id AS id_descr,
        `fr2`.fr_id AS id_titre,
        `fr2`.fr_traduction AS `titre_fr`, 
        `fr`.fr_traduction AS `description_fr`, 
        rea_travail_fr AS `travail_fr`,
        
        `en2`.en_traduction AS `titre_en`, 
        `en`.en_traduction AS `description_en`, 
        rea_travail_en AS `travail_en`
        
        FROM `fr` AS `fr2`
        
        INNER JOIN `realisations` ON (rea_titre=`fr2`.fr_designation) 
        INNER JOIN `fr` ON (rea_description=`fr`.fr_designation) 
        
        INNER JOIN `en` AS `en2` ON (rea_titre=`en2`.en_designation) 
        INNER JOIN `en` ON (rea_description=`en`.en_designation) 
        
        ORDER BY rea_type DESC, rea_id DESC;";

    $rea_bdd->requete($sql);

    $this->realisations = $rea_bdd->retourne_tableau();

    $this->protectXss();

    return $this->realisations;

    
  }




/***************************************************************************   AFFICHAGES REALISATIONS   **********************************************************************************/



  // Affiche toutes les realisations
  public function affiche(){
    $result = "<tr>\n<td colspan=\"5\"><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'><input type='submit' name='ajout_rea' value='Ajouter une nouvelle réalisation'></form></td>\n</tr>\n";
    $result .= "<tr>\n<th></th>\n<th>Type</th>\n<th>Titre</th>\n<th>Image</th>\n<th>Lien</th>\n</tr>\n";
    $i=1;
    foreach ($this->realisations as $element) {
      $result .= "<tr>\n";
      $result .= "<td>$i</td>\n";
      $result .= "<td>".$element["rea_type"]."</td>\n";
      $result .= "<td>".str_replace("rea_titre_", "", $element["rea_titre"])."</td>\n";
      $result .= "<td>".$element["rea_lienImage"]."</td>\n";
      $result .= "<td>".$element["rea_lienRealisation"]."</td>\n";
      $result .= "<td><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
      $result .= "<input type='hidden' name='id_realisation' value='".$element["rea_id"]."'>";
      $result .= "<input type='hidden' name='id_titre' value='".$element["id_titre"]."'>";
      $result .= "<input type='hidden' name='id_descr' value='".$element["id_descr"]."'>";
      $result .= "<input type='submit' name='detail_rea' value='Details'>";
      $result .= "<input type='submit' name='suppr_rea' value='Supprimer'></form></td>\n";
      $result .= "</tr>\n";
      $i++;
    }


    return $result;
  }



public function afficheDetail($id){

    foreach ($this->realisations as $element) {
      if($element["rea_id"] == $id) {


        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='id_realisation' value='".$id."'>";
        $result .= "<input type='hidden' name='id_titre' value='".$element["id_titre"]."'>";
        $result .= "<input type='hidden' name='id_descr' value='".$element["id_descr"]."'>";

        // Affichage Realisation
        $result .= "<fieldset>\n<legend> Réalisation : </legend>\n";
        $result .= "<label for='type'>Type : </label><br/>";
        $result .= "<p name='type'>".$element["rea_type"]."</p>\n";
        $result .= "<label for='titre'>Titre : </label><br/>";
        $result .= "<p name='titre'>".str_replace("rea_titre_", "", $element["rea_titre"])."</p>\n";
        $result .= "<label for='image'>Image : </label><br/>";
        $result .= "<p name='image'>".$element["rea_lienImage"]."</p>\n";
        $result .= "<label for='lien'>Lien : </label><br/>";
        $result .= "<p name='lien'>".$element["rea_lienRealisation"]."</p>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<label for='titre_fr'>Titre : </label><br/>";
        $result .= "<p name='titre_fr'>".$element["titre_fr"]."</p>\n";
        $result .= "<label for='description_fr'>Description : </label><br/>";
        $result .= "<p name='description_fr'>".$element["description_fr"]."</p>\n";
        $result .= "<label for='travail_fr'>Travail : </label><br/>";
        $result .= "<pre name='travail_fr'>".$element["travail_fr"]."</pre>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<label for='titre_en'>Titre : </label><br/>";
        $result .= "<p name='titre_en'>".$element["titre_en"]."</p>\n";
        $result .= "<label for='description_en'>Description : </label><br/>";
        $result .= "<p name='description_en'>".$element["description_en"]."</p>\n";
        $result .= "<label for='travail_en'>Travail : </label><br/>";
        $result .= "<pre name='travail_en'>".$element["travail_en"]."</pre>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='modifier_rea' value='Modifier'><input type='submit' name='suppr_rea' value='Supprimer'>\n";
        $result .= "</form>\n";


        return $result;

      }
    }

}


public function afficheModif($id){

    foreach ($this->realisations as $element) {
      if($element["rea_id"] == $id) {


        $web = ($element["rea_type"]=="web")? " selected" : "";
        $graph = ($element["rea_type"]=="graph")? " selected" : "";


        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='id_realisation' value='".$id."'>";
        $result .= "<input type='hidden' name='id_titre' value='".$element["id_titre"]."'>";
        $result .= "<input type='hidden' name='id_descr' value='".$element["id_descr"]."'>";

        // Affichage Realisation
        $result .= "<fieldset>\n<legend> Réalisation : </legend>\n";
        $result .= "<label for='type'>Type : </label><br/>";
        $result .= "<select name='type'>";
        $result .= "<option value='web' $web >Developpement Web</option>";
        $result .= "<option value='graph' $graph >Graphisme</option>";
        $result .= "</select><br />\n";
        $result .= "<label for='titre'>Titre : </label><br/>";
        $result .= "<input type='text' name='titre' value='".str_replace("rea_titre_", "", $element["rea_titre"])."'><br/>\n";
        $result .= "<label for='image'>Image : </label><br/>";
        $result .= "<input type='text' name='image' value='".$element["rea_lienImage"]."'><br/>\n";
        $result .= "<label for='lien'>Lien : </label><br/>";
        $result .= "<input type='text' name='lien' value='".$element["rea_lienRealisation"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<label for='titre_fr'>Titre : </label><br/>";
        $result .= "<input type='text' name='titre_fr' value='".$element["titre_fr"]."'><br/>\n";
        $result .= "<label for='description_fr'>Description : </label><br/>";
        $result .= "<input type='text' name='description_fr' value='".$element["description_fr"]."'><br/>\n";
        $result .= "<label for='travail_fr'>Travail : </label><br/>";
        $result .= "<textarea name='travail_fr'>".$element["travail_fr"]."</textarea>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<label for='titre_en'>Titre : </label><br/>";
        $result .= "<input type='text' name='titre_en' value='".$element["titre_en"]."'><br/>\n";
        $result .= "<label for='description_en'>Description : </label><br/>";
        $result .= "<input type='text' name='description_en' value='".$element["description_en"]."'><br/>\n";
        $result .= "<label for='travail_en'>Travail : </label><br/>";
        $result .= "<textarea name='travail_en'>".$element["travail_en"]."</textarea>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrModif_rea' value='Valider Modifications'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;

      }
    }

}


public function modifier(){

    if(is_numeric($_POST["id_realisation"]) && is_numeric($_POST["id_titre"]) && is_numeric($_POST["id_descr"])) {

      $bdd = new BDD();

      $id_rea = $bdd->secureRqt($_POST["id_realisation"]);
      $id_titre = $bdd->secureRqt($_POST["id_titre"]);
      $id_descr = $bdd->secureRqt($_POST["id_descr"]);


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $type = $bdd->secureRqt($_POST["type"]);
      $titre = $bdd->secureRqt($_POST["titre"]);
      $image = $bdd->secureRqt($_POST["image"]);
      $lien = $bdd->secureRqt($_POST["lien"]);
      $titre_fr = $bdd->secureRqt($_POST["titre_fr"]);
      $description_fr = $bdd->secureRqt($_POST["description_fr"]);
      $travail_fr = $bdd->secureRqt($_POST["travail_fr"]);
      $titre_en = $bdd->secureRqt($_POST["titre_en"]);
      $description_en = $bdd->secureRqt($_POST["description_en"]);
      $travail_en = $bdd->secureRqt($_POST["travail_en"]);

      //met a jour la table realisation
      $sql = "UPDATE `realisations` 
                SET 
                `rea_type` = \"$type\",
                `rea_lienImage` = \"$image\", 
                `rea_lienRealisation` = \"$lien\", 
                `rea_titre` = \"rea_titre_$titre\", 
                `rea_description` = \"rea_descr_$titre\", 
                `rea_travail_fr` = \"$travail_fr\", 
                `rea_travail_en` = \"$travail_en\" 
                WHERE `rea_id` = \"$id_rea\" ;";
      $req1 = $bdd->requete($sql);

      // Met a jour le titre dans la table FR
      $sql = "UPDATE `fr` 
                SET  
                `fr_designation` = \"rea_titre_$titre\", 
                `fr_traduction` = \"$titre_fr\"
                WHERE `fr_id` = \"$id_titre\" ;";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "UPDATE `fr` 
                SET  
                `fr_designation` = \"rea_descr_$titre\", 
                `fr_traduction` = \"$description_fr\"
                WHERE `fr_id` = \"$id_descr\" ;";
      $req3 = $bdd->requete($sql);

      // Met a jour le titre dans la table EN
      $sql = "UPDATE `en` 
                SET  
                `en_designation` = \"rea_titre_$titre\", 
                `en_traduction` = \"$titre_en\"
                WHERE `en_id` = \"$id_titre\" ;";
      $req4 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "UPDATE `en` 
                SET  
                `en_designation` = \"rea_descr_$titre\", 
                `en_traduction` = \"$description_en\"
                WHERE `en_id` = \"$id_descr\" ;";
      $req5 = $bdd->requete($sql);


      return array($req1,$req2,$req3,$req4,$req5);

    }
 
}

    

  

public function afficheAjout(){



        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";

        // Affichage Realisation
        $result .= "<fieldset>\n<legend> Réalisation : </legend>\n";
        $result .= "<label for='type'>Type : </label><br/>";
        $result .= "<select name='type'>";
        $result .= "<option value='web' selected>Developpement Web</option>";
        $result .= "<option value='graph' >Graphisme</option>";
        $result .= "</select><br />\n";
        $result .= "<label for='titre'>Titre : </label><br/>";
        $result .= "<input type='text' name='titre' value=''><br/>\n";
        $result .= "<label for='image'>Image : </label><br/>";
        $result .= "<input type='text' name='image' value=''><br/>\n";
        $result .= "<label for='lien'>Lien : </label><br/>";
        $result .= "<input type='text' name='lien' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<label for='titre_fr'>Titre : </label><br/>";
        $result .= "<input type='text' name='titre_fr' value=''><br/>\n";
        $result .= "<label for='description_fr'>Description : </label><br/>";
        $result .= "<input type='text' name='description_fr' value=''><br/>\n";
        $result .= "<label for='travail_fr'>Travail : </label><br/>";
        $result .= "<textarea name='travail_fr'></textarea>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<label for='titre_en'>Titre : </label><br/>";
        $result .= "<input type='text' name='titre_en' value=''><br/>\n";
        $result .= "<label for='description_en'>Description : </label><br/>";
        $result .= "<input type='text' name='description_en' value=''><br/>\n";
        $result .= "<label for='travail_en'>Travail : </label><br/>";
        $result .= "<textarea name='travail_en'></textarea>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrAjout_rea' value='Créer Nouvelle Réalisation'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;


}



public function ajouter(){


      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $type = $bdd->secureRqt($_POST["type"]);
      $titre = $bdd->secureRqt($_POST["titre"]);
      $image = $bdd->secureRqt($_POST["image"]);
      $lien = $bdd->secureRqt($_POST["lien"]);
      $titre_fr = $bdd->secureRqt($_POST["titre_fr"]);
      $description_fr = $bdd->secureRqt($_POST["description_fr"]);
      $travail_fr = $bdd->secureRqt($_POST["travail_fr"]);
      $titre_en = $bdd->secureRqt($_POST["titre_en"]);
      $description_en = $bdd->secureRqt($_POST["description_en"]);
      $travail_en = $bdd->secureRqt($_POST["travail_en"]);

      //met a jour la table realisation
      $sql = "INSERT INTO `realisations` (
              `rea_id` ,
              `rea_lienImage` ,
              `rea_lienRealisation` ,
              `rea_titre` ,
              `rea_description` ,
              `rea_travail_fr` ,
              `rea_travail_en` ,
              `rea_type`
              )
              VALUES (
              NULL ,
              \"$image\",
              \"$lien\", 
              \"rea_titre_$titre\", 
              \"rea_descr_$titre\", 
              \"$travail_fr\", 
              \"$travail_en\",
              \"$type\");";
      $req1 = $bdd->requete($sql);

      // Met a jour le titre dans la table FR
      $sql = "INSERT INTO `fr` (
              `fr_id` ,
              `fr_designation` ,
              `fr_traduction` 
              )
              VALUES (
              NULL ,
              \"rea_titre_$titre\",
              \"$titre_fr\");";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "INSERT INTO `fr` (
              `fr_id` ,
              `fr_designation` ,
              `fr_traduction` 
              )
              VALUES (
              NULL ,
              \"rea_descr_$titre\",
              \"$description_fr\");";
      $req3 = $bdd->requete($sql);

      // Met a jour le titre dans la table EN
      $sql = "INSERT INTO `en` (
              `en_id` ,
              `en_designation` ,
              `en_traduction` 
              )
              VALUES (
              NULL ,
              \"rea_titre_$titre\",
              \"$titre_en\");";
      $req4 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "INSERT INTO `en` (
              `en_id` ,
              `en_designation` ,
              `en_traduction` 
              )
              VALUES (
              NULL ,
              \"rea_descr_$titre\",
              \"$description_en\");";
      $req5 = $bdd->requete($sql);


      return array($req1,$req2,$req3,$req4,$req5);

 
}



public function supprimer(){

    if(is_numeric($_POST["id_realisation"]) && is_numeric($_POST["id_titre"]) && is_numeric($_POST["id_descr"])) {

      $bdd = new BDD();

      $id_rea = $bdd->secureRqt($_POST["id_realisation"]);
      $id_titre = $bdd->secureRqt($_POST["id_titre"]);
      $id_descr = $bdd->secureRqt($_POST["id_descr"]);


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      //met a jour la table realisation
      $sql = "DELETE FROM `realisations` WHERE `rea_id` = \"$id_rea\";";
      $req1 = $bdd->requete($sql);

      // Met a jour le titre dans la table FR
      $sql = "DELETE FROM `fr` WHERE `fr_id` = \"$id_titre\";";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "DELETE FROM `fr` WHERE `fr_id` = \"$id_descr\";";
      $req3 = $bdd->requete($sql);

      // Met a jour le titre dans la table EN
      $sql = "DELETE FROM `en` WHERE `en_id` = \"$id_titre\";";
      $req4 = $bdd->requete($sql);

      // Met a jour la description dans la table EN
      $sql = "DELETE FROM `en` WHERE `en_id` = \"$id_descr\";";
      $req5 = $bdd->requete($sql);


      return array($req1,$req2,$req3,$req4,$req5);

    }
 
}
















}














?>