<?php
require 'inc/roles.php';

function debug($tableau)
{
    echo '<pre>';
    print_r($tableau);
    echo '</pre>';
}

function getAllArticlesByStatus($pdo, $visibility = 1)
{
    $sql = "SELECT * FROM articles WHERE visibility = $visibility ORDER BY published_at DESC";
    $querry = $pdo->prepare($sql);
    $querry->execute();
    return $querry->fetchAll();
}
function mistake($text, $min, $max, $key, $errors)
{
    if(!empty($text)){
        if(mb_strlen($text) < $min){
            $errors[$key] = 'Nombre min de caractères est '.$min;
        }else if(mb_strlen($text) > $max){
            $errors[$key] = 'Nombre max de caractères est '.$max;
        }
    }else{
    $errors[$key] = 'Veuillez renseigner ce champ';
    }
    return $errors;
}
function checkPass($errors, $password, $confirmPass, $key){
    if($password !=$confirmPass){
        $errors[$key] = 'les mots de passe ne sont pas identiques';
    }
    return $errors;
}
function isConnected(){
    return isset($_SESSION['id']);
}

function createToken($range) {
    $token = '';
    $charList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
    for($i = 0; $i < $range; $i++) {
        $token .= $charList[rand()%mb_strlen($charList)];
    }
    return $token;
}

function getUser(PDO $pdo, string $email, string $password)
{
    $query = $pdo->prepare('SELECT * FROM users WHERE email=:email AND password=:password');
    $query->bindValue(':email', $email);
    $query->bindValue(':password', $password);
    $query->execute();
    return $query->fetch();
}

function passwordHash(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2I, [
        'cost' => 12
    ]);
}
function getUserById(PDO $pdo, int $id) {
    $query = $pdo->prepare('SELECT * FROM users WHERE id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query ->fetch();
}

function getCommentsByArticle($pdo, $article)
{
$sql = 'select * from comments where article = :article and state = 1';
$query = $pdo ->prepare($sql);
$query->bindValue(':article', $article, PDO::PARAM_INT);
$query->execute();
return $query ->fetchAll();
}
