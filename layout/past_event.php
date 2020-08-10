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

    $date = date('Y-m-d');
    $reqpastevent = $bdd->prepare("SELECT evenement.*, categorie.title, utilisateur.pseudo, utilisateur.id FROM evenement 
                                    LEFT JOIN utilisateur ON auteur = utilisateur.id
                                    LEFT JOIN categorie ON categorie_id = categorie.id
                                    WHERE date < ? ORDER BY date");
    $reqpastevent->execute(array($date));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past event</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style_event.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
            <?php if(isset($_SESSION['id'])){ ?>
                <a href="profil.php?id=<?php echo $_SESSION['id']; ?>"><img src="../assets/user/<?php echo $userinfoHeader['avatar']; ?>" alt="avatar" width='45' style="border-radius:22.5px; margin-left: 10px; display: flex; align-self: center;"></a>
            <?php } ?>
            <div class="container">
                <a class="navbar-brand js-scroll-trigger text-dark" href="../index.php">Brite</a>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                    <?php if(isset($_SESSION['id'])){ ?>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger text-dark" href="logout.php">Log out</a></li>
                    <?php }else{ ?>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger text-dark" href="signin.php">Sign in</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger text-dark" href="login.php">Log in</a></li>
                    <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="album py-5 bg-light">
        <div class="container">
            <div class="row">
            <?php while($pastevent = $reqpastevent->fetch()){ ?>
              <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                  <div class="bd-placeholder-img card-img-top img-card" style='
                    <?php if($pastevent["image"]){ ?>
                        background-image:url("../assets/event/<?= $pastevent['image']; ?>");
                    <?php }else{ ?>
                        background-image:url("../assets/img/lala.jpeg");
                    <?php } ?>
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: auto;'>
                  </div>
                  <div class="card-body">
                    <h5 class="card-text"><?= $pastevent['titre']; ?></h5>
                    <p class="card-text"><a href="profil.php?id=<?=$pastevent['id'];?>"><?= $pastevent['pseudo']; ?></a></p>
                    <p class="card-text"><?= $pastevent['adresse']; ?>, <?= $pastevent['cp'];?> <?= $pastevent['ville'];?></p>
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="btn-group">
                        <a href='show_event.php?id=<?= $pastevent['0']; ?>' class="btn btn-sm btn-outline-secondary">View</a>
                      </div>
                      <small class="text-muted"><?= $pastevent['date']; ?>, <?= $pastevent['time'];?></small>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?> 
            </div> 
        </div>
    </div> 
</body>
</html>