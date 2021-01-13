<?php
    session_start();

    require '../inc/functions.php';
    require '../../inc/roles.php';

    if(!isConnected() || !hasRole($_SESSION, ADMINISTRATOR))
    {
        header('Location: ../');
        exit;
    }

    $errors = [];

    if(!empty($_POST['submitted']))
    {
        $title = secureTextByArray($_POST, 'title');
        $author = $_SESSION['id'];
        $description = secureTextByArray($_POST, 'description');
        $content = secureTextByArray($_POST, 'content');

        checkLengthTextValid($title, 5, 255, $errors, 'title');
        checkLengthTextValid($description, 10, 255, $errors, 'description');
        checkLengthTextValid($content, 10, 99999999999, $errors, 'content');

        if(empty($errors))
        {
            require '../../inc/database.php';
            newArticle($pdo, $author, $title, $description, $content);
            header('Location: ../');
            exit;
        }
    }

    $urlBase = '../';
    include '../inc/header.php';
?>
    <div class="container">
        <section id="newArticle">
            <h2>Ajouter un nouvel article</h2>
            <form action="" method="post">
                <?php
                    buildInputByArray($_POST, 'Titre *', 'text', 'title', $errors);
                    buildInputByArray($_POST, 'Description *', 'text', 'description', $errors);
                    buildTextAreaByArray($_POST, 'Contenu *', 'content', 10, $errors);
                ?>
                <input type="submit" name="submitted" value="Ajouter">
            </form>
        </section>
    </div>
<?php

    include '../inc/footer.php';
