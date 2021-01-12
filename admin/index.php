<?php

session_start();

require 'inc/functions.php';
require '../inc/roles.php';

if(!isConnected() || !hasRole($_SESSION, ADMINISTRATOR, MODERATOR)){
    header('Location: ../login.php');
    exit;
}

require '../inc/database.php';


if(!empty($_POST['deleted']) && !empty($_POST['id']) && is_numeric($_POST['id']))
{
    $deleted = deleteArticle($pdo, intval($_POST['id']));
}

$articles = getArticles($pdo);

include('inc/header.php');

?>
    <div class="container">

        <?php
        if( isset($deleted) ) { ?>
            <div class="badge badge-success">
                L'article a bien été supprimé !
            </div>
        <?php }

        if(hasRole($_SESSION, ADMINISTRATOR)) { ?>
            <section id="articles">
                <h2>Liste des articles</h2>
                <a href="articles/new.php" class="btn btn-success add">Ajouter un article</a>
                <div class="articles">
                    <?php foreach ($articles as $article) :
                        $published = $article['visibility'] == 1; ?>
                        <div class="article">
                            <div class="article-header">
                                <h3><?=$article['title']?></h3>
                                <p class="description"><?=$article['description']?></p>
                                <p class="misc">De <span class="misc-author"><?=$article['author']?></span> le <?=date("d/M/Y à H:i", strtotime($published ? $article['published_at'] : $article['created_at']))?></p>
                            </div>

                            <div class="article-footer">
                                <p class="misc-state"><i class="fas fa-circle <?=$published ? 'publish' : 'draft'?>"></i> <?=$published ? 'Publié' : 'Brouillon'?></p>
                                <a href="articles/edit.php?id=<?=$article['id']?>" class="btn btn-primary">Modifier</a>
                                <form action="" method="post" onsubmit="return confirmDeleteArticle()">
                                    <input type="hidden" name="id" value="<?=$article['id']?>">
                                    <input type="submit" class="btn btn-danger" value="Supprimer" name="deleted" />
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php } ?>
    </div>
<?php

include('inc/footer.php');
