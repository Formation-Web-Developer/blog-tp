<?php
session_start();
require('inc/functions.php');
if(!isConnected()) {
    header('Location: index.php');
    exit;
}



include('inc/header.php'); ?>

<div class="user">
<?php if(!empty($_SESSION['avatar'])){ ?>
    <img src="<?=$_SESSION['avatar'] ?>"/>
<?php }else{?>
    <img src="https://img.icons8.com/ios/96/000000/leopard.png"/> <?php } ?>
<h2><?=$_SESSION['pseudo']?></h2>
<p><?=$_SESSION['email']?></p>
<p><?=$_SESSION['role']?></p>
<a href="edit-profil.php">Modifier mon profil</a>

</div>



<?php
include('inc/footer.php');