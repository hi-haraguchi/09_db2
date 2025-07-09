<?php
header('Content-Type: application/json');

include 'functions.php'; // connect_to_db() が定義されていると仮定

$pdo = connect_to_db(); // PDOオブジェクトが $pdo に代入される

// GETリクエストからisbn13を取得
$isbn13 = isset($_GET['isbn13']) ? $_GET['isbn13'] : '';

$response = [
    'status' => 'not_found'// デフォルトは「見つからない」
];

if (empty($isbn13) || !ctype_digit($isbn13) || strlen($isbn13) !== 13) {
    echo json_encode(['error' => '無効なISBN13形式です。']);
    exit;
}

try {
    // プリペアドステートメント
    // reserved_by, is_borrowed, カラムを取得
    $stmt = $pdo->prepare("SELECT reserved_by, is_borrowed  FROM booklist WHERE isbn13 = :isbn13");
    
    // パラメータのバインド
    $stmt->bindParam(':isbn13', $isbn13, PDO::PARAM_STR);
    
    // クエリの実行
    $stmt->execute();
    
    // 結果の取得
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {

        if (!empty($book['reserved_by'])) {
            $response['status'] = 'reserved'; // 予約されている
        } elseif ($book['is_borrowed'] == 1) {
            $response['status'] = 'borrowed'; // 貸出中
        } else {
            $response['status'] = 'available'; // 貸出可能
        }
    } else {
        // 本が見つからない場合は 'not_found' のまま
    }

} catch (PDOException $e) {
    // データベースエラーが発生した場合
    error_log("Database Error in check_book_status.php: " . $e->getMessage()); // エラーログに記録
    echo json_encode(['error' => 'データベースエラーが発生しました。']);
    exit;
}

echo json_encode($response);
?>