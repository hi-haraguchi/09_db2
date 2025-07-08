<?php
// データ受け取り

// var_dump($_GET);
// exit;


$userid = $_GET['id'];

// DB接続

include('functions.php');
$pdo = connect_to_db();

// SQL実行

$sql = 'DELETE FROM SET WHERE userid=:userid';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userid', $userid, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header("Location:user_read.php");
exit();
