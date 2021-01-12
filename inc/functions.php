<?php
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

function createToken($range) {
    $token = '';
    $charList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
    for($i = 0; $i < $range; $i++) {
        $token .= $charList[rand()%mb_strlen($charList)];
    }
    return $token;
}

