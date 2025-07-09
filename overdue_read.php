<?php

include 'functions.php';

$pdo = connect_to_db();

// 今日の日付を取得 (YYYY-MM-DD 形式)
$today = date('Y-m-d');

// SQLクエリの準備
// booklistとuserlistを結合し、条件に合うレコードを検索
$sql = "
    SELECT
        b.title,
        b.return_date,
        u.username

    FROM
        booklist AS b
    JOIN
        userlist AS u ON b.borrower_name = u.userid
    WHERE
        b.is_borrowed = 1 AND b.return_date < :today
    ORDER BY
        b.return_date ASC /* 返却予定日が古い順に並べ替え (任意) */
";

// プリペアドステートメントの作成と実行
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':today', $today, PDO::PARAM_STR); // 今日の日付をバインド
$stmt->execute();

// 結果の取得
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// HTML出力の準備
$output = "";

if (count($records) > 0) {
    // レコードがある場合、テーブルヘッダーを追加
    $output .= "
        <table border='1'>
            <thead>
                <tr>
                    <th>ユーザー名</th>
                    <th>本のタイトル</th>
                    <th>返却予定日</th>
                </tr>
            </thead>
            <tbody>
    ";

    // 各レコードをループ処理してHTMLを生成
    foreach ($records as $record) {
        $output .= "
            <tr>
                <td>{$record["username"]}</td>
                <td>{$record["title"]}</td>
                <td>{$record["return_date"]}</td>
            </tr>
        ";
    }

    // テーブルフッターを追加
    $output .= "
            </tbody>
        </table>
    ";
} else {
    // 該当するレコードがない場合
    $output = "<p>延滞している本はありません。</p>";
}

// データベース接続を閉じる (PDOでは通常不要ですが、明示的にnullを代入することも可能)
$pdo = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　延滞している人の一覧</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/styleuserlist3.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet">   
</head>

<body>

<h2><span></span>ジーズ図書　延滞している人の一覧</h2>

<div id="userinputlink">
  <a href="index.html" target='_blank'>ホーム画面へ</a>
</div>

<?= $output ?>

</body>

</html>