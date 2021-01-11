<?php

function debug(array $array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function getArticles(PDO $pdo)
{
    $query = $pdo -> prepare('SELECT * FROM articles ORDER BY created_at DESC');
    $query->execute();
    return $query->fetchAll();
}
