<?php

class BDD {

	// Chaîne de caractères contenant des informations utiles
	protected $serveur;//protected pour autorisé lacces au variable aux classes filles
	protected $user;
	protected $base;
	protected $requete;
	
	// Indicateurs spéciaux
	protected $connexion; // Permet de savoir si la connexion au serveur fonctionne
	protected $base_en_cours; // Permet de savoir si la connexion à la base se passe bien
	protected $requete_en_cours; // Permet d'avoir un indicateur sur la requête en cours d'exécution
	protected $ligne_en_cours; // Le tableau de la ligne de requête en cours de traitement
	
	public function __construct($base = "persofolio", $serveur = "localhost", $user = "root", $mdp = "")
	{
		// Sauvegarde des informations utiles à afficher
		$this->serveur = $serveur;
		$this->user = $user;
		$this->base = $base;
		$this->requete_en_cours = false; // Pour nous protéger d'un appel intempestif à "retourne_ligne"
		
		// Tentative de connexion au serveur
		$this->connexion = mysqli_connect($this->serveur, $this->user, $mdp);
		if($this->connexion === false) // Gestion de l'erreur de connexion
		{
			echo "<p>La connexion sur $this->serveur n'a pu s'établir pour l'utilisateur $this->user avec le mot de passe $this->mdp</p>";
			exit(1);
		}
		
		// Tentative de sélection de la base qui nous intéresse
		$this->base_en_cours = mysqli_select_db($this->connexion, $this->base);
		if($this->base_en_cours === false) // Gestion du select de la base
		{
			echo "<p>La sélection de la base $this->base est impossible</p>";
			exit(2);
		}

		$this->requete("SET NAMES 'UTF8';");

	}

	public function __destruct()
	{
		mysqli_close($this->connexion);
	}

	public function __toString()
	{
		$toret = "<p>Vous êtes connecté sur $this->serveur en tant que $this->user</p>\n";
		$toret .= "<p>Vous travaillez avec la base $this->base</p>\n";
		$toret .= "<p>La requête en cours est : $this->requete</p>\n";
		
		return $toret;
	}
	
	public function requete($sql)
	{
		// Mémorisation de la requête à exécuter
		$this->requete = $sql;
		
		// Exécution de la requête
		$this->requete_en_cours = mysqli_query($this->connexion, $this->requete);
		
		// Traîtement d'erreur
		if($this->requete_en_cours === false) { // Erreur lors de la requête
				echo "<p>La requête a échouée</p>".$this;
				return false;
		}
		return true;
	}
	
	public function retourne_ligne()
	{
		
		if($this->requete_en_cours === false) { // Pas de requête en cours
			echo "<p>Il n'y a pas de requête en cours !</p>".$this;
			$this->ligne_en_cours = false;
		} else { // ligne_en_cours contient le tableau retourné par mysqli_fetch_assoc
			$this->ligne_en_cours = mysqli_fetch_assoc($this->requete_en_cours);
		}
		return $this->ligne_en_cours;
	}

	public function retourne_ligne_en_cours()
	{
		// Si ligne_en_cours est un tableau, c'est qu'il y a une requête en cours, on peut donc renvoyer de nouveau cette ligne : utile pour la première ligne
		if(is_array($this->ligne_en_cours)) {
			return $this->ligne_en_cours;
		} else { // Gestion d'erreur
			return false;
		}
	}

	public function retourne_tableau()
	{
		$tableau_ligne=array();
		while($this->retourne_ligne()) {
			$tableau_ligne[] = $this->retourne_ligne_en_cours();
		}
		return $tableau_ligne;
	}
	public function secureSQL($string){
		return mysqli_real_escape_string ($this->connexion ,$string);
	}
	public function secureXSS($string){
		return  htmlspecialchars ($string,ENT_QUOTES);
	}
}

?>
