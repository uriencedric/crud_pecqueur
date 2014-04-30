<?php



class tableMenu {

  private $menu = array();



// Protege des Failles XSS
  private function protectXss() {
    for ($i=0; $i < count($this->menu); $i++) { 
      
      if (is_array($this->menu[$i])) {
        foreach ($this->menu[$i] as $key => $value) {
          $this->menu[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
      } else {
        $this->menu[$i] = htmlspecialchars($this->menu[$i], ENT_QUOTES);
      }
    }
  }


/***************************************************************************   RECUPERATION DES DONNEES   **********************************************************************************/



  // Recupere et met dans la variable $menu tous les elements de la table menu
  public function recupMenu(){
    $bdd = new BDD();

    if(isset($charset))
      $bdd->requete("SET NAMES '$charset';");

      $sql = "SELECT * 
              FROM menu 
              INNER JOIN `fr` ON ( men_lien = fr_designation)
              INNER JOIN `en` ON ( men_lien = en_designation)
              ORDER BY men_index;";
    
    $bdd->requete($sql);
    
    $this->menu = $bdd->retourne_tableau();

    $this->protectXss();

    return $this->menu;


  }





/***************************************************************************   AFFICHAGES MENU   **********************************************************************************/

  // Affiche tous les elements du menu
  public function affiche(){
    $result = "<tr>\n<td colspan=\"5\"><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'><input type='submit' name='ajout_men' value='Ajouter un nouvel Element'></form></td>\n</tr>\n";
    $result .= "<tr>\n<th></th>\n<th>Index</th>\n<th>Nom de la Page</th>\n<th>Traduction FR</th>\n<th>Traduction EN</th>\n</tr>\n";
    $i=1;
    foreach ($this->menu as $element) {
      $result .= "<tr>\n";
      $result .= "<td>$i</td>\n";
      $result .= "<td>".$element["men_index"]."</td>\n";
      $result .= "<td>".$element["men_lien"]."</td>\n";
      $result .= "<td>".$element["fr_traduction"]."</td>\n";
      $result .= "<td>".$element["en_traduction"]."</td>\n";
      $result .= "<td><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
      $result .= "<input type='hidden' name='id_men' value='".$element["men_id"]."'>";
      $result .= "<input type='hidden' name='id_fr' value='".$element["fr_id"]."'>";
      $result .= "<input type='hidden' name='id_en' value='".$element["en_id"]."'>";
      
      $result .= "<input type='submit' name='modif_menu' value='Modifier'>";
      $result .= "<input type='submit' name='suppr_menu' value='Supprimer'></form></td>\n";
      $result .= "</tr>\n";
      $i++;
    }

    return $result;
  }



// Affiche la modification d'un element du menu
public function afficheModif($id){

    foreach ($this->menu as $element) {
      if($element["men_id"] == $id) {




        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='id_men' value='".$id."'>";
        $result .= "<input type='hidden' name='id_trad' value='".$element["fr_id"]."'>";

        $result .= "<fieldset>\n<legend> Element du menu : </legend>\n";
        $result .= "<label for='index'>Index : </label><br/>";
        $result .= "<input type='text' name='index' value='".$element["men_index"]."'><br/>\n";
        $result .= "<label for='nom'>Nom de la page : </label><br/>";
        $result .= "<input type='text' name='nom' value='".$element["men_lien"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<input type='text' name='trad_fr' value='".$element["fr_traduction"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<input type='text' name='trad_en' value='".$element["en_traduction"]."'><br/>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrModif_menu' value='Valider Modifications'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;

      }
    }

}




// Modifie la table MENU et les Tables FR et EN
public function modifier(){


      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $id_men = $bdd->secureRqt($_POST["id_men"]);
      $id_trad = $bdd->secureRqt($_POST["id_trad"]);


      $index = $bdd->secureRqt($_POST["index"]);
      $nom = $bdd->secureRqt($_POST["nom"]);
      $trad_fr = $bdd->secureRqt($_POST["trad_fr"]);
      $trad_en = $bdd->secureRqt($_POST["trad_en"]);

      $sql = "UPDATE `menu` 
                SET 
                `men_lien` = \"$nom\",
                `men_index` = \"$index\"
                WHERE `men_id` = \"$id_men\" ;";
      $req1 = $bdd->requete($sql);

      // Met a jour le menu dans la table FR
      $sql = "UPDATE `fr` 
                SET  
                `fr_designation` = \"$nom\", 
                `fr_traduction` = \"$trad_fr\"
                WHERE `fr_id` = \"$id_trad\" ;";
      $req2 = $bdd->requete($sql);

      // Met a jour le menu dans la table EN
      $sql = "UPDATE `en` 
                SET  
                `en_designation` = \"$nom\", 
                `en_traduction` = \"$trad_en\"
                WHERE `en_id` = \"$id_trad\" ;";
      $req3 = $bdd->requete($sql);


      return array($req1,$req2,$req3);

 
}



// Affiche la creation d'un element du menu
public function afficheAjout(){


        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='id_men' value=''>";
        $result .= "<input type='hidden' name='id_trad' value=''>";

        $result .= "<fieldset>\n<legend> Element du menu : </legend>\n";
        $result .= "<label for='index'>Index : </label><br/>";
        $result .= "<input type='text' name='index' value=''><br/>\n";
        $result .= "<label for='nom'>Nom de la page : </label><br/>";
        $result .= "<input type='text' name='nom' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue FR
        $result .= "<fieldset>\n<legend> Traduction FRANCAIS : </legend>\n";
        $result .= "<input type='text' name='trad_fr' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        // Affichage langue EN
        $result .= "<fieldset>\n<legend> Traduction ANGLAIS : </legend>\n";
        $result .= "<input type='text' name='trad_en' value=''><br/>\n";
        $result .= "</fieldset>\n";
        
        $result .= "<input type='submit' name='enrAjout_menu' value='Ajouter ce nouvel Element'><input type='reset' name='reset' value='Annuler'>\n";
        $result .= "</form>\n";


        return $result;


}



// Modifie la table MENU et les Tables FR et EN
public function ajouter(){


      $bdd = new BDD();



      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      // Securise les donnees
      $index = $bdd->secureRqt($_POST["index"]);
      $nom = $bdd->secureRqt($_POST["nom"]);
      $trad_fr = $bdd->secureRqt($_POST["trad_fr"]);
      $trad_en = $bdd->secureRqt($_POST["trad_en"]);

      $sql = "INSERT INTO `menu` (
              `men_id` ,
              `men_lien` ,
              `men_index` 
              )
              VALUES (
              NULL ,
              \"$nom\",
              \"$index\");";
      $req1 = $bdd->requete($sql);

      // Met a jour le menu dans la table FR
      $sql = "INSERT INTO `fr` (
              `fr_id` ,
              `fr_designation` ,
              `fr_traduction` 
              )
              VALUES (
              NULL ,
              \"$nom\",
              \"$trad_fr\");";
      $req2 = $bdd->requete($sql);

      // Met a jour le menu dans la table EN
      $sql = "INSERT INTO `en` (
              `en_id` ,
              `en_designation` ,
              `en_traduction` 
              )
              VALUES (
              NULL ,
              \"$nom\",
              \"$trad_en\");";
      $req3 = $bdd->requete($sql);


      return array($req1,$req2,$req3);

 
}


public function supprimer(){

    if(is_numeric($_POST["id_men"]) && is_numeric($_POST["id_fr"])) {

      $bdd = new BDD();

      $id_men = $bdd->secureRqt($_POST["id_men"]);
      $id_trad = $bdd->secureRqt($_POST["id_fr"]);


      if(isset($charset))
        $bdd->requete("SET NAMES '$charset';");


      //met a jour la table realisation
      $sql = "DELETE FROM `menu` WHERE `men_id` = \"$id_men\";";
      $req1 = $bdd->requete($sql);

      // Met a jour le titre dans la table FR
      $sql = "DELETE FROM `fr` WHERE `fr_id` = \"$id_trad\";";
      $req2 = $bdd->requete($sql);

      // Met a jour la description dans la table FR
      $sql = "DELETE FROM `en` WHERE `en_id` = \"$id_trad\";";
      $req3 = $bdd->requete($sql);


      return array($req1,$req2,$req3);

    }
 
}



















}














?>