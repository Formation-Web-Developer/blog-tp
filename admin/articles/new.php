<?php

    require '../inc/functions.php';

    $urlBase = '../';
    include '../inc/header.php';

    $errors = [];

    if(!empty($_POST['submitted']))
    {
        $title = secureTextByArray($_POST, 'title');
        $author = secureTextByArray($_POST, 'author');
        $description = secureTextByArray($_POST, 'description');
        $content = secureTextByArray($_POST, 'content');

        checkLengthTextValid($title, 5, 255, $errors, 'title');
        checkLengthTextValid($author, 4, 50, $errors, 'author');
        checkLengthTextValid($description, 10, 255, $errors, 'description');
        checkLengthTextValid($content, 10, 99_999_999_999, $errors, 'content');

        if(empty($errors))
        {
            require '../../inc/database.php';
            newArticle($pdo, $author, $title, $description, $content);
            header('Location: ../');
            exit;
        }
    }

?>
    <div class="container">
        <section id="newArticle">
            <h2>Ajouter un nouvel article</h2>
            <form action="" method="post">
                <?php
                    buildInputByArray($_POST, 'Titre *', 'text', 'title', $errors);
                    buildInputByArray($_POST, 'Auteur *', 'text', 'author', $errors);
                    buildInputByArray($_POST, 'Description *', 'text', 'description', $errors);
                    buildTextAreaByArray($_POST, 'Contenu *', 'content', 10, $errors);
                ?>
                <input type="submit" name="submitted" value="Ajouter">
            </form>
        </section>
    </div>
<?php

    include '../inc/footer.php';
