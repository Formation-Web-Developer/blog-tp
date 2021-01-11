<?php
    if( !isset($title) ) { $title = 'Administration - MonBlog.fr'; }
    if( !isset($urlBase) ) { $urlBase = ''; }
?>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="<?=$urlBase?>assets/css/style.css">
</head>
<body>
    <header class="header">
        <nav>
            <ul class="menu">
                <li class="menu-item"><a href="/"><i class="fas fa-arrow-left"></i> Revenir sur le site</a></li>
            </ul>
        </nav>
        <h1 class="title"><a href="index.php">Administration de MonBlog.fr</a></h1>
    </header>
