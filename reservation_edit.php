<?php

// var_dump($_GET);
// exit();

// id受け取り

$id = $_GET['id'];

// DB接続

include 'functions.php';

$pdo =  connect_to_db();

// SQL実行

$sql = 'SELECT * FROM booklist WHERE isbn13=:isbn13';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':isbn13', $id, PDO::PARAM_INT);
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$record = $stmt->fetch(PDO::FETCH_ASSOC);


// echo '<pre>';
// var_dump($record);
// echo '</pre>';
// exit;


?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　予約画面</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/styleregisteredit.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet"> 
</head>

<body>

<h2><span></span>ジーズ図書　予約画面</h2>
<div id="registerlistlink">
  <a href="register_read.php" target='_blank'>本の一覧画面へ</a>
</div>

  <form action="reservation_update.php" method="POST">
      <input type="hidden" name="isbn13" value="<?= $record['isbn13'] ?>" >
      <div>
        <label for="title">タイトル:</label>
        <input type="text" name="title" id="title" value="<?= $record['title'] ?>" readonly>
      </div>
      <div>
        <label for="author">著者:</label>
        <input type="text" name="author" id="author" value="<?= $record['author'] ?>" readonly>
      </div>
      <div>
        <label for="reserved_by">ユーザIDを入力してください：</label>
        <input type="text" name="reserved_by" id="reserved_by">
      </div>
      <div>
        <label for="username">予約者:</label>
        <input type="text" name="username" id="username"  readonly>
      </div>      
      <div>
        <button id="button_reserve">予約します！</button>
      </div>      
  </form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
        $(document).ready(function() {
            // #reserved_by の入力イベントを監視
            $('#reserved_by').on('input', function() {
                const userId = $(this).val(); // 入力されたユーザーIDを取得

                // 入力をAjaxリクエストを送る
                // または入力が空でなければ送る
                if (userId.length > 5) { // 空文字でないことを確認
                    $.ajax({
                        url: 'fetch_name.php', // ユーザー名を取得するためのPHPスクリプトのパス
                        type: 'GET', // GETリクエストでデータを送信
                        dataType: 'json', // サーバーからの応答をJSONとして期待
                        data: {
                            userid: userId // 'userid'という名前で入力値を送信
                        },
                        success: function(response) {
                            if (response.username) {
                                // ユーザー名が見つかった場合
                                $('#username').val(response.username);
                            } else {
                                // ユーザー名が見つからなかった場合
                                $('#username').val('ユーザーが見つかりません');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Ajaxリクエストエラー:", status, error);
                            $('#username').val('エラーが発生しました');
                        }
                    });
                } else {
                    // 入力が空になった場合、表示をリセット
                    $('#username').val('ユーザIDから自動入力されます');
                }
            });
        });
</script>
</body>
</html>