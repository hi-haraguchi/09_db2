<?php

include 'functions.php';

$pdo =  connect_to_db();

$username = null; // デフォルトはnull


    // GETリクエストからユーザーIDを取得
    $userId = $_GET['userid'] ?? ''; // null合体演算子で未定義の場合に備える

    if (!empty($userId)) {
        // SQLクエリを準備
        // userlistテーブルのuseridカラムとusernameカラムを使用
        $sql = "SELECT username FROM userlist WHERE userid = :userid LIMIT 1";
        $stmt = $pdo->prepare($sql);

        // 値をバインド
        $stmt->bindValue(':userid', $userId, PDO::PARAM_STR);

        // クエリを実行
        $stmt->execute();

        // 結果を取得
        $record = $stmt->fetch();

        if ($record) {
            $username = $record['username'];
        }
    }


// 結果をJSON形式で出力
header('Content-Type: application/json');
echo json_encode(['username' => $username]);