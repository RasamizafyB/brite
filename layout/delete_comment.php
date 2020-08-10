<?php
    session_start();

    include '../config/config.php'; 

    try{
    $db = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }catch (Exeption $e){
        die('erreur :' .$e ->getMessage());
    } 
    if(isset($_GET['id'])){
        if($_GET['idauthor'] === $_SESSION['id']){
            $deleteCom = $db->prepare("DELETE FROM commentaires WHERE id = ?");
            $deleteCom->execute(array($_GET['id']));
            echo "<h1> COMMENT DELETED </h1>";
            header("location: show_event.php?id=".$_GET['eventid']);
        }else{
            echo "<h1> ACCES DENIED </h1>";
        }
    }else{
        echo "<h1> ACCES DENIED </h1>";
    }
?>