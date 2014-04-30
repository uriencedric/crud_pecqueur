<?php
/*	$utilisateurs = array(
		array('login' => 'login', 'mdp' => 'mdp'),
		array('login' => 'toto', 'mdp' => 'titi'));
*/	
if(!isset($_SESSION))
		session_start();


include("class/BDD.php");
	$login = "";
	$mdp = "";

	if(isset($_POST['validelogin'])) {
		if(isset($_POST['login']))
			$login = $_POST['login'];
		if(isset($_POST['mdp']))
			$mdp = $_POST['mdp'];
	} 

	if(isset($_GET['Deco']))
		unset($_SESSION['login']);

	if(isset($_SESSION['login']))
		$checklogin = $_SESSION['login'];
	else
		$checklogin = false;
	
	
	if(isset($_POST['validelogin'])) {

		$admin= new BDD();

		if(isset($charset)) $admin->requete("SET NAMES '$charset';");
		$admin->requete("SELECT *  FROM admin");
		$tableadmin = $admin->retourne_tableau();
		foreach ($tableadmin as $element) {
			if ($login==$element['nom'] && $mdp==$element['mdp']) {
				$checklogin=true;
			}
		}

		if ($checklogin) {
			$_SESSION['login'] = true;
			header("location:back_office.php");
			/*echo "<p class='user'>Bonjour utilisateur $login<BR/>\n</p><img class='avatar' src='icons/Spy-icon.png'>";//penser à un switch pour prof*/
		}
		else{
			echo "<span>Echec de connexion</span>";
		}
	}
	
	if(!$checklogin) {
?>

<div class="loginbox">
	<p>Entrez login et mot de passe</p>

	<form method='post' action='<?php echo $_SERVER['PHP_SELF'];?>'>

		<p>identifiant  : </p><input type="text" name='login'/>

		<p>mot de passe : </p><input type="password" name='mdp'/>

		<input class="loginbutton" type='submit' name='validelogin' value='Soumettre'>
		

	</form>
	<p><a href="index.php"><img src="icons/backoffice.png " alt="retourfrontoffice"></a></p>  

</div>

</body>
</html>
<?php
	exit(0);
	}
?>
