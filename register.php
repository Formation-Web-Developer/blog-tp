<?php
require 'vendor/autoload.php';
use Gregwar\Image\Image;

require('inc/functions.php');
checkConnection();

if(isConnected()) {
    header('Location: index.php');
    exit;
}

require('inc/database.php');
$errors = array();
$success = false;

if(!empty($_POST['submitted'])){
    $pseudo = trim(strip_tags($_POST['pseudo']));
    $email = trim(strip_tags($_POST['email']));
    $password = trim(strip_tags($_POST['password']));
    $confirmPass = trim(strip_tags($_POST['confirm_password']));
    $avatar = null;

    //validation de chacun des champs
    $errors = mistake($pseudo, 2, 50, 'pseudo', $errors);
    $errors = mistake($password, 8, 32, 'password', $errors);
    $errors = mistake($email, 3, 255, 'email', $errors);
    $errors = checkPass($errors, $password, $confirmPass, 'passdiff');

    if(!empty($_FILES['avatar']) && !empty($_FILES['avatar']['type'])){

        $type = $_FILES['avatar']['type'];
        $size = $_FILES['avatar']['size'];
        $pixelSize = getimagesize($_FILES['avatar']['tmp_name']);

        if(!(strstr($type, 'jpg') || strstr($type, 'jpeg'))){
            $errors['avatar'] = 'Cette image n\'est pas une image jpg';
        }
        if($size > 1000000){
            $errors['avatar'] = 'Cette image est trop grande (<1Mo) ';
        }
        if($pixelSize[0] > 512 || $pixelSize[1] > 512 ){
            $errors['avatar'] = 'Cette image est trop grande (512x512 max) ';
        }
        $avatar = $_FILES['avatar']['tmp_name'];
    }

    //si pas d'erreur
    if(count($errors)==0) {

        $count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

        $password = passwordHash($password);

        //$id = $_GET['id'];
        $sql = "INSERT INTO users (pseudo, email, password, token, role, created_at) VALUES (:pseudo, :email, :password, :token, :role, NOW())";
        $query = $pdo->prepare($sql);

        $token = createToken(20);

        $query->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':password', $password, PDO::PARAM_STR);
        $query->bindValue(':role', $count === 0 ? ADMINISTRATOR : MEMBER);
        $query->bindValue(':token', $token, PDO::PARAM_STR);

        $query->execute();
        $id = $pdo-> lastInsertId();

        if($avatar !== null){
            move_uploaded_file($avatar,'assets/uploads/avatars/'.$id.'.jpg');
            $avatar = 'assets/uploads/avatars/'.$id.'.jpg';

            $image = Image::open($avatar);
            $image->resize(256, 256)
                  ->save('assets/uploads/avatars/'.$id.'-256x256.jpg');
                $image->resize(128, 128)
                  ->save('assets/uploads/avatars/'.$id.'-128x128.jpg');
            $image->resize(64, 64)
                  ->save('assets/uploads/avatars/'.$id.'-64x64.jpg');

            $sql = 'update users set avatar = :avatar where id = :id';
            $query = $pdo ->prepare($sql);
            $query -> bindValue(':avatar', $avatar);
            $query -> bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        }
        $success = true;

    }

}

include('inc/header.php');?>

<div class="wrap2">
<?php if($success){ ?>
<p class="success">Vos données sont envoyées avec success</p>
<?php }else { ?>
<h2>Veuillez vous inscrire</h2>
<form action="" method="POST" enctype="multipart/form-data">

    <label for="avatar">Votre avatar</label>
    <input class="noms" type="file" id="avatar" name="avatar" >
    <span class="error"><?php if(!empty($errors['avatar'])){echo $errors['avatar'];}?></span>

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
