<?php
    session_start();

    include ('config/config.php');

    try {
		$bdd = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
    }
    $date = date("Y-m-d");
    $reqevent = $bdd->prepare("SELECT evenement.*, categorie.title, utilisateur.pseudo, utilisateur.id FROM evenement 
                                LEFT JOIN utilisateur ON auteur = utilisateur.id
                                LEFT JOIN categorie ON categorie_id = categorie.id  
                                WHERE date >= ? ORDER BY date, time");
    $reqevent->execute(array($date));
    $reqshowevent = $bdd->prepare("SELECT evenement.*, categorie.title, utilisateur.pseudo, utilisateur.id FROM evenement 
                                LEFT JOIN utilisateur ON auteur = utilisateur.id
                                LEFT JOIN categorie ON categorie_id = categorie.id  
                                WHERE date >= ? ORDER BY date, time LIMIT 1");
    $reqshowevent->execute(array($date));
    $showevent = $reqshowevent->fetch()
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Jepsens-Brite</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/album/">
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <?php include ('layout/header.php'); ?>
        <!-- Masthead-->
        <header class="masthead">
            <div class="container d-flex h-100 align-items-center">
                <div class="mx-auto text-center">
                    <h1 class="mx-auto my-0 text-uppercase">JEPSENS-BRITE</h1>
                    <h2 class="text-white-50 mx-auto mt-2 mb-5">A website where you can find and create events.</h2>
                    <a class="btn btn-primary js-scroll-trigger" href="#about">Show events</a>
                </div>
            </div>
        </header>
        <!-- About-->
        <section class="about-section text-center" id="about">
            <div class="container about">
                <div class="row about">
                    <div class="col-lg-8 mx-auto about">
                        <h2 class="text-white mb-4"><?= $showevent['titre']; ?></h2>
                        <p class="text-white-50"><?= $showevent['description']; ?></p>
                    </div>
                </div>
                <a class="btn btn-primary js-scroll-trigger more-info" href="layout/show_event.php?id=<?= $showevent['0']; ?>">More information</a>
            </div>
        </section>
        <!-- Projects-->
        <section class="projects-section bg-light" id="projects">
        <div class="album py-5 bg-light">
            <div class="container">
                <div class="row">
                <?php while($event = $reqevent->fetch()){ ?>
                  <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                      <div class="bd-placeholder-img card-img-top img-card" style='
                        <?php if($event["image"]){ ?>
                            background-image:url("assets/event/<?= $event['image']; ?>");
                        <?php }else{ ?>
                            background-image:url("assets/img/lala.jpeg");
                        <?php } ?>
                            background-repeat: no-repeat; 
                            background-position: center;'>
                      </div>
                      <div class="card-body">
                        <h5 class="card-text"><?= $event['titre']; ?></h5>
                        <p class="card-text"><a href="layout/profil.php?id=<?= $event['id'];?>"><?= $event['pseudo']; ?></a></p>
                        <p class="card-text"><?= $event['adresse']; ?>, <?= $event['cp'];?> <?= $event['ville'];?></p>
                        <div class="d-flex justify-content-between align-items-center">
                          <div class="btn-group">
                            <a href='layout/show_event.php?id=<?= $event['0']; ?>' class="btn btn-sm btn-outline-secondary">View</a>
                          </div>
                          <small class="text-muted"><?= $event['date']; ?>, <?= $event['time'];?></small>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?> 
                </div> 
            </div>
        </div> 
        <?php if(isset($_SESSION['id'])){ ?>
            <div class="create-section text-center container" id='addEvent'>
                <a href="layout/create_event.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Create event</a>
            </div>
        <?php } ?>
        </section>
        <!-- Contact-->
        <section class="contact-section bg-black" id="contact">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-map-marked-alt text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Address</h4>
                                <hr class="my-4" />
                                <div class="small text-black-50">Rue de Mulhouse 36, 4020 Liège</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-envelope text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Email</h4>
                                <hr class="my-4" />
                                <div class="small text-black-50">bryanrasamizafy@gmail.com</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Phone</h4>
                                <hr class="my-4" />
                                <div class="small text-black-50">+32 (0) 4 239 69 00</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="social d-flex justify-content-center">
                    <a class="mx-2" href="https://twitter.com/becodeorg"><i class="fab fa-twitter"></i></a>
                    <a class="mx-2" href="https://www.facebook.com/becode.org/"><i class="fab fa-facebook-f"></i></a>
                    <a class="mx-2" href="https://github.com/becodeorg"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </section>
        <!-- Footer-->
        <footer class="footer bg-black small text-center text-white-50"><div class="container">Copyright © Your Website 2020 by BRYAN, MICHAEL and MATHIEU</div></footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <!-- Third party plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>
