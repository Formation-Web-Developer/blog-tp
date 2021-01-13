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
            $edit = editArticle($pdo, $article['id'], $title, $description, $content, $visibility, $article['published_at'] == null && $visibility == 1);
        }
    }

    $urlBase = '../';
    include '../inc/header.php';
?>

    <div class="container">

        <?php
        if( isset($edit) ) { ?>
            <div class="badge badge-success">
                L'article a bien été modifier !
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
    </div>

<?php
    include '../inc/footer.php';
