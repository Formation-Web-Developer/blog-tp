<?php

require 'inc/functions.php';
require '../inc/roles.php';

checkConnection();

if(!isConnected() || !hasRole($_SESSION['user'], ADMINISTRATOR, MODERATOR)){
    header('Location: ../login.php');
    exit;
}

require '../inc/database.php';


if(!empty($_POST['deleted']) && !empty($_POST['id']) && is_numeric($_POST['id']) && hasRole($_SESSION['user'], ADMINISTRATOR))
{
    deleteArticle($pdo, intval($_POST['id']));
    $info = 'L\'article a bien été supprimé !';
}

if(!empty($_POST['comment']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
    if($_POST['comment'] === 'Accepter') {
        validComment($pdo, intval($_POST['id']));
        $info = 'Le commentaire a bien été accepté !';

    } elseif ($_POST['comment'] == 'Refuser') {
        deleteComment($pdo, intval($_POST['id']));
        $info = 'Le commentaire a bien été refusé !';
    }
}

if(!empty($_POST['role_modify']) && !empty($_POST['id']) && is_numeric($_POST['id']) && hasRole($_SESSION['user'], ADMINISTRATOR))
{
    changeRoleByUser($pdo, intval($_POST['id']), getRoleByValue($_POST['role']));
    $info = 'Le role a bien été modifier';
}

$comments = getWaitingComments($pdo);

include('inc/header.php');

?>
    <div class="container">

        <?php
        if( isset($info) ) { ?>
            <div class="badge badge-success">
                <?=$info?>
            </div>
        <?php }


        if(!empty($comments)) { ?>
            <section id="comments">
                <h2>Liste des commentaires en attente</h2>
                <div class="comments">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <h3><?=$comment['title']?></h3>
                                <p class="description"><?=$comment['content']?></p>
                                <p class="misc">De <a class="misc-author" href="#"><?=$comment['pseudo']?></a> le <?=date("d/M/Y à H:i", strtotime($comment['created_at']))?></p>
                            </div>
                            <div class="comment-footer">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?=$comment['id']?>">
                                    <input type="submit" name="comment" value="Accepter" class="btn btn-primary">
                                </form>
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?=$comment['id']?>">
                                    <input type="submit" name="comment" value="Refuser" class="btn btn-danger">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php }

        if(hasRole($_SESSION['user'], ADMINISTRATOR)) {
        $articles = getArticles($pdo); ?>

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
                                <p class="misc">De <a href="../profil.php?id=<?=$article['author']?>" class="misc-author"><?=!empty($article['pseudo']) ? $article['pseudo'] : 'Not defined'?></a> le <?=date("d/M/Y à H:i", strtotime($published ? $article['published_at'] : $article['created_at']))?></p>
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

        <section id="users">
            <h2>Liste des utilisateurs</h2>
            <?php $users = getUsers($pdo); ?>
            <div class="users">
                <?php foreach ($users as $user): ?>
                    <div class="user">
                        <h3><?=$user['pseudo']?></h3>
                        <?php if(hasRole($_SESSION['user'], ADMINISTRATOR)) { ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?=$user['id']?>">
                                <?php
                                    buildSelect('Role', 'role', getRoles(), getValueByArray($_POST, 'role', getRoleByUser($user)), []);
                                ?>
                                <input class="btn btn-primary" type="submit" name="role_modify" value="Modifier">
                            </form>
                        <?php } else { ?>
                            <p><?=getRoleByUser($user)?></p>
                        <?php } ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
<?php

include('inc/footer.php');
