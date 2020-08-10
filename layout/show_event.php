<?php 
    session_start();

    include '../config/config.php'; 

    try{
    $db = new PDO($dbdsn, $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }catch (Exeption $e){
        die('erreur :' .$e ->getMessage());
    } 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    if(isset($_SESSION['id'])){
        $getidHeader = intval($_SESSION['id']);
        $requserHeader = $db->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $requserHeader->execute(array($getidHeader));
        $userinfoHeader = $requserHeader->fetch();
    }

    $countParticipant = $db->prepare("SELECT * FROM user_event WHERE event_id = ?");
    $countParticipant->execute(array($_GET['id']));
    $nbParticipant = $countParticipant->rowCount();

    $countParticipant = $db->prepare("SELECT * FROM user_event WHERE event_id = ?");
    $countParticipant->execute(array($_GET['id']));
    $nbParticipant = $countParticipant->rowCount();


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
    if(isset($_SESSION['id'])){

        if(isset($_POST['dontGo'])){
            $dontGo = $db->prepare('DELETE FROM user_event WHERE event_id = ? && user_id = ?');
            $dontGo->execute(array($_GET['id'],$_SESSION['id']));
            header("location: show_event.php?id=".$event['id']);
            exit();
        }
        if(isset($_POST['goEvent'])){
            $go = $db->prepare('INSERT INTO user_event (event_id, user_id) VALUES (:event , :user)');
            $go->bindParam('event',$_GET['id']);
            $go->bindParam('user',$_SESSION['id']);
            $go->execute();
            header("location: show_event.php?id=".$event['id']);
            exit();
        }

        if (isset($_POST['delete'])) { 
            $deleteEvent = $db ->prepare("DELETE FROM evenement WHERE id = ?" );
            $deleteEvent ->execute(array($idevent));
            $deletecomments = $db->prepare("DELETE FROM commentaires WHERE event_id = ?");
            $deletecomments->execute(array($idevent));
            $deletesubcat = $db->prepare("DELETE FROM subcat_event WHERE event_id = ?");
            $deletesubcat->execute(array($idevent));
            header("location: ../index.php");
        }
    
        if(isset($_POST['sendComment'])){
            $addComment = $db->prepare("INSERT INTO commentaires (commentaire, date_commentaire, createur_id, event_id) VALUES (:text ,DATE_ADD(NOW(), interval +2 HOUR), :author, :event)");
            $addComment->bindParam('text',$_POST['userComment']);
            $addComment->bindParam('author',$_SESSION['id']);
            $addComment->bindParam('event',$idevent);
            $addComment->execute();
            header("location: show_event.php?id=".$event['id']);
            exit();
        }
    } 
        $comments = $db->prepare("SELECT pseudo,u.id, date_commentaire, commentaire, mail, c.id,  
                                        YEAR(date_commentaire), 
                                        MONTHNAME(date_commentaire), 
                                        DAY(date_commentaire), 
                                        DAYNAME(date_commentaire), 
                                        HOUR(date_commentaire), 
                                        MINUTE(date_commentaire)
                                    FROM commentaires AS c LEFT JOIN utilisateur AS u ON c.createur_id = u.id WHERE event_id =  ? ORDER BY date_commentaire DESC");
        $comments->execute(array($_GET['id']));
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Show event</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico" />

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style_event.css">
    <!-- <link rel="stylesheet" href="css/responsive.css"> -->
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
    <?php
        if($event['13'] ==0){
          $minToShow = '00';
        } else {
          $minToShow = $event['13'];
        }
      ?>
    <!-- slider_area_start -->
    <div class="slider_area slider_bg_1">
        <div class="slider_text">
            <div class="container">
                <div class="position_relv">
                    <div class="row">
                        <div class="col-xl-9">
                            <div class="title_text">
                                <h3><?= $event['titre']; ?></h3>
                                <form method="POST">
                                    <?php 
                                        if(isset($_SESSION['id'])){
                                            $verifParticipation = $db->prepare('SELECT * FROM user_event WHERE event_id = ? && user_id = ?');
                                            $verifParticipation->execute(array($_GET['id'],$_SESSION['id']));
                                            $participation = $verifParticipation->rowCount();
                                            if($participation == 1){
                                    ?>
                                                <input class="boxed-btn-white" type="submit" value="Je ne participe plus" name="dontGo">
                                    <?php   }else{ ?>
                                                <input class="boxed-btn-white" type="submit" value="Je participe" name="goEvent">

                                    <?php }} ?>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="countDOwn_area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-4 col-md-6 col-lg-4">
                        <div class="single_date">
                        <i class="fas fa-map-marked-alt"></i>
                            <span><?= $event['8'] . ',' . $event['9'] . ' ' .  $event['10'] ?></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-lg-3">
                        <div class="single_date">
                        <i class="far fa-calendar"></i>
                            <span><?php echo $event['date'] . ' ' . $event['time']?> </span>
                        </div>
                    </div>
                    <div class='col-xl-5 col-md-12 col-lg-5'>
                        <span id="clock">
                            <div class='countdown_time single_date'>   
                                <span><?php echo $nbParticipant; ?> Participant(s)</span>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- slider_area_end -->

    <!-- about_area_event -->
    <div class="about_area" id='about'>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6 col-md-6">
                    <div class="about_thumb">
                        <?php if($event['image']){ ?>
                            <img src="../assets/event/<?= $event['image']; ?>" alt="image event">
                        <?php }elseif($event['video']){ ?>
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $event["video"]; ?>" frameborder="0" 
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-xl-5 offset-xl-1 col-md-6">
                    <div class="about_info">
                        <div class="section_title">
                            <span class="sub_heading">Welcome To</span>
                            <h3><?php echo $event['titre']; ?></h3>
                        </div>
                        <p><?= $event['description']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- about_area_end -->

    <!-- google maps -->
    <div class='speakers_area'>
        <div class='container'>
            <div id="map-container-google-2" class="z-depth-1-half map-container" style="height: 500px">
              <iframe src="https://maps.google.com/maps?q=<?= $event['adresse'].' '.$event['cp'] ;?> &output=embed"  height="100%" width="100%" frameborder="0"
                style="border:0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    <!-- google maps end -->

    <!-- comment -->
    <div class='comment_event'>
        <div class='container'>
            <?php if(isset($_SESSION['id'])){ ?>
                <form method="POST" style="margin-top:40px;">
                    <div class="md-form">
                        <textarea id="form10" class="md-textarea form-control" rows="3" name='userComment'></textarea>
                        <label for="form10">Comment</label>
                    </div>  
                    <div class="modal-footer">
                        <input class="btn btn-info" type="submit" name="sendComment" value="Post Comment">
                    </div>
                </form>
            <?php }else{ ?>
                 <div class="connectToComment d-flex justify-content-around d-block mx-auto" style="width: 500px;">
                    <a href="login.php"> <button class="btn btn-info">Log in to comment</button></a>  
                    <a href="signin.php"><button class="btn btn-info">Sign in to comment</button></a>
                 </div>
            <?php } ?>
        </div>
    </div> 
    <!-- comment end -->
    
    <!-- event_area_start -->
    <div class="event_area">
        <div class="container">
            <div class="double_line">
                <div class="row">
                    <div class="col-xl-3 col-lg-3">
                        <div class="date">
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-9">
                        <?php 
                            while($showComments = $comments->fetch()){
                                if($showComments['11'] ==0){
                                    $minToShow = '00';
                                } else {
                                    $minToShow = $showComments['11'];
                                }
                                if(isset($showComments['mail'])){
                        ?>
                                    <div class="single_speaker">
                                        <img src="<?php echo "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $showComments['mail']) ) ). "&s=" . 10;?>" alt="">
                                        <div class="speaker-name">
                                            <div class="heading d-flex justify-content-between align-items-center">
                                                <span><a class="h4" href="<?php echo "user.php?id=".$showComments['1'];?>"><?php echo $showComments['pseudo'];?></a></span>
                                                <div class="time">
                                                <?php echo $showComments['9'] . ' ' . $showComments['8'] . ' ' . $showComments['7'] . ' ' . $showComments['6'] . ' - ' . $showComments['10'] . ':' . $showComments['11'];?>
                                                </div>
                                            </div>
                                            <p><?php echo $showComments['commentaire'];?></p>
                                            <?php 
                                                if(isset($_SESSION['id'])){
                                                    if($_SESSION['id'] === $showComments['1'] OR $_SESSION['admin'] == 1){
                                            ?>
                                                            <a href="<?php echo 'delete_comment.php?id='.$showComments['id'].'&idauthor='.$showComments['1'].'&eventid='.$idevent; ?>">
                                                            <i class="fas fa-times buttonsection" style="float:right"></i></a>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                        <?php }} ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- event_area_end -->

    <!-- footer_start -->
    <footer class="footer footer_bg_1">
        <div class="circle_ball d-none d-lg-block">
            <?php 
                if (isset ($_SESSION['id'])) {
                    if ($_SESSION['id'] === $event['auteur']) {
            ?>
                    <div class="modal-footer">
                        <a href="edit_event.php?id=<?= $event['id'];?>" class="btn btn-primary">Edit event</a>
                        <?php }
                        if($_SESSION['id'] === $event['auteur'] OR $_SESSION['admin'] == 1){ ?>
                            <form action="" method='POST'>
                                <input type="submit" name="delete" value="Delete event" class="btn btn-primary">
                            </form>
                        <?php } ?>
                    </div>
            <?php } ?>
        </div>
    </footer>
    <!-- footer_end -->






    <!-- JS here -->
    <script src="../js/scripts.js"></script>

</body>

</html>