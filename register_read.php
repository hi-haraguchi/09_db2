<?php

include 'functions.php';

$pdo =  connect_to_db();

$sql = 'SELECT * FROM booklist ORDER BY title ASC';

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

// 貸出状況のテキストを格納する変数
    $loan_status_text = "";

    // is_borrowed の値に基づいて貸出状況を判定
    if ($record["is_borrowed"] == 0) {
        $loan_status_text = "校舎にあります";
    } else {
        // is_borrowed が1の場合
        // return_date が存在するかどうかを確認し、表示を調整
        if (!empty($record["return_date"])) {
            // return_date が 'YYYY-MM-DD' 形式の場合、必要に応じてフォーマット
            $return_date_formatted = htmlspecialchars($record["return_date"]);
            $loan_status_text = "{$return_date_formatted}までに返却予定";
        } 
    }

    // 予約状況の確認
    $reservation_status_text = "";
    if ($record["reserved_by"] === null) { // reserved_byがNULLの場合
        $reservation_status_text = "なし";
    } else { // reserved_byに何らかの値が入っている場合
        $reservation_status_text = "予約中";
    }

    // URLの構築
    $thumbnail_url = "https://ndlsearch.ndl.go.jp/thumbnail/" . htmlspecialchars($record["isbn13"]) . ".jpg";
    $detail_link = htmlspecialchars($record["detail_url"]); // URLは必ずエスケープする

    $output .= "
    <div class='eachbook'>
        <div class='eachbookcover'>
            <img src='{$thumbnail_url}' alt='{$record["title"]}の表紙'>
        </div>
        <div class='eachbookright'>
            <p>タイトル：" . htmlspecialchars($record["title"]) . "</p>
            <p>　著者名：" . htmlspecialchars($record["author"]) . "</p>
            <p>　出版社：" . htmlspecialchars($record["publisher"]) . "</p>
            <p>出版時期：" .htmlspecialchars($record["publication_year"]) . "</p>
            <p class='eachbookurl'><a href='{$detail_link}' target='_blank'>詳細を見る</a></p>
            <p>所蔵場所：" . htmlspecialchars($record["location"]) . "</p>
            <p>貸出状況：" . $loan_status_text . "</p>
            <p>予約状況：" . $reservation_status_text . "</p>
            <p class='eachbookurl'><a href='reservation_edit.php?id={$record["isbn13"]}'>予約する</a></p>
        </div>
    </div>
    ";
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ジーズ図書　本の一覧</title>
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/styleregisterlist.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet"> 
</head>
<body>

<h2><span></span>ジーズ図書　本の一覧</h2>
<div id="registerinputlink">
    <a href="register_input.php" target='_blank'>本の登録画面へ</a>
</div>


<div id="booklist-container">
<?= $output ?>
</div>



</body>

</html>