<?php
/**
 * @param array $msgReturn
 */
function msgReturn_push($msgReturn){
	if($msgReturn[0]){
		echo '<div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
        ';
	}else{
		echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
        ';
	}
	echo $msgReturn[1].'</div>';
}

/**
 * Exécute une requête de type SELECT et retourne son résultat
 *
 * @param PDO $db
 * @param string $query Requete a éxécuter
 * @param array $arrayData Tableau des données de la requete
 *
 * @return array Tableau de retour de la requete de sélect
 */
function executeSelect(PDO &$db, string $query, array $arrayData){
	$q = $db->prepare($query);
	$q->execute($arrayData);

	$arrayResult = array();
	while ($data = $q->fetch(PDO::FETCH_ASSOC)){
		$arrayResult[] = $data;
	}
	
	return $arrayResult;
}

/**
 * Execute une requête de type INSERT
 *
 * @param PDO $db
 * @param string $query Requete a éxécuter
 * @param array $arrayData Tableau des données de la requete
 */
function executeInsert(PDO &$db, string $query, array $arrayData){
	$q = $db->prepare($query);
	$q->execute($arrayData);
}

/**
 * Autoload des classes (Require de la classe requise au moment de l'appel a cette derniere)
 *
 * @param string $classname Nom de la classe a charger
 */
function loadClass($classname){
	$classname = str_replace('\\', '/', $classname);
	require($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/classes/'.$classname.'.class.php');
}

function is_connected(){
	return (empty($_SESSION['connected']) ? false : $_SESSION['connected']);
}

/**
 * @param string     $link
 * @param array|null $args
 */
function myInclude(string $link, array $args = null){
	if(is_file($link)){
		$fileToInclude = $link;
		if($args != null){
			$fileToInclude .= '?';
			foreach($args as $arg => $value){
				$$arg = $value;
				$fileToInclude .= $arg.'='.$value.'&';
			}
		}
		include $link;
	}
	else{
		msgReturn_push([0, 'Erreur d\'accès, le fichier est introuvable.']);
	}
}

/**
 * Vérifie le bon formattage d'un email
 *
 * @param $email
 *
 * @return bool
 */
function verifyMail($email){
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		return false;
	}
	else{
		return true;
	}
}

/**
 * Retourne un numéro de téléphone épuré (juste les chiffres)
 * Ex : 01.02.03-04 05 en entrée ==> 0102030405 en sortie
 *
 * @param string $phone
 *
 * @return string
 */
function purgePhone(string $phone){
	return (string)preg_replace_callback('([-. ])', function(){return '';}, $phone);
}

/**
 * Vérifie le bon formattage d'un numéro de téléphone/fax (10 chiffres)
 *
 * @param string $phone
 *
 * @return bool
 */
function verifyPhone(string $phone){
	if(preg_match('/^[0-9]{10}$/', $phone)){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Vérifie le bon formattage d'un numéro de siret (14 chiffres)
 *
 * @param string $siret
 *
 * @return bool
 */
function verifySiret(string $siret){
	if(preg_match('/^[0-9]{14}$/', $siret)){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Vérifie le bon formattage d'un code APE (4chiffres puis 1 lettre)
 *
 * @param string $ape
 *
 * @return bool
 */
function verifyApe(string $ape){
	if(preg_match('/^[0-9]{4}[a-zA-Z]{1}$/', $ape)){
		return true;
	}
	else{
		return false;
	}
}

/**
 * @param string $string
 *
 * @return bool
 */
function verifyPureString(string $string){
	if(preg_match('/^[-a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ ]*$/', $string)){
		return true;
	}
	else{
		return false;
	}
}

/**
 * @param string $phone
 *
 * @return string
 */
function getPhoneNumber(string $phone){
	$number = $phone;
	$toReturn = '';
	for($i = 0; $i <= strlen($number); $i++){
		if(isset($number[$i])){
			if($i % 2 == 0 && $i != 0){
				$toReturn .= '.';
			}
			$toReturn .= $number[$i];
		}
		else{
			return $toReturn;
		}
	}

	return $toReturn;
}

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['V']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}