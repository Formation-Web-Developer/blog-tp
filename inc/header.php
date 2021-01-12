<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
    <header>
        <nav>
        <ul class="ulnav">
            <li><a href="index.php">Accueil</a></li>
            <?php if(!empty($_SESSION['id'])){

                if(hasRole($_SESSION, ADMINISTRATOR, MODERATOR)) {?>
                    <li><a href="admin/">Administration</a></li>
                <?php } ?>
                <li><a href="disconnect.php">Se déconnecter</a></li>
            <?php }else{ ?>
                <li><a href="login.php">Se connecter</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php } ?>

        </ul>


        </nav>
        <div class="top">
             <div class="top-img">
                 <img class="img" src="assets/img/63.jpg" alt="">
             </div>
             <div class="top-title">
               <h1>MON AFRIQUE</h1>
            <h2 class="h2">Blog des amateurs d'Afrique</h2>
             </div>

        </div>



    </header>
