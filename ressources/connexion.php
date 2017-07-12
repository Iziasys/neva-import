<?php
/**
 * Retourne l'objet PDO permettant de se connecter a la BDD
 *
 * @return PDO
 */
function databaseConnection(){
	try{
		$db = new PDO('mysql:host='.getHost().';dbname='.getPrefDb(), ''.getUsr().'', ''.getPwd().'', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
	}
	catch (Exception $e){
		die('Erreur : ' . $e->getMessage());
	}
	
	return $db;
}

?>