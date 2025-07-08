<?php

// var_dump($_POST);
// exit();


include 'functions.php';

if (
  !isset($_POST['isbn13']) || $_POST['isbn13'] === '' ||
  !isset($_POST['title']) || $_POST['title'] === ''||
  !isset($_POST['acquired_date']) || $_POST['acquired_date'] === '' ||
  !isset($_POST['location']) || $_POST['location'] === ''
) {
  exit('paramError');
}

$isbn13 = $_POST['isbn13'];
$title = $_POST['title'];
$author = $_POST['author'];
$publisher = $_POST['publisher'];
$publication_year = $_POST['publication_year'];
$ndc10 = $_POST['ndc10'];
$detail_url = $_POST['detail_url'];
$acquired_date = $_POST['acquired_date'];
$location = $_POST['location'];
$notes = $_POST['notes'];


// DB接続

$pdo =  connect_to_db();

$sql = 'INSERT INTO booklist(isbn13, title, author, publisher, publication_year, ndc10, detail_url, acquired_date, location, notes, updated_at) 
VALUES(:isbn13, :title, :author, :publisher, :publication_year, :ndc10, :detail_url, :acquired_date, :location, :notes, now())';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':isbn13', $isbn13, PDO::PARAM_STR);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':author', $author, PDO::PARAM_STR);
$stmt->bindValue(':publisher', $publisher, PDO::PARAM_STR);
$stmt->bindValue(':publication_year', $publication_year, PDO::PARAM_STR);
$stmt->bindValue(':ndc10', $ndc10, PDO::PARAM_STR);
$stmt->bindValue(':detail_url', $detail_url, PDO::PARAM_STR);
$stmt->bindValue(':acquired_date', $acquired_date, PDO::PARAM_STR);
$stmt->bindValue(':location', $location, PDO::PARAM_STR);
$stmt->bindValue(':notes', $notes, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:register_input.php");
exit();
