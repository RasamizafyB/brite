<?php
    session_start();
   
    include '../config/config.php';

    try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
    }

    if(isset($_SESSION['id'])){
        $getidHeader = intval($_SESSION['id']);
        $requserHeader = $bdd->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $requserHeader->execute(array($getidHeader));
        $userinfoHeader = $requserHeader->fetch();
    }
    
    $today = date('Y-m-d');
    if(isset($_GET['id'])){
        if($_GET['id']){
            $participated = $bdd->prepare("SELECT titre, id, date FROM evenement,user_event WHERE id=event_id && user_id= ? && date< ? ORDER BY date");
            $participated->execute(array($_GET['id'],$today));
        }
        if($_GET['id']){
            $participate = $bdd->prepare("SELECT titre, id, date FROM evenement,user_event WHERE id=event_id && user_id= ? && date>= ? ORDER BY date");
            $participate->execute(array($_GET['id'],$today));
        }
        if($_GET['id']){
            $created = $bdd->prepare("SELECT titre, id, date FROM evenement WHERE auteur= ? ORDER BY date");
            $created->execute(array($_GET['id']));
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboerd</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style_event.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous"> 

</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
            <?php if(isset($_SESSION['id'])){ ?>
                <a href="profil.php?id=<?php echo $_SESSION['id']; ?>"><img src="../assets/user/<?php echo $userinfoHeader['avatar']; ?>" alt="avatar" width='45' style="border-radius:22.5px; margin-left: 10px; display: flex; align-self: center;"></a>
            <?php } ?>
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="../index.php">Brite</a>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                    <?php if(isset($_SESSION['id'])){ ?>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="logout.php">Log out</a></li>
                    <?php }else{ ?>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="signin.php">Sign in</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="login.php">Log in</a></li>
                    <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Event</th>
          <th scope="col">Titre</th>
          <th scope="col">Date</th>
        </tr>
      </thead>
      <tbody>
            <td>Past events</td>
        <?php while($showParticipated = $participated->fetch()){ ?>
            <tr>
                <td></td>
                <td><a href="<?php echo 'show_event.php?id='.$showParticipated['id']?>" class="buttonsection"><?php echo $showParticipated['titre']?></a></td>
                <td><?= $showParticipated['date'];?></td>
            </tr>
        <?php } ?> 
            <td>Future events</td>
        <?php while($showParticipate = $participate->fetch()){ ?>
            <tr>
                <td></td>
                <td><a href="<?php echo 'show_event.php?id='.$showParticipate['id']?>" class="buttonsection"><?php echo $showParticipate['titre']?></a></td>
                <td><?= $showParticipate['date'];?></td>
            </tr>
        <?php } ?> 
            <td>Created event</td>
        <?php while($showCreated = $created->fetch()){ ?>
            <tr>
                <td></td>
                <td><a href="<?php echo 'show_event.php?id='.$showCreated['id']?>" class="buttonsection"><?php echo $showCreated['titre']?></a></td>
                <td><?= $showCreated['date'];?></td>
            </tr>
        <?php } ?> 
        <?php while($showCreated = $created->fetch()){ ?>
            <tr>
                <td></td>
                <td><a href="<?php echo 'show_event.php?id='.$showCreated['id']?>" class="buttonsection"><?php echo $showCreated['titre']?></a></td>
                <td><?= $showCreated['date'];?></td>
            </tr>
        <?php } ?> 
      </tbody>
    </table>
</body>
</html>