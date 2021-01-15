<?php
require('inc/functions.php');
checkConnection();

if(isConnected()){
    header('Location: profil.php');
    exit;
}
 if(empty($_GET['id']) || empty($_GET['type']) || empty($_GET['token']) || !is_numeric($_GET['id'])){
    header('Location: index.php');
    exit;
 }
 if($_GET['type'] !== 'validate'){
    header('Location: index.php');
    exit;
 }
 require('inc/database.php');
 $sql = 'SELECT COUNT(*) FROM users WHERE id = :id AND token = :token';
$query = $pdo -> prepare($sql);
$query -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$query -> bindValue(':token', $_GET['token'], PDO::PARAM_STR);
$query-> execute();
if($query-> fetchColumn() !== 1){
    header('Location: index.php');
    exit;
}
$sql = 'UPDATE users SET token = NULL WHERE id = :id';
$query = $pdo -> prepare($sql);
$query -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$query-> execute();

include('inc/header.php'); ?>
<p class="success">Votre compte a été validé avec success! <a href="login.php">Se connecter</a> </p>

<?php
include('inc/footer.php');