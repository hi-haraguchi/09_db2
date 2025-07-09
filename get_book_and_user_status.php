<?php
header('Content-Type: application/json');

include 'functions.php'; // connect_to_db() が定義されていると仮定

$pdo = connect_to_db(); // PDOオブジェクトが $pdo に代入される

// GETリクエストからisbn13を取得
$isbn13 = isset($_GET['isbn13']) ? $_GET['isbn13'] : '';

// デフォルトのレスポンス
$response = [
    'status' => 'not_found',
    'borrower_name' => null,
    'username' => null,
    'return_date' => null
];

// 入力値のバリデーション
if (empty($isbn13) || !ctype_digit($isbn13) || strlen($isbn13) !== 13) {
    echo json_encode(['error' => '無効なISBN13形式です。']);
    exit;
}

try {
    // booklistとuserlistを結合して情報を取得
    // LEFT JOIN を使うことで、booklistにレコードがあってもuserlistに一致するユーザーがいなくてもエラーにならない
    $stmt = $pdo->prepare("
        SELECT 
            b.is_borrowed, 
            b.borrower_name, 
            b.return_date,
            u.username
        FROM 
            booklist AS b
        LEFT JOIN 
            userlist AS u ON b.borrower_name = u.userid
        WHERE 
            b.isbn13 = :isbn13
    ");
    
    $stmt->bindParam(':isbn13', $isbn13, PDO::PARAM_STR);
    $stmt->execute();
    
    $book_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book_data) {
        // 本が見つかった場合
        if ($book_data['is_borrowed'] == 0) {
            $response['status'] = 'not_borrowed'; // 貸出処理がされていない
        } else {
            $response['status'] = 'borrowed'; // 貸出中
            $response['borrower_name'] = $book_data['borrower_name'];
            $response['username'] = $book_data['username'];
            $response['return_date'] = $book_data['return_date'];
        }
    } else {
        // 本が見つからない場合は 'not_found' のまま
    }

} catch (PDOException $e) {
    error_log("Database Error in get_book_and_user_status.php: " . $e->getMessage());
    echo json_encode(['error' => 'データベースエラーが発生しました。']);
    exit;
}

echo json_encode($response);
?>