<?php
    session_start();

    require '../inc/functions.php';
    require '../../inc/roles.php';

    if(!isConnected() || !hasRole($_SESSION, ADMINISTRATOR))
    {
        header('Location: ../');
        exit;
    }

    require '../../inc/database.php';

    $article = getArticleById($pdo, intval($_GET['id']));

    if(empty($article))
    {
        header('Location: ../');
        exit;
    }

    $errors = [];
    $visibilities = [
            '0' => 'Brouillon',
            '1' => 'Publié'
    ];

    if(!empty($_POST['submitted']))
    {
        $title = secureTextByArray($_POST, 'title');
        $description = secureTextByArray($_POST, 'description');
        $content = secureTextByArray($_POST, 'content');
        $visibility = secureTextByArray($_POST, 'visibility');

        checkLengthTextValid($title, 5, 255, $errors, 'title');
        checkLengthTextValid($description, 10, 255, $errors, 'description');
        checkLengthTextValid($content, 10, 99999999999, $errors, 'content');

        checkValueSelect($visibilities, $visibility, $errors, 'visibility');

        if(empty($errors))
        {
            editArticle($pdo, $article['id'], $title, $description, $content, $visibility, $article['published_at'] == null && $visibility == 1);
            $info = 'L\'article a bien été modifier !';
        }
    }

    if(!empty($_POST['comment']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
        if($_POST['comment'] === 'Accepter') {
            validComment($pdo, intval($_POST['id']));
            $info = 'Le commentaire a bien été accepté !';

        } elseif ($_POST['comment'] == 'Refuser' || $_POST['comment'] == 'Supprimer') {
            deleteComment($pdo, intval($_POST['id']));
            $info = $_POST['comment'] == 'Refuser' ? 'Le commentaire a bien été refusé !' : 'Le commentaire a bien été supprimé !';
        }
    }

    $urlBase = '../';
    include '../inc/header.php';
?>

    <div class="container">

        <?php
        if( isset($info) ) { ?>
            <div class="badge badge-success">
                <?=$info?>
            </div>
        <?php } ?>

        <section id="editArticle">
            <h2>Edition de l'article: <?=$article['title']?></h2>
            <form action="" method="post">
                <?php
                    buildInput(getValueByArray($_POST, 'title', $article['title']), 'Titre *', 'text', 'title', $errors);
                    buildInput(getValueByArray($_POST, 'description', $article['description']), 'Description *', 'text', 'description', $errors);
                    buildTextArea(getValueByArray($_POST, 'content', $article['content']), 'Contenu *', 'content', 10, $errors);

                    buildSelect('Visibilité', 'visibility', $visibilities, getValueByArray($_POST, 'visibility', $article['visibility']), $errors);
                ?>
                <input type="submit" name="submitted" value="Modifier">
            </form>
        </section>

        <?php
            $comments = getCommentsByArticle($pdo, intval($article['id']));
            if(!empty($comments)) { ?>
                <section id="comments">
                    <h2>Commentaires (<?=count($comments)?>)</h2>
                    <div class="comments">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-header">
                                    <h3><?=$comment['pseudo']?></h3>
                                    <p class="description"><?=$comment['content']?></p>
                                    <p class="misc">Le <?=date("d/M/Y à H:i", strtotime($comment['created_at']))?></p>
                                </div>
                                <div class="comment-footer">
                                    <?php if($comment['state'] == 0) { ?>
                                        <form action="" method="post">
                                            <input type="hidden" name="id" value="<?=$comment['id']?>">
                                            <input type="submit" name="comment" value="Accepter" class="btn btn-primary">
                                        </form>
                                    <?php } ?>
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?=$comment['id']?>">
                                        <input type="submit" name="comment" value="<?=$comment['state'] == 0 ? 'Refuser' : 'Supprimer'?>" class="btn btn-danger">
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php }
        ?>
    </div>

<?php
    include '../inc/footer.php';
