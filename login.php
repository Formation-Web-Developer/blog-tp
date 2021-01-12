<?php
    session_start();

    if(!empty($_SESSION['id'])) {
        header('Location: index.php');
        exit;
    }

    require 'admin/inc/functions.php';

    $errors = [];
    if(!empty($_POST['submitted']))
    {
        $email = secureTextByArray($_POST, 'email');
        checkEmailValid($email, $errors, 'email');
        $password = secureTextByArray($_POST, 'password');

        if(empty($errors))
        {
            require 'inc/database.php';

            $user = getUser($pdo, $email, $password);

            if($user != null) {
                $_SESSION = $user;
                header('Location: index.php');
                exit;
            }

            $error = 'Les informations d\'identification ne sont pas valides !';
        }
    }

    include 'inc/header.php';
?>
    <div class="container">
        <h1>Espace de connexion</h1>

        <?php if(isset($error)) { ?>
            <div class="bagde badge-danger">
                <?=$error?>
            </div>
        <?php } ?>

        <form action="" method="post">
            <?php
                buildInput(getValueByArray($_POST, 'email'), 'Email *', 'email', 'email', $errors);
                buildInput('', 'Mot de passe *', 'password', 'password', $errors);
            ?>

            <input type="submit" value="Se connecter" name="submitted">
        </form>
    </div>
<?php
    include 'inc/footer.php';
