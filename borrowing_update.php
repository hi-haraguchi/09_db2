<?php
// 入力項目のチェック

// var_dump($_POST);
// exit;



if (
    !isset($_POST['userid']) || $_POST['userid'] === '' ||
    !isset($_POST['isbn13']) || $_POST['isbn13'] === '' ||
    !isset($_POST['borrowed_date']) || $_POST['borrowed_date'] === '' ||
    !isset($_POST['return_date']) || $_POST['return_date'] === ''
) {
    exit('paramError');
}

$borrower_name = $_POST['userid'];
$isbn13 = $_POST['isbn13'];
$borrowed_date = $_POST['borrowed_date'];
$return_date = $_POST['return_date'];


// DB接続

include('functions.php');
$pdo = connect_to_db();


// SQL実行


$sql = 'UPDATE booklist SET is_borrowed=1, borrower_name=:borrower_name, borrowed_date=:borrowed_date, return_date=:return_date, reserved_by=NULL, updated_at=now() WHERE isbn13=:isbn13';


//ほかにも予約はリセットして、貸出状態を１へ


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':borrower_name', $borrower_name, PDO::PARAM_STR);
$stmt->bindValue(':borrowed_date', $borrowed_date, PDO::PARAM_STR);
$stmt->bindValue(':return_date', $return_date, PDO::PARAM_STR);
$stmt->bindValue(':isbn13', $isbn13, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header('Location:borrowing_input.php');
exit();
