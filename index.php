<?php
session_start();

require('inc/functions.php');
require('inc/database.php');


$title= 'Accueil - MonBlog.fr';

$articles = getAllArticlesByStatus($pdo);
include('inc/header.php'); ?>
<div id="all">
<?php foreach($articles as $article) { ?>

<div class="wrap">
        <h2> <?= $article['title'];?></h2>
        
    <p class="brown">Auteur: <?= $article['author'];?></p>        
    <div class="article">
    <p> Déscription: <?= $article['description'];?></p>             
    <p class="pub"> Publié le: <?= date("Y/m/d", strtotime($article['published_at']));?></p>

    </div>
    
    <button class="voir"><a href="article.php?id=<?=$article['id']; ?>">Voir plus</a></button>   
    

</div>

<?php } ?>


</div>
<?php
include('inc/footer.php');
