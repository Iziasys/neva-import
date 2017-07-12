<?php
	include('../../conf.php');
	if(!isset($_SESSION)){
		include($_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/fonctions.php');
		spl_autoload_register('loadClass');
		include($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/connexion.php');
		session_start();
	}