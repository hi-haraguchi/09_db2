<?php
header('Content-Type: application/json');

include 'functions.php'; // connect_to_db() が定義されていると仮定

$pdo = connect_to_db(); // PDOオブジェクトが $pdo に代入される

$isbn13 = isset($_GET['isbn13']) ? $_GET['isbn13'] : '';

$response = [
    'is_reserved' => false, // デフォルトは予約されていない
    'error' => null
];

// ISBN13のバリデーション
if (empty($isbn13) || !ctype_digit($isbn13) || strlen($isbn13) !== 13) {
    $response['error'] = '無効なISBN13形式です。';
    echo json_encode($response);
    exit;
}

try {
    // booklistテーブルからreserved_byカラムを取得
    $stmt = $pdo->prepare("SELECT reserved_by FROM booklist WHERE isbn13 = :isbn13");
    $stmt->bindParam(':isbn13', $isbn13, PDO::PARAM_STR);
    $stmt->execute();
    
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // reserved_by が NULL でなければ予約されていると判断
        if ($book['reserved_by'] !== null) { // もしくは !empty($book['reserved_by'])
            $response['is_reserved'] = true;
        }
    } else {
        // 本が見つからない場合も予約はされていない
        // $response['error'] = '指定されたISBNの本は見つかりませんでした。'; // 必要であればエラーメッセージを追加
    }

} catch (PDOException $e) {
    error_log("Database Error in check_reservation_status.php: " . $e->getMessage());
    $response['error'] = 'データベースエラーが発生しました。';
}

echo json_encode($response);
?>