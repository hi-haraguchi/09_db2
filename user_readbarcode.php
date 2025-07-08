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
        <canvas id='{$record["userid"]}'></canvas>
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
  <link rel="stylesheet" href="css/styleuserlist2.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet">   
</head>

<body>

<h2><span></span>ジーズ図書　ユーザ一覧（バーコード用）</h2>

<div id="userinputlink">
  <a href="user_input.php" target='_blank'>ユーザの登録画面へ</a>
</div>


    <table>
      <thead>
        <tr>
          <th>ユーザID</th>
          <th>名前</th>
          <th>バーコード</th>
        </tr>
      </thead>
      <tbody>
        <?= $output ?>
      </tbody>
    </table>
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
$(document).ready(function() {
  // 各ユーザーIDに対応するバーコードを生成
  $("canvas[id]").each(function() {
    var userId = $(this).attr("id"); // tdのid属性からユーザーIDを取得

    // JsBarcodeを使ってバーコードを生成
    // displayValue: false にすると、バーコードの下に数字が表示されなくなります。
    // その他のオプションについてはJsBarcodeの公式ドキュメントを参照してください。
    JsBarcode("#" + userId, userId, {
      format: "CODE128", // 例: CODE128フォーマット
      width: 2,
      height: 50,
      displayValue: true // バーコードの下に値を表示するか
    });
  });
});    
</script>
</body>

</html>