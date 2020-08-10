<?php 
    session_start();


    include 'config/config.php';

	try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
    }
    
    $delete = $bdd->prepare('DELETE FROM utilisateur WHERE ID=:num LIMIT 1');
    $delete->execute(array('num' => $_SESSION['id']));
    var_dump($_SESSION['id']);

    header('Location: ../index.php');
?>