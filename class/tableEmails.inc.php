<?php



class tableEmails {

  private $emails = array();






// Protege des Failles XSS
  private function protectXss() {
    for ($i=0; $i < count($this->emails); $i++) { 
      
      if (is_array($this->emails[$i])) {
        foreach ($this->emails[$i] as $key => $value) {
          $this->emails[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
      } else {
        $this->emails[$i] = htmlspecialchars($this->emails[$i], ENT_QUOTES);
      }
    }
  }


/***************************************************************************   RECUPERATION DES DONNEES   **********************************************************************************/


  // Recupere et met dans la variable $emails tous les messages de la table email
  public function recupEmails(){
    $bdd = new BDD();

    if(isset($charset))
      $bdd->requete("SET NAMES '$charset';");

      $sql = "SELECT * 
              FROM email 
              ORDER BY ema_date DESC;";
    
    $bdd->requete($sql);
    
    $this->emails = $bdd->retourne_tableau();

    $this->protectXss();

    return $this->emails;


  }



/***************************************************************************   AFFICHAGES EMAILS   **********************************************************************************/


  // Affiche tous les emails
  public function afficheEmails(){
    $result = "<tr>\n<td colspan=\"5\"></td>\n</tr>\n";
    $result .= "<tr>\n<th></th>\n<th>Date</th>\n<th>Prenom - Nom</th>\n<th>Objet</th>\n</tr>\n";
    $i=1;
    foreach ($this->emails as $element) {
      $result .= "<tr>\n";
      $result .= "<td>$i</td>\n";
      $result .= "<td>".date("d/m",strtotime($element["ema_date"]))."</td>\n";
      $result .= "<td>".$element["ema_prenom"]." ".$element["ema_nom"]."</td>\n";
      $result .= "<td>".$element["ema_objet"]."</td>\n";
      $result .= "<td><form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'><input type='hidden' name='id_email' value='".$element["ema_id"]."'><input type='submit' name='voir_email' value='Voir'><input type='submit' name='suppr_email' value='Supprimer'></form></td>\n";
      $result .= "</tr>\n";
      $i++;
    }
    return $result;
  }





  // Affiche le message d'un email choisi ($id)
  public function afficheMessage($id) {

    foreach ($this->emails as $element) {
      if($element["ema_id"] == $id) {
        $result = "<div>\n";
        $result .= "<p>".$element["ema_prenom"]." ".$element["ema_nom"]."</p>\n";
        $result .= "<p>".date("d/m - H:i",strtotime($element["ema_date"]))."</p>\n";
        $result .= "<p>".$element["ema_email"]."</p>\n";

        $result .= "<p>Objet : </p>\n";
        $result .= "<p>".$element["ema_objet"]."</p>\n";

        $result .= "<p>Message : </p>\n";
        $result .= "<pre>".$element["ema_message"]."</pre>\n";

        $result .= "</div>\n";

        $result .= "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= "<input type='hidden' name='id_email' value='".$element["ema_id"]."'><input type='submit' name='send_email' value='Répondre'><input type='submit' name='suppr_email' value='Supprimer'>\n";
        $result .= "</form>\n";

        return $result;

      }
    }

  }


  // Affiche un formulaire pour repondre à un message
  public function afficheNewMessage($id) {
        foreach ($this->emails as $element){
          if($element["ema_id"] == $id) {
                 $email = "<input type='hidden' name='email' value='".$element["ema_email"]."'>";
          }
        }
        $result = "<form method='POST' action='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>\n";
        $result .= $email;
        $result .= "<fieldset>\n<legend> Repondre : </legend>\n";
        $result .= "<label for='objet'>Objet : </label><br/>";
        $result .= "<input type='text' name='objet' value=''><br/>\n";
        $result .= "<label for='message'>Message : </label><br/>";
        $result .= "<textarea name='message'></textarea>";
        $result .= "</fieldset>\n";
        $result .= "<input type='submit' name='repondre_email' value='Envoyer'>\n";
        $result .= "<input type='submit' name='annuler' value='Annuler'>\n";
        $result .= "</form>\n";

        return $result;

  }

  // Envoi le message de reponse
  public function envoyerMessage($email,$objet,$message) {
          global $config;
          $entetes = "From: ".$config["email"]["admin"]." \n";
          $ret = mail($email, $objet, $message, $entetes);

          return $ret;
        
        return false;
 
  }

  // Supprime un email
  public function supprimerMessage($id) {
    $suppr_ligne = new BDD();

    $sql = "DELETE FROM email WHERE ema_id = \"$id\"";

    return $suppr_ligne->requete($sql);
  }













}














?>