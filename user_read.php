<?php

include 'functions.php';

$pdo =  connect_to_db();

$sql = 'SELECT * FROM userlist ORDER BY userid ASC';

$stmt = $pdo->prepare($sql);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$output = "";
foreach ($result as $record) {
  $output .= "
    <tr>
      <td>{$record["userid"]}</td>
      <td>{$record["username"]}</td>
      <td>
        <a href='user_delete.php?id={$record["userid"]}'>delete</a>
      </td>
    </tr>
  ";
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　ユーザ一覧</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/styleuserlist1.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet">   
</head>

<body>

<h2><span></span>ジーズ図書　ユーザ一覧</h2>

<div id="userinputlink">
  <a href="user_input.php" target='_blank'>ユーザの登録画面へ</a>
</div>


    <table>
      <thead>
        <tr>
          <th>ユーザID</th>
          <th>名前</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?= $output ?>
      </tbody>
    </table>
</body>

</html>