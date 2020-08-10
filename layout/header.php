<?php

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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
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
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <?php if(isset($_SESSION['id'])){ ?>
            <a href="layout/profil.php?id=<?php echo $_SESSION['id']; ?>"><img src="assets/user/<?php echo $userinfoHeader['avatar']; ?>" alt="avatar" width='45' style="border-radius:22.5px; margin-left: 10px; display: flex; align-self: center;"></a>
        <?php } ?>
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">Brite</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#about">Next Event</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#projects">Events</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="layout/past_event.php">Past event</a></li>
                <?php if(isset($_SESSION['id'])){ ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#addEvent">Creat event</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="layout/logout.php">Log out</a></li>
                <?php }else{ ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="layout/signin.php">Sign in</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="layout/login.php">Log in</a></li>
                <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>