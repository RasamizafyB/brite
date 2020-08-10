<?php
    session_start();
    
    include '../config/config.php';  

    try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
    }

    if(isset($_POST['formEvent'])){
        $title = htmlspecialchars($_POST['title']);
        $date = htmlspecialchars($_POST['date']);
        $hour = htmlspecialchars($_POST['time']);
        $adresse = htmlspecialchars($_POST['adresse']);
        $code_postal = htmlspecialchars($_POST['code_postal']);
        $ville = htmlspecialchars($_POST['ville']);
        $description = htmlspecialchars($_POST['description']);
        $video = substr(htmlspecialchars($_POST['video']), 32) ;
        $category = $_POST['category'];
        $userId = $_SESSION['id'];

        if(isset($_FILES['image']) AND !empty($_FILES['image']['name'])){
            $tailleMax = 2097152;
            $extensoinValide = array('jpg', 'jpeg', 'png', 'gif');
            if($_FILES['image']['size'] <= $tailleMax){
                $extensionUpload = strtolower(substr(strrchr($_FILES['image']['name'], '.'), 1));
                if(in_array($extensionUpload, $extensoinValide)){
                    $chemin = "../assets/event/".$title.".".$extensionUpload;
                    $resultat = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                    $eventImage = $title.".".$extensionUpload;
                }
            }
        }
        
        if(!empty($_POST['title']) AND !empty($_POST['date']) AND !empty($_POST['time']) AND !empty($_POST['category']) AND  
        isset($_SESSION['id']) AND !isset($error) AND !empty($_POST['adresse']) AND !empty($_POST['code_postal']) AND !empty($_POST['ville'])){
            if(isset($_FILES['image']) AND !empty($_FILES['image']['name']) AND isset($_POST['video']) AND !empty($_POST['video'])){
                $error = 'You can insert an image or video url but not both';
            }elseif(isset($_FILES['image']) AND !empty($_FILES['image']['name'])){
                $addEvent = $bdd->prepare("INSERT INTO evenement (titre, auteur, date, time, image, description, categorie_id, adresse, cp, ville) VALUES 
                ( :titre, :auteur, :date, :time, :image, :description, :categorie_id, :adresse, :cp, :ville)"); 
                $addEvent->execute(array(
                    'titre' => $title,
                    'auteur' => $userId,
                    'date' => $date,
                    'time' => $hour,
                    'image' => $eventImage,
                    'description' => $description,
                    'categorie_id' => $category,
                    'adresse' => $adresse,
                    'cp' => $code_postal,
                    'ville' => $ville
                ));
                header('Location ../index.php');

            }elseif(isset($_POST['video']) AND !empty($_POST['video'])){
                $addEvent = $bdd->prepare("INSERT INTO evenement (titre, auteur, date, time, description, categorie_id, adresse, cp, ville, video) VALUES 
                ( :titre, :auteur, :date, :time, :description, :categorie_id,  :adresse, :cp, :ville, :video)"); 
                $addEvent->execute(array(
                    'titre' => $title,
                    'auteur' => $userId,
                    'date' => $date,
                    'time' => $hour,
                    'description' => $description,
                    'categorie_id' => $category,
                    'adresse' => $adresse,
                    'cp' => $code_postal,
                    'ville' => $ville,
                    'video' => $video
                ));
                header('Location ../index.php');
            }
        }

        if($category == 1){
            if(isset($_POST['1'])){

            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create event</title>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="../css/login.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="css/responsive.css"> -->
</head>
<body>
    <div class="container">
    	<div class="d-flex justify-content-center h-100">
    		<div class="card" style="width:50%;">
    			<div class="card-header">
    				<h3>Create event</h3>
    			</div>
    			<div class="card-body">
    				<form action="" method="POST" enctype='multipart/form-data'>
                        <div class="input-group form-group">
    		            	<div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-heading"></i></span>
    		            	</div>
                            <input type="text" name='title' placeholder='Titre' id="Form-email3" class="form-control">              
                        </div>
                        <div class="input-group form-group">
    		            	<div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-clock"></i></span>
    		            	</div>
    		            	<input type="date"  name="date"  id="Form-email3" class="form-control">
                            <input type="time" name="time" id="Form-email3" class="form-control">
                        </div>
    					<div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
    						</div>
    						<input type="text" name="adresse" placeholder='Rue de Mulhouse 36' id="Form-email3" class="form-control">
                        </div>
                        <div class="input-group form-group">
    		            	<div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
    		            	</div>
    		            	<input type="text" name="code_postal" placeholder='4000' maxlength='4' id="Form-email3" class="form-control">
                            <input type="text" name="ville" placeholder='Liège'  id="Form-email3" class="form-control">
    		            </div>
                        <div class="input-group form-group">
    		            	<div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-file-alt"></i></span>
    		            	</div>
    		            	<textarea type="text" name="description" class="form-control" rows="5"  data-emojiable="true" data-emoji-input="unicode"></textarea>
                        </div>
                        <div class="input-group form-group">
    		            	<div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-images"></i></span>
    		            	</div>
    		            	<input type="file" name="image" id="Form-email3" class="form-control">
    		            </div>
                        <div class="input-group form-group text-center" style="color:white;">
    		            	OU
    		            </div>
                        <div class="input-group form-group">
    		            	<div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-video"></i></span>
    		            	</div>
    		            	<input type="text" name="video" id="Form-email3" placeholder='https://www.youtube.com/watch?v=nX6QOOyCWtc' class="form-control">
    		            </div>
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
    		            		<span class="input-group-text"><i class="fas fa-list-ul"></i></span>
    		            	</div>
                            <select class="custom-select mr-sm-2" id="inlineFormCustomSelect" name='category'>
                                <option selected>Choose...</option>
                                <?php
                                    $reqcategory = $bdd->query("SELECT * FROM categorie ORDER BY title");
                                    while($categoryMenu = $reqcategory->fetch()){
                                ?>
                                    <option value="<?= $categoryMenu['id']; ?>" name='category'><?= $categoryMenu['title']; ?></option>
                                <?php } ?>
                            </select>
    		            </div>
    		            <div class="form-group">
                            <input type="submit" value="Edit" name="formEvent" class="btn float-right login_btn">
                            <a href="../index.php" class="btn float-right login_btn">Back</a>
    		        	</div>
                    </form>
    			</div>
            </div>
        </div>
    </div>
</body>
</html>