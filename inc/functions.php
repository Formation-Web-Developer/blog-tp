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
    $sql = "SELECT articles.*, users.pseudo FROM articles INNER JOIN users on articles.author = users.id WHERE visibility = $visibility ORDER BY published_at DESC";
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

function checkConnection()
{
    session_start();
    $connected = isset(
        $_SESSION['user']['identifier'],
        $_SESSION['user']['username'],
        $_SESSION['user']['email'],
        $_SESSION['user']['avatar'],
        $_SESSION['user']['role'],
        $_SESSION['user']['created_at'],
        $_SESSION['user']['ip'],
        $_SESSION['user']['last_connection']
    );
    if($connected && (((time() - $_SESSION['user']['last_connection']) > 60*60*24) || $_SESSION['user']['ip'] !== $_SERVER['REMOTE_ADDR'])) {
        unset($_SESSION['user']);
    }elseif($connected) {
        $_SESSION['user']['last_connection'] = time();
    }
}

function isConnected()
{
    return !empty($_SESSION['user']);
}

function createToken($range) {
    $token = '';
    $charList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
    for($i = 0; $i < $range; $i++) {
        $token .= $charList[rand()%mb_strlen($charList)];
    }
    return $token;
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

function getCommentsByArticle($pdo, $article, $user, $isModerator)
{
    $closeWhere = $isModerator ? '' :  'and ( state=1 OR comments.user=:user )';
    $sql = 'SELECT comments.*, users.pseudo FROM comments INNER JOIN users ON users.id=comments.user WHERE article = :article '.$closeWhere;
    $query = $pdo ->prepare($sql);
    $query->bindValue(':article', $article, PDO::PARAM_INT);
    if(!$isModerator){
        $query->bindValue(':user', $user, PDO::PARAM_INT);
    }
    $query->execute();
    return $query ->fetchAll();
}

function uploadValid($errors,$key,$file,$sizeMax = 2000000,$extensions = array( '.jpg','.jpeg', '.png'),$extensionsmime = array('image/jpeg','image/png','image/jpg'), $empty = true)
{
    if(!empty($file)) {
        if ($file['error'] > 0) {
            if ($file['error'] != 4) {
                $errors[$key] = 'Problem: ' . $file['error'] . '<br />';
            }else {
                if($empty) {
                    $errors[$key] = 'Aucun fichier n\'a été téléchargé';
                }
            }
        } else {
            $file_name = $file['name']; // le nom du fichier
            $file_size = $file['size']; // la taille ( peu fiable depend du navigateur)
            $file_tmp  = $file['tmp_name'];  // le chemin du fichier temporaire
            $file_type = $file['type'];  // type MIME (peu fiable, depend du navigateur)

            // Taille du fichier
            //$sizeMax = 2000000;
            if($file_size > $sizeMax || filesize($file_tmp) > $sizeMax){ //limite le fichier a 2mo
                $errors[$key] = 'Le fichier est trop gros (max '. $sizeMax/1000000 .'mo)';
            }
            else {
                $i_point = strrpos($file_name,'.');
                $fileExtension = substr($file_name, $i_point ,strlen($file_name) - $i_point);

                if (!in_array($fileExtension, $extensions)) {
                    $errors[$key] = 'Veuillez télécharger une image de type '.implode(', ', $extensions);
                } else {

                    // alternative, sécurité +++++
                    $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                    $mime = finfo_file($finfo, $file_tmp);
                    finfo_close($finfo);

                    if (!in_array($mime, $extensionsmime)) {
                        $errors[$key] = 'Veuillez télécharger une image de type '.implode(', ', $extensions);
                    }
                }
            }
        }
    }
    return $errors;
}
