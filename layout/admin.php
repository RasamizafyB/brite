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
        $_SESSION['admin'] = $userinfoHeader['admin'];
    }

    if(isset($_SESSION) AND $_SESSION['admin'] == 1){
        $requserAdminEvent = $bdd->query("SELECT evenement.*, utilisateur.pseudo, utilisateur.id FROM evenement 
                                                        LEFT JOIN utilisateur ON auteur = utilisateur.id ORDER BY evenement.date");
        $requserAdminUser = $bdd->query("SELECT * FROM utilisateur ORDER BY pseudo");

        if(isset($_GET['supprimerUser']) AND !empty($_GET['supprimerUser'])){
            $supprimerUser = (int) $_GET['supprimerUser'];
            $deleteUser = $bdd->prepare('DELETE FROM utilisateur WHERE id = ? LIMIT 1');
            $deleteUser->execute(array($supprimerUser));
            header('Location: admin.php');
        }
        if(isset($_GET['adminUser']) AND !empty($_GET['adminUser'])){
            $admin = (int) $_GET['adminUser'];
            $adminUser = $bdd->prepare('UPDATE `utilisateur` SET `admin`= 1 WHERE id = ? ');
            $adminUser->execute(array($admin));
            header('Location: admin.php');
        }
        if(isset($_GET['desadminUser']) AND !empty($_GET['desadminUser'])){
          $desadmin = (int) $_GET['desadminUser'];
          $desadminUser = $bdd->prepare('UPDATE `utilisateur` SET `admin`= 0 WHERE id = ? ');
          $desadminUser->execute(array($desadmin));
          header('Location: admin.php');
      }
        if(isset($_GET['supprimerEvent']) AND !empty($_GET['supprimerEvent'])){
            $supprimerEvent = (int) $_GET['supprimerEvent'];
            $deleteEvent = $bdd->prepare('DELETE FROM evenement WHERE id = ? LIMIT 1');var_dump($user['admin']);
            $deleteEvent->execute(array($supprimerEvent));
            header('Location: admin.php');
        }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page administrateur</title>

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
    <main>
        <table class="table">
          <thead class="thead-dark">
            <tr>
              <th scope="col">id</th>
              <th scope="col">Pseudo</th>
              <th scope="col">Email</th>
              <th scope="col">Admin</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php while($AdminUser = $requserAdminUser->fetch()){ ?>
                <tr>
                  <th scope="row"><?= $AdminUser['id']; ?></th>
                  <td><a href="profil.php?id=<?= $AdminUser['id']; ?>"><?= $AdminUser['pseudo']; ?></a></td>
                  <td><?= $AdminUser['mail']; ?></td>
                  <?php if($AdminUser['admin'] != 1 ){ ?>
                    <td><a href="admin.php?id=<?= $_SESSION['id'] ?>&adminUser=<?= $AdminUser['id'] ?>"><i class="fas fa-user-cog buttonsection"></i></a></td>
                    <td><a href="admin.php?id=<?= $_SESSION['id'] ?>&supprimerUser=<?= $AdminUser['id'] ?>"><i class="fas fa-trash buttonsection" style="color:red;"></i></a></td>
                  <?php }else{ ?>
                    <td><a href="admin.php?id=<?= $_SESSION['id'] ?>&desadminUser=<?= $AdminUser['id'] ?>"><i class="fas fa-user-check" style="color:green;"></i></a></td>
                  <?php } ?>
                </tr>
            <?php } ?>
          </tbody>
        </table>

        <table class="table">
          <thead class="thead-light">
            <tr>
              <th scope="col">id</th>
              <th scope="col">Titre</th>
              <th scope="col">Auteur</th>
              <th scope="col">Date</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php while($AdminEvent = $requserAdminEvent->fetch()){ ?>
              <tr>
                <th scope="row"><?= $AdminEvent['0']; ?></th>
                <td><a href="show_event.php?id=<?= $AdminEvent['0']; ?>"><?= $AdminEvent['titre']; ?></a></td>
                <td><?= $AdminEvent['date']; ?></td>
                <td><a href="profil.php?id=<?= $AdminEvent['id']; ?>"><?= $AdminEvent['pseudo']; ?></a></td>
                <td><a href="admin.php?id=<?= $_SESSION['id'] ?>&supprimerEvent=<?= $AdminEvent['0'] ?>"><i class="fas fa-trash buttonsection" style="color:red;"></i></a></td>
            <?php } ?>
          </tbody>
        </table> 
    </main>
</body>
</html>
<?php }else{
  header('Location: profil.php?id='. $_SESSION['id']);
} ?>