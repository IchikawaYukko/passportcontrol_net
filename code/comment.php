<?php
$pdo;
function init() {
    global $pdo;

    $dbhost = 'postgis';
    $dsn = "pgsql:dbname=passportctl host=$dbhost port=5432";
    $dbuser = 'postgres';
    $dbpass ='';

    try {
        $pdo = new PDO($dsn, $dbuser, $dbpass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true
            ]
        );
    } catch(PDOException $e) {
        header('Content-Type: text/plain; charset=UTF-8', true, '500');
        echo $e->getMessage();
        die();
    }
}

function read($border_id) {
    global $pdo;

    $sql =  'SELECT comment, to_char(postdate, \'YYYY Mon DD HH24:MI:SS\') AS postdate, displayname FROM comments,users '.
            'WHERE comments.userid = users.id AND border = :id ORDER BY postdate DESC';
    $prepare = $pdo->prepare($sql);
    $prepare->bindValue(':id', (int) $border_id, PDO::PARAM_INT);
    $prepare->execute();
    $result = $prepare->fetchAll();

    if(count($result) === 0) {
        echo 'no comment here';
    } else {
        foreach ($result as $comment) {

            echo '<div>'. h($comment['displayname']) . ' ' . h($comment['postdate']) . '<br>' . h($comment['comment']) .'</div>';
        }
    }
}

function write($border_id, $user_id, $comment) {
    global $pdo;

    $sql =
        'INSERT INTO comments (border, userid, comment) VALUES(:border_id, :user_id, :comment)';
    $prepare = $pdo->prepare($sql);
    $prepare->bindValue(':border_id', (int) $border_id, PDO::PARAM_INT);
    $prepare->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
    $prepare->bindValue(':comment', $comment, PDO::PARAM_STR);

    $pdo->beginTransaction();
    is_inTransaction($pdo);

    try {
        $prepare->execute();
        is_inTransaction($pdo);
        $pdo->commit();
    } catch(PDOException $e) {
        is_inTransaction($pdo);
        $pdo->rollback();
        header('Content-Type: text/plain; charset=UTF-8', true, '500');
        echo $e->getMessage();
        die();
    }
}

function is_inTransaction($pdo) {
    if(!$pdo->inTransaction()) {
        log::write('Not in transaction!?');
        throw new Exception();
    }
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

init();
if(count($_POST) === 0) {
    read(filter_input(INPUT_GET, 'border'));
} else {
    write(filter_input(INPUT_POST, 'border'), 1, filter_input(INPUT_POST, 'comment')); // 1 is anonymous
}