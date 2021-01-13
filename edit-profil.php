<?php
require 'vendor/autoload.php';
use Gregwar\Image\Image;
session_start();
require('inc/functions.php');
if(!isConnected()) {
    header('Location: index.php');
    exit;
}
require('inc/database.php');
$errors = array();
$success = false;

if(!empty($_FILES['avatar'])){
         
    $type = $_FILES['avatar']['type'];
    $size = $_FILES['avatar']['size'];
    $pixelSize = getimagesize($_FILES['avatar']['tmp_name']);

    if(!(strstr($type, 'jpg') || strstr($type, 'jpeg'))){
        $errors['avatar'] = 'Cette image n\'est pas une image jpg';
    }
    if($size > 1000000){
        $errors['avatar'] = 'Cette image est trop grande (<1Mo) ';
    }
    if($pixelSize[0] > 128 || $pixelSize[1] > 128 ){
        $errors['avatar'] = 'Cette image est trop grande (128x128 max) ';
    }
    $avatar = $_FILES['avatar']['tmp_name'];

    if(count($errors)==0) {
        
        $id = $_SESSION['id'];
    
        if($avatar !== null){
            move_uploaded_file($avatar,'assets/uploads/avatars/'.$id.'.jpg');
            $avatar = 'assets/uploads/avatars/'.$id.'.jpg';

            $image = Image::open($avatar);
            $image->resize(64, 64)
              ->save('assets/uploads/avatars/'.$id.'-64x64.jpg');
            $image->resize(32, 32)
              ->save('assets/uploads/avatars/'.$id.'-32x32.jpg');

            $sql = 'update users set avatar = :avatar where id = :id';
            $query = $pdo ->prepare($sql);
            $query -> bindValue(':avatar', $avatar);
            $query -> bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            $success = true;
        }
    
    }
}

  
include('inc/header.php'); ?>
<?php if($success){ ?>
    <p>Avatar modifié!</p>
<?php }else{ ?>
    <form action="" method="POST" enctype="multipart/form-data">    
        <label for="avatar">Votre avatar</label>
        <input class="noms" type="file" id="avatar" name="avatar" >
        <span class="error"><?php if(!empty($errors['avatar'])){echo $errors['avatar'];}?></span>          
        <input class="button" type="submit" name="submitted" value="Valider">
    </form>        
 <?php } ?>


<?php
include('inc/footer.php');