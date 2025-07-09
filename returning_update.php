<?php
// 入力項目のチェック

// var_dump($_POST);
// exit;



if (!isset($_POST['isbn13']) || $_POST['isbn13'] === '' ) {
    exit('paramError');
}


$isbn13 = $_POST['isbn13'];



// DB接続

include('functions.php');
$pdo = connect_to_db();


// SQL実行


$sql = 'UPDATE booklist SET is_borrowed=0, updated_at=now() WHERE isbn13=:isbn13';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':isbn13', $isbn13, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header('Location:returning_input.php');
exit();