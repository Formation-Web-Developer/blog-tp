<?php
session_start();
require('inc/functions.php');
if(!isConnected()) {
    header('Location: index.php');
    exit;
}



include('inc/header.php'); ?>

<div class="user">
<img src="https://img.icons8.com/fluent/128/000000/leopard.png"/>
<h2><?=$_SESSION['pseudo']?></h2>
<p><?=$_SESSION['email']?></p>
<p><?=$_SESSION['role']?></p>


</div>



<?php
include('inc/footer.php');