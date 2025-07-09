<?php

include 'functions.php';

$pdo =  connect_to_db();

// クライアントからのGETリクエストでISBN13を取得
$isbn13 = $_GET['isbn13'] ?? ''; // PHP 7.0以降のNull合体演算子

// ISBN13が空でなければ処理を続行
if (!empty($isbn13)) {
    try {

        // SQLクエリを準備
        $stmt = $pdo->prepare("SELECT title FROM booklist WHERE isbn13 = :isbn13");
        // パラメータをバインド
        $stmt->bindValue(':isbn13', $isbn13, PDO::PARAM_STR);
        // クエリを実行
        $stmt->execute();

        // 結果を取得
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        // 結果をJSON形式で出力
        header('Content-Type: application/json');
        if ($record) {
            echo json_encode(['title' => $record['title'], 'isbn13' => $isbn13]);
        } else {
            echo json_encode(['title' => null]); // 見つからなかった場合はnullを返す
        }

    } catch (PDOException $e) {
        // データベースエラーが発生した場合
        header('Content-Type: application/json', true, 500); // HTTP 500 Internal Server Error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // ISBN13が提供されていない場合
    header('Content-Type: application/json', true, 400); // HTTP 400 Bad Request
    echo json_encode(['error' => 'ISBN13 is required.']);
}
?>