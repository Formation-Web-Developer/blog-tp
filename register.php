
<?php

require('inc/functions.php');
require('inc/database.php');
$errors = array();
$success = false;

if(!empty($_POST['submitted'])){
    $pseudo = trim(strip_tags($_POST['pseudo']));
    $email = trim(strip_tags($_POST['email']));
    $password = trim(strip_tags($_POST['password']));
    $confirmPass = trim(strip_tags($_POST['confirm_password']));
        
    //validation de chacun des champs
    $errors = mistake($pseudo, 2, 50, 'pseudo', $errors);
    $errors = mistake($password, 8, 32, 'password', $errors);
    $errors = mistake($email, 3, 255, 'email', $errors);
    $errors = checkPass($errors, $password, $confirmPass, 'passdiff');    

        //si pas d'erreur 
    if(count($errors)==0) {
    
    //$id = $_GET['id'];
    $sql = "INSERT INTO users (pseudo, email, password, token_verified, created_at) VALUES (:pseudo, :email, :password, :token_verified ,NOW())";
    $query = $pdo->prepare($sql);
    
    $token = OAuthProvier::generateToken(20);

    $query->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':password', $password, PDO::PARAM_STR);
    $query->bindValue(':token_verified', $token, PDO::PARAM_STR);

    $query->execute();
    $success = true;
    die();
    }
   
}

include('inc/header.php');?>

<div class="wrap2">
<?php if($success){ ?>
<p class="success">Vos données sont envoyées avec success</p>
<?php }else { ?>
<h1>Veuillez vous inscrir</h1>
<form action="" method="POST">
    
    <label for="pseudo">Pseudo</label>
    <input class="noms" type="text" id="pseudo" name="pseudo" value="<?php if(!empty($_POST['pseudo'])) {echo $_POST['pseudo'];} ?>">
    <span class="error"><?php if(!empty($errors['pseudo'])){echo $errors['pseudo'];}?></span>
    
    <label for="email">Email</label>
    <input class="noms" type="email" id="email" name="email" value="<?php if(!empty($_POST['email'])) {echo $_POST['email'];} ?>">
    <span class="error"><?php if(!empty($errors['email'])){echo $errors['email'];}?></span>
    
    <label for="password">Mot de passe</label>
    <input class="noms" type="password" id="password" name="password">
    <span class="error"><?php if(!empty($errors['password'])){echo $errors['password'];}?></span>

    <label for="confirm_password">Confirmer le mot de passe</label>
    <input class="noms" type="password" id="confirm_password" name="confirm_password">
    <span class="error"><?php if(!empty($errors['passdiff'])){echo $errors['passdiff'];}?></span>

    <input class="button" type="submit" name="submitted" value="S'inscrire"> 
</form>
<?php } ?>
</div>
 
</body>
</html>
<?php
include('inc/footer.php');