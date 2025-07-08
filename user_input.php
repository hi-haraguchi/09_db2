<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　ユーザ登録</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/styleuser.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet"> 
</head>

<body>
<h2><span></span>ジーズ図書　ユーザの登録</h2>

<div id="userlistlink">
  <a href="user_read.php" target='_blank'>ユーザの一覧画面へ</a>
</div>

<div>
  <p class="explanation">ユーザIDと名前を入力して登録をお願いします。</p>
  <p class="explanation">ユーザIDは頭文字　f　に加えて５桁の数字を想定しています。</p>
  <p class="explanation">最初の１桁：DEV→１、LAB→２、スタッフ→３</p>
  <p class="explanation">次の２桁：〇〇期</p>
  <p class="explanation">最後の２桁：出席番号など</p>
</div>  

<form action="user_create.php" method="POST">
      <div>
        <label for="userid">ユーザID:</label>
        <input type="text" name="userid" id="userid">
      </div>
      <div>
        <label for="username">名前:</label>
        <input type="text" name="username" id="username">
      </div>
      <div>
        <button id="button_user">登録します！</button>
      </div>
  </form>

</body>

</html>