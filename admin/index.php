<?php

require '../inc/database.php';
require 'inc/functions.php';

$articles = getArticles($pdo);

include('inc/header.php');

?>
    <div class="container">
        <section id="articles">
            <h2>Liste des articles</h2>
            <a href="articles/new.php" class="btn btn-success add">Ajouter un article</a>
            <div class="articles">
                <?php foreach ($articles as $article) :
                    $published = $article['visibility'] == 1; ?>
                    <div class="article">
                        <h3><?=$article['title']?></h3>
                        <p class="description"><?=$article['description']?></p>
                        <div class="misc">
                            <p class="misc-date">De <span class="misc-author"><?=$article['author']?>></span> le <?=date("d/M/Y à H:i", strtotime($published ? $article['published_at'] : $article['created_at']))?></p>
                            <div class="article-footer">
                                <p class="misc-state"><i class="fas fa-circle <?=$published ? 'publish' : 'draft'?>"></i> <?=$published ? 'Publié' : 'Brouillon'?></p>
                                <a href="#" class="btn btn-primary">Modifier</a>
                                <a href="#" class="btn btn-danger">Supprimer</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
<?php

include('inc/footer.php');
