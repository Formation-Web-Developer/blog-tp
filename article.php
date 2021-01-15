

<?php

require('inc/functions.php');
require('inc/database.php');

checkConnection();


include('inc/header.php');

if(!empty($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];
}else{
    die('404');
}

$sql = "SELECT articles.*, users.pseudo FROM articles INNER JOIN users ON users.id=articles.author WHERE articles.id = $id";
$query = $pdo->prepare($sql);
$query->execute();
$article = $query->fetch();

if(empty($article)){
    die('404');
}
$errors = array();
if(isConnected() && !empty($_POST['submitted']) && !empty($_POST['comment'])){
    $commentaire = trim(strip_tags($_POST['comment']));
    $errors = mistake($commentaire, 3, 255, 'comment', $errors);
    if(!empty($errors)){
        $sql = 'INSERT INTO comments (article, user, content, created_at) VALUES (:article, :user, :content, NOW())';
        $query = $pdo->prepare($sql);
        $query->bindValue(':article', $article['id'], PDO::PARAM_INT);
        $query->bindValue(':user', $_SESSION['user']['identifier'], PDO::PARAM_INT);
        $query->bindValue(':content', $commentaire, PDO::PARAM_STR);
        $query->execute();
        

    }
}
?>
<div class="wrap1">
        <div class="content">
        <h2 class="h2-2"> <?= $article['title'];?></h2>
        <p class="author">Par: <?= $article['pseudo'];?></p>
        <p> Déscription: <?= $article['description'].'<br>'.$article['content'];?></p>
        <p class="date">  Publié le: <?= date("Y/m/d à H:i", strtotime($article['published_at']));?></p>
        </div>
        <div class="wrap2">
            <form action="" method="POST">
                <input class="text-comment" type="text" name="comment">
                <input class="sub-comment" type="submit" name="submitted" value="Envoyer le commentaire">
            </form>
        </div>
        <?php
        $comments = getCommentsByArticle($pdo, $article['id'], isConnected()? $_SESSION['user']['identifier']: -1, isConnected() && hasRole($_SESSION, MODERATOR, ADMINISTRATOR));
        foreach($comments as $comment){
            ?>
            <div class="comment">
                <h3>Auteur: <?=$comment['pseudo'];?></h3>
                <p><?=$comment['content']  ?></p>
                <p><?=date("Y/m/d à H:i", strtotime($comment['created_at'])); ?></p>

            </div>
        <?php }

        ?>

        <div>

        </div>


</div>
    <?php

include('inc/footer.php');
