<?php

include 'functions.php';

if (
  !isset($_POST['userid']) || $_POST['userid'] === '' ||
  !isset($_POST['username']) || $_POST['username'] === ''
) {
  exit('paramError');
}

$todo = $_POST['userid'];
$deadline = $_POST['username'];

// DB接続

$pdo =  connect_to_db();

$sql = 'INSERT INTO userlist(userid, username, created_at) VALUES(:userid, :username, now())';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userid', $todo, PDO::PARAM_STR);
$stmt->bindValue(':username', $deadline, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:user_input.php");
exit();
