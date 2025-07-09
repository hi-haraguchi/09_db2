<?php
header('Content-Type: application/json');

include 'functions.php'; // connect_to_db() が定義されている

$pdo = connect_to_db(); // PDOオブジェクトが $pdo に代入される

// GETリクエストからuseridを取得
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';

$books = [];

// PDOでエラーモードを設定しておくと、エラー時に例外をスローしてくれるのでデバッグしやすい
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($userid) {
    try {
        // プリペアドステートメント
        $stmt = $pdo->prepare("SELECT title, isbn13, return_date FROM booklist WHERE is_borrowed = 1 AND borrower_name = :userid");
        
        // パラメータのバインド (PDO形式)
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        
        // クエリの実行
        $stmt->execute();
        
        // 結果の取得 (PDO形式)
        // fetchAll(PDO::FETCH_ASSOC) で連想配列の配列として全て取得
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // データベースエラーが発生した場合
        // デバッグ時はエラーメッセージを表示しても良いが、本番環境では具体的なメッセージを避けるべき
        error_log("Database Error: " . $e->getMessage()); // エラーログに記録
        echo json_encode(['error' => 'データベースエラーが発生しました。']);
        exit; // エラーなのでここで終了
    }
}

// PDOは接続を明示的に閉じる必要はありません（スクリプト終了時に自動的に閉じられます）
// $pdo = null; // 必要であれば、リソース解放のために明示的にnullを代入することも可能

echo json_encode($books);
?>