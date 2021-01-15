<?php
require('inc/functions.php');
checkConnection();

if(!isConnected()) {
    header('Location: index.php');
    exit;
}



include('inc/header.php'); ?>

<div class="user">
<?php if(!empty($_SESSION['user']['avatar'])){ ?>
    <img src="<?=$_SESSION['user']['avatar'] ?>"/>
<?php }else{?>
    <img src="https://img.icons8.com/ios/96/000000/leopard.png"/> <?php } ?>
<h2><?=$_SESSION['user']['username']?></h2>
<p><?=$_SESSION['user']['email']?></p>
<p><?=$_SESSION['user']['role']?></p>
<a href="edit-profil.php">Modifier mon profil</a>

</div>



<?php
include('inc/footer.php');
