<?php
    session_start();
   
    include '../config/config.php';

    try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
    }
    $today = date("Y-m-d");

    if(isset($_GET['id'])){
        if($_GET['id']){
            $participated = $bdd->prepare("SELECT titre, id, date FROM evenement,user_event WHERE id=event_id && user_id= ? && date< ? ORDER BY date");
            $participated->execute(array($_GET['id'],$today));
            $nbParticipated = $participated->rowCount();
        }
        if($_GET['id']){
            $participate = $bdd->prepare("SELECT titre, id, date FROM evenement,user_event WHERE id=event_id && user_id= ? && date>= ? ORDER BY date");
            $participate->execute(array($_GET['id'],$today));
            $nbParticipant = $participate->rowCount();
        }
        if($_GET['id']){
            $created = $bdd->prepare("SELECT titre, id, date FROM evenement WHERE auteur= ? ORDER BY date");
            $created->execute(array($_GET['id']));
            $nbEvent = $created->rowCount();
        }
        if($_GET['id']){
            $ifadmin = $bdd->prepare("SELECT admin FROM utilisateur WHERE id= ?");
            $ifadmin->execute(array($_GET['id']));
            $nbadmin = $ifadmin->fetch();
        }
    }
    
    if(isset($_GET['id']) AND $_GET['id'] > 0){
        $getid = intval($_GET['id']);
        $req = $bdd->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $req->execute(array($getid));
        $user = $req->fetch();

        $formEdit = isset($_POST['formEdit']) ? $_POST['formEdit'] : NULL;
        if($formEdit){
            if(isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $useredit['pseudo']){
                $newpseudo = htmlspecialchars($_POST['newpseudo']);
                $insertpseudo = $bdd->prepare("UPDATE utilisateur SET pseudo = ? WHERE id = ?");
                $insertpseudo->execute(array($newpseudo, $_SESSION['id']));
                header('Location: profil.php?id='.$_SESSION["id"]);
            }
            $oldPassword = sha1($_POST['password']);
            if($_SESSION['password'] == $oldPassword){
                if(isset($_POST['newpassword']) AND !empty($_POST['newpassword']) AND isset($_POST['confirmnewpassword']) AND !empty($_POST['confirmnewpassword'])){
                    $newpassword = sha1($_POST['newpassword']);
                    $confirmnewpassword = sha1($_POST['confirmnewpassword']);
                    if($newpassword == $confirmnewpassword){
                        $insertpassword = $bdd->prepare("UPDATE utilisateur SET password = ? WHERE id = ?");
                        $insertpassword->execute(array($newpassword, $_SESSION['id']));
                        header('Location: profil.php?id='.$_SESSION["id"]);
                    }else{
                        $error = "Your PASSWORD doesn't match!";
                    }
                }
            }else{
                $error = 'Old password deasn\'t correct';
            }
            if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])){
                $tailleMax = 2097152;
                $extensoinValide = array('jpg', 'jpeg', 'png', 'gif');
                if($_FILES['avatar']['size'] <= $tailleMax){
                    $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
                    if(in_array($extensionUpload, $extensoinValide)){
                        $chemin = "../assets/user/".$_SESSION['id'].".".$extensionUpload;
                        $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
                        if($resultat){
                            $updateAvatar = $bdd->prepare('UPDATE utilisateur SET avatar = :avatar WHERE id = :id');
                            $updateAvatar->execute(array(
                                'avatar' => $_SESSION['id'].".".$extensionUpload,
                                'id' => $_SESSION['id']
                            ));
                            header('Location: profil.php?id='.$_SESSION["id"]);
                        }else{
                            $error = 'Error during import file';
                        }
                    }else{
                        $error = 'The image format must be in JPG, JPEG, PNG or GIF';
                    }
                }else{
                    $error = 'Your AVATAR is so big';
                } 
            }
        }
?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profil</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style_event.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="../index.php">Brite</a>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link js-scroll-trigger text-dark" href="logout.php">Log out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
<hr>
<div class="container bootstrap snippet">
    <div class="row">
        <div class="col-sm-10">
            <h1><?php echo $user['pseudo']; ?></h1> 
        </div>
    </div>
    <div class="row">
  		<div class="col-sm-3"><!--left col-->
              

            <div class="text-center">
              <img src="../assets/user/<?php echo $user['avatar']; ?>" class="avatar img-circle img-thumbnail" alt="avatar">
            </div></hr><br>

            <div class="panel panel-default">
                <div class="panel-heading">Status</div>
                <div class="panel-body">
                    <?php if($_SESSION['id'] == $_GET['id'] AND $_SESSION['admin'] == 1){?>
                        <a href="admin.php?id=<?= $_SESSION['id']; ?>" class="titre-h2 buttonsection">Administrateur</a>
                    <?php }elseif($nbadmin['admin'] == 1){ ?>
                        <p>Administrateur</p>
                    <?php }else{ ?>
                        <p>Utilisateur</p>
                    <?php } ?>
            </div>
            </div>
                
            <div class="panel panel-default">
              <div class="panel-heading">Email</div>
              <div class="panel-body"><?php echo $user['mail']; ?></div>
            </div>
                
                
            <ul class="list-group">
              <li class="list-group-item text-muted"><a href="dashboard.php?id=<?= $_GET['id']; ?>">Activity</a></li>
              <li class="list-group-item text-right"><span class="pull-left"><strong>Participate in past events </strong></span><?= $nbParticipated; ?></li>
              <li class="list-group-item text-right"><span class="pull-left"><strong>Participated events</strong></span><?= $nbParticipant; ?></li>
              <li class="list-group-item text-right"><span class="pull-left"><strong>Events created</strong></span><?= $nbEvent; ?></li>
            </ul> 
          
        </div><!--/col-3-->
        <?php if(isset($_SESSION['id']) AND $_SESSION['id'] == $_GET['id']){ ?>
    	    <div class="col-sm-9">
              <div class="tab-content">
                <div class="tab-pane active" id="home">
                    <hr>
                      <form class="form" action="" method="POST" enctype='multipart/form-data' id="registrationForm">
                          <div class="form-group">
                              <div class="col-xs-6">
                                  <label for="first_name"><h4>New pseudo</h4></label>
                                  <input type="text" class="form-control" name="newpseudo" id="first_name" placeholder="<?= $user['pseudo']; ?>">
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-xs-6">
                                  <label for="password"><h4>Old password</h4></label>
                                  <input type="password" class="form-control" name="password" id="password" placeholder="Old password">
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-xs-6">
                                  <label for="password"><h4>New password</h4></label>
                                  <input type="password" class="form-control" name="newpassword" id="password" placeholder="New password">
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-xs-6">
                                <label for="password2"><h4>Verify</h4></label>
                                  <input type="password" class="form-control" name="confirmnewpassword" id="confirmnewpassword" placeholder="Confirm new password">
                              </div>
                          </div>

                          <div class="form-group">
                            <div class="col-xs-6">
                                <label for="first_name"><h4>New avatar</h4></label>
                                <input type="file" class="text-center center-block file-upload" name="avatar" id="first_name">
                            </div>
                          </div>

                          <div class="form-group">
                               <div class="col-xs-12">
                                    <br>
                                    <?php if($_SESSION['admin'] != 1){ ?>
                                        <a href="delete_profile.php" class="btn btn-lg btn-success">Delete</a>
                                    <?php } ?>
                                  	<input class="btn btn-lg btn-success" type="submit" name='formEdit' value='Save'>
                                </div>
                          </div>
                  	</form>

                  <hr>

                 </div><!--/tab-pane-->
               </div>
            </div><!--/col-9-->
        <?php } ?><!--/tab-content-->
    </div><!--/row-->
</body>
</html>   
<?php
    }else{
        header("Location: ../index.php");
    }
?>                                            