<?php
    session_start();
    include ('../config/config.php');
	try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
	}
	if(isset($_POST['formLog'])){
		$pseudolog = htmlspecialchars($_POST['usernameLog']);
		$passwordlog = sha1($_POST['passwordLog']);
		if(!empty($pseudolog) AND !empty($passwordlog)){
			$requser = $bdd->prepare('SELECT * FROM utilisateur WHERE pseudo = ? AND password = ?');
			$requser->execute(array($pseudolog, $passwordlog));
			$userexist = $requser->rowCount();
			if($userexist == 1){
				$userinfo = $requser->fetch();
				$_SESSION['id'] = $userinfo['id'];
				$_SESSION['pseudo'] = $userinfo['pseudo'];
				$_SESSION['email'] = $userinfo['email'];
				$_SESSION['password'] = $userinfo['password'];
				$_SESSION['admin'] = $userinfo['admin'];
				header("Location: ../index.php");
			}else{
				$error = "Incorrect USERNAME or PASSWORD";
			}
		}else{
			$error = "Complet the form please!";
		}
	}
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!DOCTYPE html>
<html>
<head>
	<title>Login Page</title>
   <!--Made with love by Mutiullah Samim -->
   <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />
   
	<!--Bootsrap 4 CDN-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
    <!--Fontawesome CDN-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

	<!--Custom styles-->
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/style.css">	
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="../index.php">Brite</a>
        </div>
    </nav>
	<div class="container">
		<div class="d-flex justify-content-center h-100">
			<div class="card log">
				<div class="card-header">
					<h3>Log In</h3>
				</div>
				<div class="card-body">
					<form action="" method="POST">
						<div class="input-group form-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fas fa-user"></i></span>
							</div>
							<input type="text" class="form-control" placeholder="username" name="usernameLog">
						</div>
						<div class="input-group form-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fas fa-key"></i></span>
							</div>
							<input type="password" class="form-control" placeholder="password" name="passwordLog">
						</div>
						<div class="form-group">
							<a href="signin.php" class="btn float-right login_btn">Sign</a>
							<input type="submit" value="Login" name="formLog" class="btn float-right login_btn">
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
	    ?>
	</div>
</body>
</html>