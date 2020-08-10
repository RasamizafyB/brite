<?php
    
    include '../config/config.php';  
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';
    
    try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
    }
    if(isset($_POST['formInscription'])){
        if(!empty($_POST['pseudo']) AND !empty($_POST['email']) AND !empty($_POST['password']) AND !empty($_POST['confirmpassword'])){
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $email = htmlspecialchars($_POST['email']);
            $password = sha1($_POST['password']);
            $confirm_password = sha1($_POST['confirmpassword']);
            $defaultAvatar = "default.jpeg";

            $pseudolength = strlen($pseudo);
            if($pseudolength <= 20){
                $reqpseudo = $bdd->prepare("SELECT * FROM utilisateur WHERE pseudo = ?");
                $reqpseudo->execute(array($pseudo));
                $pseudoexist = $reqpseudo->rowCount();
                if($pseudoexist == 0){
                    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $reqmail = $bdd->prepare("SELECT * FROM utilisateur WHERE mail = ?");
                        $reqmail->execute(array($email));
                        $mailexist = $reqmail->rowCount();
                        if($mailexist == 0){
                            if($password == $confirm_password){
                                $insertMember = $bdd->prepare("INSERT INTO utilisateur(pseudo, password, mail, avatar) VALUE (:pseudo, :password, :mail, :avatar)");
                                $insertMember->execute(array(
                                    'pseudo' => $pseudo,
                                    'password' => $password,
                                    'mail' => $email,
                                    'avatar' => $defaultAvatar
                                ));

                                $mail = new PHPMailer();
                                $mail->IsSMTP();
                                $mail->Mailer = "smtp";
                                // $mail->SMTPDebug  = 1;  
                                $mail->SMTPAuth   = TRUE;
                                $mail->SMTPSecure = "tls";
                                $mail->Port       = 587;
                                $mail->Host       = "smtp.gmail.com";
                                $mail->Username   = "bryanrasamizafy98@gmail.com";
                                $mail->Password   = "Beloha98";

                                $mail->IsHTML(true);
                                $mail->AddAddress($email, $pseudo);
                                $mail->SetFrom("bryanrasamizafy98@gmail.com", "JEPSENS-BRITE");
                                $mail->AddReplyTo("bryanrasamizafy98@gmail.com", "Teem media");
                                $mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
                                $mail->Subject = "Jepsens-brite event";
                                $content = "<p>Congratulation " . $pseudo . ",</p>
                                            <p>Your registration has been successfully created.</p> 
                                            <p>Welcome to the great team of JEPSENS-BRITE.</p>
                                            <p>Cordially,</p>
                                            <p>The JEPSENS-BRITE team.</p>
                                                <img src='https://cdn.discordapp.com/attachments/734665861394071563/740911873318322266/jepsen_brite.png' alt='jepsens-brite'> 
                                            ";

                                $mail->MsgHTML($content); 
                                $mail->send();
                              
                                $done = "Your account is done!";

                            }else{
                                $error = "Your PASSWORD doesn't match!";
                            }
                        }else{
                            $error = "This EMAIL alrady exists!";
                        }
                    }else{
                        $error = "Your EMAIL isn't valide!";
                    }
                }else{
                    $error = "This USERNAME alrady exists!";
                }
            }else{
                $error = "Your PSEUDO is too long!";
            }
        }else{
            $error = "Complet form please!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Sign in</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Jepsens-Brite</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    <!--Bootsrap 4 CDN-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
    <!--Fontawesome CDN-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="../css/login.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="../index.php">Brite</a>
        </div>
    </nav>
    <div class="container">
    	<div class="d-flex justify-content-center h-100">
    		<div class="card">
    			<div class="card-header">
    				<h3>Sign In</h3>
    			</div>
    			<div class="card-body">
    				<form action="" method="POST">
                        <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-user"></i></span>
    						</div>
    						<input type="text" class="form-control" placeholder="pseudo" name="pseudo">
                        </div>
                        <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-envelope-open"></i></span>
    						</div>
    						<input type="email" class="form-control" placeholder="email" name="email">
                        </div>
    					<div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-key"></i></span>
    						</div>
    						<input type="password" class="form-control" placeholder="password" name="password">
                        </div>
                        <div class="input-group form-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i class="fas fa-key"></i></span>
    						</div>
    						<input type="password" class="form-control" placeholder="confirm password" name="confirmpassword">
    					</div>
    					<div class="form-group">
                            <input type="submit" value="Sign" name="formInscription" class="btn float-right login_btn">
                            <a href="login.php" class="btn float-right login_btn">Login</a>
    					</div>
                    </form>
    			</div>
            </div>
        </div>
        <?php
            if(isset($error)){
        ?>
            <div class="error">
                <p><i class="fas fa-times"></i> <?php echo $error ?> <i class="fas fa-times"></i></p>
            </div>
        <?php
            }
            if(isset($done)){
        ?>
            <div class="done">
                <p><i class="fas fa-check"></i> <?php echo $done?> <i class="fas fa-check"></i></p>
            </div>
        <?php
            }
        ?>
    </div>

    <script src="https://kit.fontawesome.com/1815b8a69b.js" crossorigin="anonymous"></script>
</body>
</html>
