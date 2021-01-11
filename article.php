<!DOCTYPE html>

<?php

require('inc/functions.php');
require('inc/database.php');

include('inc/header.php');

if(!empty($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];
}else{
    die('404');
}

$sql = "SELECT * FROM articles WHERE id = $id";
$query = $pdo->prepare($sql);
$query->execute();
$article = $query->fetch();

if(empty($article)){
    die('404');
}
?>
<div class="wrap">
    
        <h2> <?= $article['title'];?></h2>
        <p>Auteur: <?= $article['author'];?></p>        
        <p> Déscription: <?= $article['description'].'<br>'.$article['content'];?></p>      

        <p> Date de creation: <?= date("Y/m/d à H:i", strtotime($article['published_at']));?></p>        

</div>
    <?php

include('inc/footer.php');