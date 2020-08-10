<?php 
    session_start();
    include '../config/config.php'; 
      try
      {
      $db = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      } 
      catch (Exeption $e)
      {
        die('erreur :' .$e ->getMessage());
    } 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    if (isset($_GET['id'])) {
        $idevent = $_GET['id'];
        $events = $db ->prepare('SELECT *,
                                        YEAR(date), 
                                        MONTHNAME(date), 
                                        DAY(date), 
                                        DAYNAME(date), 
                                        HOUR(time), 
                                        MINUTE(time),
                                        adresse,
                                        cp 
                                        FROM evenement
                                        WHERE id= ?');
  
        $events -> execute(array($idevent));
        $event = $events-> fetch();
  
        $category = $db-> prepare('SELECT title FROM categorie,evenement WHERE evenement.categorie_id = categorie.id && evenement.id=?');
        $category -> execute(array($_GET['id']));
        $categoryTitle = $category->fetch();
  
    }

    if ($_SESSION['id'] === $event['auteur']) {

        if (isset($_POST['edit'])) {
        
            $newtitle = htmlspecialchars($_POST['newTitle']);    
            $editTitle = $db ->prepare('UPDATE evenement SET titre=? WHERE id=?' );
            $editTitle -> execute(array($newtitle, $idevent));
            
            $newdate = htmlspecialchars($_POST['newDate']);
            $editdate = $db -> prepare('UPDATE evenement SET date=? WHERE id=?');
            $editdate ->execute(array($newdate, $idevent));
            
            $newtime=htmlspecialchars($_POST['newHour']);
            $edittime= $db ->prepare('UPDATE evenement SET time=? WHERE id=?');
            $edittime ->execute(array($newtime, $idevent));

            $newAdresse = htmlspecialchars($_POST['newAdresse']);    
            $editAdresse = $db ->prepare('UPDATE evenement SET adresse=? WHERE id=?' );
            $editAdresse -> execute(array($newAdresse, $idevent));

            $newCp = htmlspecialchars($_POST['newCp']);    
            $editCp = $db ->prepare('UPDATE evenement SET cp=? WHERE id=?' );
            $editCp -> execute(array($newCp, $idevent));

            $newVille = htmlspecialchars($_POST['newVille']);    
            $editVille = $db ->prepare('UPDATE evenement SET ville=? WHERE id=?' );
            $editVille -> execute(array($newVille, $idevent));
            
            $newdescription=htmlspecialchars($_POST['newDescription']);
            $editdescription =$db -> prepare('UPDATE evenement SET description=? WHERE id=?');
            $editdescription ->execute(array($newdescription, $idevent));
        
            $MailUserEvent = $db->prepare('SELECT pseudo , mail FROM utilisateur,user_event WHERE utilisateur.id = user_id && event_id = ?');
            $MailUserEvent->execute(array($idevent));
            while($sendMail = $MailUserEvent->fetch()){
        
            $maileditevent = new PHPMailer();
            $maileditevent->IsSMTP();
            $maileditevent->Mailer = "smtp"; 
            $maileditevent->SMTPAuth   = TRUE;
            $maileditevent->SMTPSecure = "tls";
            $maileditevent->Port       = 587;
            $maileditevent->Host       = "smtp.gmail.com";
            $maileditevent->Username   = "bryanrasamizafy98@gmail.com";
            $maileditevent->Password   = "apzoeiruty135";
            $maileditevent->IsHTML(true);
            $maileditevent->AddAddress($sendMail['mail'], $sendMail['pseudo']);
            $maileditevent->SetFrom("bryanrasamizafy98@gmail.com", "JEPSENS-BRITE");
            $maileditevent->AddReplyTo("bryanrasamizafy98@gmail.com", "Teem media");
            $maileditevent->AddCC("cc-recipient-email@domain", "cc-recipient-name");
            $maileditevent->Subject = "Jepsens-brite event";
            $contenteditevent = "<p>" . $sendMail['pseudo'] . ",</p>
                        <p>We announce that event " . $newtitle . " has been modified.</p>
                        <p>It will take place on the " . $newdate ." at " . $newtime . " at the adresse " . $newAdresse . "," . $newCp . " " . $newVille . ".</p>
                        <p>Cordially,</p>
                        <p>The JEPSENS-BRITE team.</p>
                            <img src='https://cdn.discordapp.com/attachments/734665861394071563/740911873318322266/jepsen_brite.png' alt='jepsens-brite'> 
                        ";
            $maileditevent->MsgHTML($contenteditevent); 
            $maileditevent->send();
        }

          header("location: show_event.php?id=".$event['id']);

        }

        if (isset($_POST['delete'])) { 
        
          $deleteEvent = $db ->prepare("DELETE FROM evenement WHERE id = ?" );
          $deleteEvent ->execute(array($idevent));
          $deletecomments = $db->prepare("DELETE FROM commentaires WHERE event_id = ?");
          $deletecomments->execute(array($idevent));
          $deletesubcat = $db->prepare("DELETE FROM subcat_event WHERE event_id = ?");
          $deletesubcat->execute(array($idevent));
          header("location: index.php");
          exit();
        
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit event</title>

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
<body >
    <div class="container">
    	<div class="d-flex justify-content-center h-100">
    		<div class="card" style="width:50%;">
    			<div class="card-header">
    				<h3>Edit <?= $event['titre']; ?></h3>
    			</div>
    			<div class="card-body">
    				<form action="" method="POST">
              <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-heading"></i></span>
    						</div>
                <input type="text" name='newTitle' value="<?php echo $event['titre'];?>" id="Form-email3" class="form-control">              
              </div>
              <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-clock"></i></span>
    						</div>
    						<input type="date"  name="newDate" value="<?php echo $event['date'];?>" id="Form-email3" class="form-control">
                <input type="time" name="newHour" value="<?php echo $event['time'];?>" id="Form-email3" class="form-control">
              </div>
    					<div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
    						</div>
    						<input type="text" name="newAdresse" value="<?php echo $event['adresse'];?>" id="Form-email3" class="form-control">
              </div>
              <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
    						</div>
    						<input type="text" name="newCp" value="<?php echo $event['cp'];?>" id="Form-email3" class="form-control">
                <input type="text" name="newVille" value="<?php echo $event['ville'];?>" id="Form-email3" class="form-control">
    					</div>
              <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-file-alt"></i></span>
    						</div>
    						<textarea type="text" name="newDescription" class="form-control" rows="10"  data-emojiable="true" data-emoji-input="unicode"><?php echo  $event['description'];?></textarea>
              </div>
    					<div class="form-group">
              <input type="submit" value="Edit" name="edit" class="btn float-right login_btn">
              <a href="show_event.php?id=<?= $event['id']; ?>" class="btn float-right login_btn">Back</a>
    					</div>
            </form>
    			</div>
        </div>
      </div>
    </div>
</body>
</html>