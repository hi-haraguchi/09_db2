<?php
// 入力項目のチェック

// var_dump($_POST);
// exit;



if (
    !isset($_POST['isbn13']) || $_POST['isbn13'] === '' ||
    !isset($_POST['reserved_by']) || $_POST['reserved_by'] === ''
) {
    exit('paramError');
}

$isbn13 = $_POST['isbn13'];
$reserved_by = $_POST['reserved_by'];


// DB接続

include('functions.php');
$pdo = connect_to_db();


// SQL実行


$sql = 'UPDATE booklist SET reserved_by=:reserved_by, updated_at=now() WHERE isbn13=:isbn13';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':reserved_by', $reserved_by, PDO::PARAM_STR);
$stmt->bindValue(':isbn13', $isbn13, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header('Location:register_read.php');
exit();
