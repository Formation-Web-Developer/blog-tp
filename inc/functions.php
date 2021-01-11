<?php
function debug($tableau)
{
    echo '<pre>';
    print_r($tableau);
    echo '</pre>';
}

function getAllArticlesByStatus($pdo, $visibility = 1)
{
    $sql = "SELECT * FROM articles WHERE visibility = $visibility";
    $querry = $pdo->prepare($sql);
    $querry->execute();
    return $querry->fetchAll();    
}
