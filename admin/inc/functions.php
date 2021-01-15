<?php


function debug(array $array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function getArticles(PDO $pdo)
{
    $query = $pdo -> prepare('SELECT articles.*, users.pseudo FROM articles INNER JOIN users ON users.id = articles.author ORDER BY created_at DESC');
    $query->execute();
    return $query->fetchAll();
}

function newArticle(PDO $pdo, int $author, string $title, string $description, string $content)
{
    $query = $pdo -> prepare('INSERT INTO articles (title,author,description,content,created_at) VALUES (:title, :author, :description, :content, NOW())');
    $query->bindValue(':title', $title);
    $query->bindValue(':author', $author, PDO::PARAM_INT);
    $query->bindValue(':description', $description);
    $query->bindValue(':content', $content);
    $query->execute();
}

function deleteArticle(PDO $pdo, int $id): bool
{
    $query = $pdo -> prepare('DELETE FROM articles WHERE id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    return $query->execute();
}

function getArticleById(PDO  $pdo, int $id): array
{
    $query = $pdo->prepare('SELECT articles.*, users.pseudo FROM articles INNER JOIN users ON users.id = articles.author WHERE articles.id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
}

function editArticle(PDO $pdo, int $id, string $title, string $description, string $content, int $visibility, bool $published): bool
{
    $sql = 'UPDATE articles SET title=:title,description=:description,content=:content,visibility=:visibility,modified_at=NOW()';
    if($published) {
        $sql.=',published_at=NOW()';
    }
    $query = $pdo->prepare($sql.' WHERE id=:id');
    $query->bindValue(':title', $title);
    $query->bindValue(':description', $description);
    $query->bindValue(':content', $content);
    $query->bindValue(':visibility', $visibility, PDO::PARAM_INT);
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    return $query->execute();
}

function getWaitingComments(PDO $pdo): array {
    $query = $pdo->prepare('SELECT comments.*,users.pseudo,articles.title FROM comments INNER JOIN users ON users.id = comments.user INNER JOIN articles ON articles.id = comments.article WHERE state=0 ORDER BY created_at ASC');
    $query->execute();
    return $query->fetchAll();
}

function getValueByArray(array $array, string $key, string $defaultValue = '') {
    return isset($array[$key]) ? $array[$key] : $defaultValue;
}

function secureTextByArray(array $array, string $key): string
{
    return secureText(getValueByArray($array, $key));
}

function secureText(string $text): string
{
    return trim(strip_tags($text));
}

function checkLengthTextValid(string $text, int $min, int $max, array &$errors, string $key): void
{
    if( empty($text) ) {
        $errors[$key] = 'Veuillez renseigner ce champs !';
    } elseif (mb_strlen($text) < $min ) {
        $errors[$key] = 'Ce champs ne contient pas assez de caractère !';
    } elseif (mb_strlen($text) > $max) {
        $errors[$key] = 'Ce champs contient trop de caractère !';
    }
}

function checkNumericValid($number, array &$errors, string $key, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX)
{
    if( empty($number) ) {
        $errors[$key] = 'Veuillez renseigner ce champs !';
    }elseif ( !filter_var($number, FILTER_VALIDATE_INT) ) {
        $errors[$key] = 'La valeur saisie n\'est pas un nombre valide !';
    }elseif (intval($number) < $min) {
        $errors[$key] = 'La valeur saisie est trop petite !';
    }elseif (intval($number) > $max) {
        $errors[$key] = 'La valeur saisie est trop grande !';
    }
}

function checkEmailValid(string $text, array &$errors, string $key): void
{
    if(empty($text)){
        $errors[$key] = 'Veuillez renseigner ce champs !';
    } elseif(!filter_var($text, FILTER_VALIDATE_EMAIL)) {
        $errors[$key] = 'Ceci n\'est pas un email valide !';
    }
}

function checkValueSelect(array $array, string $value, array &$errors, string $key): void
{
    if((empty($value) && !is_numeric($value)) || empty($array[$value])) {
        $errors[$key] = 'Veuillez renseigner ce champs !';
    }
}

function buildInputByArray(array $array, string $label, string $type, string $id, array $errors) {
    buildInput((!empty($array[$id]) ? $array[$id] : ''), $label, $type, $id, $errors);
}

function buildInput(string $text, string $label, string $type, string $id, array $errors){?>
    <div class="form-group">
        <label for="<?=$id?>"><?=$label?></label>
        <input type="<?=$type?>" name="<?=$id?>" id="<?=$id?>"  value="<?=$text?>"<?= !empty($errors[$id]) ? ' class="error"' : '' ?>>
        <span class="error"><?= !empty($errors[$id]) ? $errors[$id] : '' ?></span>
    </div>
<?php }

function buildTextAreaByArray(array $array, string $label, string $id, int $rows, array $errors){
    buildTextArea(getValueByArray($array, $id), $label, $id, $rows, $errors);
}

function buildTextArea(string $text, string $label, string $id, int $rows, array $errors){ ?>
    <div class="form-group">
        <label for="<?=$id?>"><?=$label?></label>
        <textarea name="<?=$id?>" id="<?=$id?>" rows="<?=$rows?>"<?= !empty($errors[$id]) ? ' class="error"' : '' ?>><?=$text?></textarea>
        <span class="error"><?= !empty($errors[$id]) ? $errors[$id] : '' ?></span>
    </div>
<?php }

function buildSelect(string $label, string $id, array $values, $defaultValue, array $errors) { ?>
    <div class="form-group">
        <label for="<?=$id?>"><?=$label?></label>
        <select name="<?=$id?>" id="<?=$id?>"<?= !empty($errors[$id]) ? ' class="error"' : '' ?>>
            <?php foreach ($values as $key => $value): ?>
                <option value="<?=$key?>"<?= $key == $defaultValue ? ' selected' : '' ?>><?=$value?></option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?= !empty($errors[$id]) ? $errors[$id] : '' ?></span>
    </div>
<?php }

function buildPagination($count, $currentPage, $limit, $range = 3, $url='/?') {
    if($count <= $limit) { return; }
    $maxPage = ceil($count/$limit); ?>
    <div class="pagination">
        <?php
        for ($i = 1; ($maxPage > ($range*2) && $i <= $range) || ($maxPage <= ($range*2) && $i <= $maxPage); $i++){
            buildButtonPage($url.'page='.$i, $i, $i == $currentPage);
        }
        if($maxPage > $range) {
            if($currentPage >= ($range*2)) { ?>
                <span class="pagination-split">...</span>
            <?php }
            for($i = $maxPage-($range-1); $i < $currentPage-(($range-1)/2); $i++){
                buildButtonPage($url.'page='.$i, $i, false);
            }
            for($i = $currentPage - (($range-1)/2); $i <= $currentPage+(($range-1)/2); $i++) {
                if($i > $range && $i <= $maxPage) {
                    buildButtonPage($url.'page='.$i, $i, $i == $currentPage);
                }
            }
            if($maxPage-($range-1) > $currentPage+((($range-1)/2)+1)) { ?>
                <span class="pagination-split">...</span>
            <?php }
            for($i = $maxPage-($range-1); $i <= $maxPage; $i++) {
                if($i > $currentPage+(($range-1)/2)) {
                    buildButtonPage($url.'page='.$i, $i, $i == $currentPage);
                }
            }
        } ?>
    </div>
<?php }

function buildButtonPage($url, $index, $selected) { ?>
    <a href="<?=$url?>"<?=$selected ? 'class="active"' : ''?>><?=$index?></a>
<?php }

function getUser(PDO $pdo, string $email, string $password)
{
    $query = $pdo->prepare('SELECT * FROM users WHERE email=:email');
    $query->bindValue(':email', $email);
    $query->execute();
    $user = $query ->fetch();
    if($user != null) {
        if(!password_verify($password, $user['password'])){ $user = null; }
        else {
            $user = [
                'identifier' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'avatar' => $user['avatar'],
                'role' => $user['role'],
                'created_at' => strtotime($user['created_at']),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'last_connection' => time()
            ];
        }
    }
    return $user;
}

function getUserById(PDO $pdo, int $id) {
    $query = $pdo->prepare('SELECT * FROM users WHERE id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query ->fetch();
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

function isConnected(): bool
{
    return !empty($_SESSION['user']);
}

function passwordVerify($password, $hash): bool
{
    return password_verify($password, $hash);
}

function validComment(PDO $pdo, int $id)
{
    $query = $pdo->prepare('UPDATE comments SET state=1 WHERE id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
}

function deleteComment(PDO $pdo, int $id)
{
    $query = $pdo->prepare('DELETE FROM comments WHERE id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
}

function getCommentsByArticle(PDO $pdo, int $id): array
{
    $query = $pdo->prepare('SELECT comments.*, users.pseudo FROM comments INNER JOIN users ON users.id=comments.user WHERE comments.article=:id ORDER BY comments.created_at DESC');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll();
}

function getUsers(PDO $pdo): array
{
    $query = $pdo->prepare("SELECT * FROM users ORDER BY role DESC");
    $query->execute();
    return $query->fetchAll();
}

function changeRoleByUser(PDO $pdo, int $id, string $role)
{
    $query = $pdo->prepare('UPDATE users SET role=:role WHERE id=:id');
    $query->bindValue(':role', $role);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
}
