<?php

class membre {
  public $login;
  public $pwd;

  private $ligne_table;


  public function __construct($login="",$pwd="") {
    $this->login = $login;
    $this->pwd = $pwd;
    $this->ligne_table = array() ; 
  }









  /**
   * ************** HASHING *******************
   */ 
  

// REMPLACE PAR EXEMPLE UN HASHING EN MD5
// JE NE PENSE PAS QU'IL SOIT PLUS "SECURISE" MAIS CA M'INTERESSAIT DE CREER MON PROPRE ALGORYTHME






  /**
   * algo($mdp) algo
   * Ex : 'jean-paul' -> '5ORJpIwFYJlCIBbBoB'
   * @param STR $mdp
   * @return STR
   */
  private function algo($mdp){
    $arr1 = str_split($mdp);
    $arr2 = array();
    $count = count($arr1);
  
    $lettre = array();
    for ($i=65 ;$i<=90;$i++){
      $lettre[] = chr($i);
    }
    for ($i=48 ;$i<=57;$i++){
      $lettre[] = chr($i);
    }
    for ($i=97 ;$i<=122;$i++){
      $lettre[] = chr($i);
    }
  
    $code_int1 ='';
  
    for ($i=0;$i<$count;$i++){
      $arr1[$i] = ord ($arr1[$i]);
      $arr2[$i] = intval((pow ($i+10, 4)*($i+7))/$arr1[$i]);
      $arr2[$i] = str_pad($arr2[$i], 6, "001", STR_PAD_LEFT);
      $arr3[$i] = str_split($arr2[$i],3);
      $a = ((($arr3[$i][0])%61));
      $b = ((($arr3[$i][1])%61));
  
      $code_int1 .= $lettre[$a];
      $code_int1 .= $lettre[$b];
    }
    $code_int2 = strrev ($code_int1);
  
    return $code_int2;
  }
  
  /**
   * code($mdp) code
   * Ex : '5ORJpIwFYJlCIBbBoB' -> 'AKC5OEQORJzi4pIXNqwFszJYJb6alClPCIBFbobBWItoB'
   * @param STR $mdp
   * @return STR
   */
  private function code($mdp){
  
  
    $code_array = str_split($mdp,2);
    $count = count($code_array);
    $code_fini = '';
    for ($i=0;$i<$count;$i++){
      $random = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',3)),0,3);
      $code_fini .= $random.$code_array[$i];
    }
  
    return $code_fini;
  }
  
  /**
   * decode ($mdp) decode
   * Ex : 'AKC5OEQORJzi4pIXNqwFszJYJb6alClPCIBFbobBWItoB' -> '5ORJpIwFYJlCIBbBoB'
   * @param STR $mdp
   * @return STR
   */
  private function decode ($mdp){
    $code_array = str_split($mdp,5);
    $count = count($code_array);
    $code_fini = '';
    for ($i=0;$i<$count;$i++){
      $code_fini .= substr($code_array[$i], -2);
    }
    return $code_fini;
  }
  
  




  /**
   * ************** CONNEXION *******************
   */ 



  private function searchLogin() {
    $bdd = new BDD();

    $this->login = $bdd->secureRqt($this->login);

    $sql = "SELECT * FROM `portfolio_backoffice` WHERE `por_login` = \"".$this->login."\" LIMIT 0,1;";

    $bdd->requete($sql);

    return $this->ligne_table = $bdd->retourne_tableau();
 
  }




  private function verifMdp() {
    // Utilise algo pour coder le mdp entré par l'utilisateur
    // Utilise decode pour le mdp venant de la bdd
    return ( $this->algo($this->pwd) == $this->decode($this->ligne_table[0]["por_pwd"]) ) ? true : false;
  }





  public function connexion() {
    $log = $this->searchLogin();
    if($log != array() && is_array($log)){
      return $this->verifMdp();
    }
    else { return false;}
  }









  /**
   * ************** CREER LOGIN *******************
   */ 


  public function createLogin($login,$mdp) {
    $this->login = $login;
    $searchLogin = $this->searchLogin();
    if(empty($searchLogin)){
      $bdd = new BDD();

      $this->login = $bdd->secureRqt($login);

      $this->pwd = $this->code($this->algo($bdd->secureRqt($mdp)));

        //insertion d'un nouveau login/mdp
        $sql = "INSERT INTO `portfolio_backoffice` (
                `por_id` ,
                `por_login` ,
                `por_pwd`
                )
                VALUES (
                NULL ,
                \"".$this->login."\",
                \"".$this->pwd."\");";
      return $bdd->requete($sql);

    } else {
      return false;
    }


  }








  /**
   * ************** MODIFIER LOGIN *******************
   */ 


  public function modifMdp($newMdp) {
    if($this->connexion()){
      $bdd = new BDD();
      $this->pwd = $this->code($this->algo($bdd->secureRqt($newMdp))); 


      //modifier mdp
      $sql = "UPDATE `portfolio_backoffice` 
              SET 
              `por_pwd` = \"".$this->pwd."\" 
              WHERE `por_id` = \"".$this->ligne_table[0]["por_id"]."\" ;";
      return $bdd->requete($sql);

    }

    return false;



  }


}



?>