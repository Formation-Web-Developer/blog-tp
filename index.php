<?php

require('inc/functions.php');
require('inc/database.php');


$title= 'Accueil - MonBlog.fr';
include('inc/header.php');
$articles = getAllArticlesByStatus($pdo);

//debug($articles);

foreach($articles as $article) { ?>

    <div class="wrap">
    
        <h2> <?= $article['title'];?></h2>
        <p>Auteur: <?= $article['author'];?></p>        
        <p> Déscription: <?= $article['description'];?></p>             
        <p> Date de creation: <?= date("Y/m/d à H:i", strtotime($article['published_at']));?></p>
        <button><a href="article.php?id=<?=$article['id']; ?>">Voir plus</a></button>
        
    
        

    </div>
    
 <?php }
include('inc/footer.php');
