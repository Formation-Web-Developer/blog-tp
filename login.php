<?php
require 'admin/inc/functions.php';
    checkConnection();

    if(isConnected()) {
        header('Location: index.php');
        exit;
    }
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
            if($user !== NULL && !empty($user['invalid_token'])){
                $error = 'Ce compte n\'a pas été confirmé, veuillez verifier votre adresse mail';   
            }
            elseif($user != null) {
                $_SESSION['user'] = $user;
                header('Location: index.php');
                exit;
            }else{
                $error = 'Les informations d\'identification ne sont pas valides !';
            }
        
        }
    }

    include 'inc/header.php';
?>
    <div class="container">
        <h2 class="titleconnect">Espace de connexion</h2>

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
