<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　本の返却</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/stylereturn.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet"> 

</head>

<body>

<h2><span></span>ジーズ図書　本の返却</h2>

  <form action="returning_update.php" method="POST">
      <div class="form-group">
          <label for="isbn13">本のISBN</label>
          <p>９７８からはじまる本のバーコードを読み取ってください。</p>
          <input type="text" name="isbn13" id="isbn13">
      </div>

      <div id="attention">
          <p>期日等を確認してください</p>
          <p>延滞していた場合は、別途対応をお願いします。</p>	
          <button id="button_borrowing">返却処理します</button>
      </div>
  </form>

<div id="returning_detail">
    <div id="isbn13inputafter">
      <img id='bookcover'  >
      <div id="title"></div>
    </div>

    <div id="bookstatus"></div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>


//書影とタイトルを表示

//  ISBNを入力したら、書影を表示
$(document).ready(function() {
            $('#isbn13').on('input', function() {
                const isbn13Value = $('#isbn13').val(); // 入力された値を取得

                if (isbn13Value) { // 値が存在する場合のみ処理を実行
                    $('#bookcover').attr('src', "https://ndlsearch.ndl.go.jp/thumbnail/" + isbn13Value + ".jpg");
                } else {
                    $('#bookcover').attr('src', ""); // ISBN13が空になったら画像をクリア
                }
            });
        });        


//タイトルを表示
    $('#isbn13').on('input', function() {
        const isbn13 = $(this).val(); // 入力されたISBN13を取得
        const titleDiv = $('#title'); // 書籍タイトルを表示する要素

        // ISBNが13桁の数字であることを簡易的に確認
        // 実際にはもっと厳密なバリデーションが必要になることもあります
        if (isbn13.length === 13 && /^\d+$/.test(isbn13)) {
            $.ajax({
                url: 'fetch_book_info.php', // 後述するPHPスクリプトのパス
                type: 'GET',
                dataType: 'json',
                data: { isbn13: isbn13 }, // ISBNをクエリパラメータとして送信
                success: function(response) {
                    if (response.title) {
                        // 書籍が見つかった場合
                        titleDiv.text(response.title); // タイトルを表示
                    } else {
                        // 書籍が見つからなかった場合
                        titleDiv.text('この本は登録されていません');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Ajaxリクエストエラー:", status, error);
                    titleDiv.text('情報の取得中にエラーが発生しました');
                    bookCoverImg.attr('src', '');
                    bookCoverImg.css('display', 'none');
                }
            });
        } 
    });


//isbn13状態確認
// is_borrowed 0→貸出処理がされていません
// そうでなければ、貸し出した人（番号から名前を表示）
// 返却期日を表示
$(document).ready(function() {
    // #isbn13の入力欄で文字が入力されるたびに処理を実行
    $('#isbn13').on('input', function() {
        const isbn13 = $(this).val(); // 現在の入力値を取得

        // ISBN13は通常13桁なので、文字数が13になったら処理を実行
        if (isbn13.length === 13) {
            // 入力値が数字のみで構成されているかチェック（オプション）
            if (/^\d{13}$/.test(isbn13)) { // 13桁の数字であるか
                getBookAndUserStatus(isbn13); // 本とユーザーの状態をチェックする関数を実行
            } else {
                $('#bookstatus').empty().append('<p style="color: red;">ISBNは13桁の数字で入力してください。</p>');
            }
        } else {
            // 13桁ではない場合は表示をクリア
            $('#bookstatus').empty();
        }
    });

    function getBookAndUserStatus(isbn13) {
        // Ajaxリクエストを送信
        $.ajax({
            url: 'get_book_and_user_status.php', // サーバーサイドのスクリプトのURL
            type: 'GET', // GETまたはPOST
            data: {
                isbn13: isbn13 // サーバーに送信するデータ
            },
            dataType: 'json', // サーバーからJSON形式のデータを受け取ることを期待
            success: function(response) {
                const displayArea = $('#bookstatus');
                displayArea.empty(); // 前の表示をクリア

                if (response.error) {
                    displayArea.append(`<p style="color: red;">エラー: ${response.error}</p>`);
                    return;
                }

                if (response.status === 'not_found') {
                    displayArea.append('<p style="color: gray;">このISBNの本は見つかりませんでした。</p>');
                } else if (response.status === 'not_borrowed') {
                    displayArea.append('<p style="color: red; font-weight: bold;">貸出処理がされていません。</p>');
                } else if (response.status === 'borrowed') {
                    // 貸出中の場合
                    let htmlContent = `<p style="color: green;">貸出中です。</p>`;
                    if (response.borrower_name) {
                        htmlContent += `<p>借りているユーザー: <strong>${response.username}</strong> (${response.borrower_name})</p>`;
                    }
                    if (response.return_date) {
                        htmlContent += `<p>返却予定日: <strong>${response.return_date}</strong></p>`;
                    }
                    displayArea.append(htmlContent);
                } else {
                    displayArea.append('<p>本の状態を取得できませんでした。</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajaxリクエストエラー:', status, error);
                $('#bookstatus').empty().append('<p style="color: red;">本の状態の取得中にエラーが発生しました。</p>');
            }
        });
    }
});

</script>



</body>
</html>