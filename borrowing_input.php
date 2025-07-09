

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　貸出</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/styleborrow.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet"> 

</head>

<body>

<h2><span></span>ジーズ図書　貸出</h2>

<form action="borrowing_update.php" method="POST">

  <div class="form-group">
      <label for="userid">ユーザID</label>
      <p>入力すると下部に名前を表示されます</p>      
      <input type="text" name="userid" id="userid">
      <div id="username"></div>
  </div>

  <div class="form-group">
        <label for="isbn13">本のISBN</label>
        <p>９７８からはじまる本のバーコードを読み取ってください。</p>
        <input type="text" name="isbn13" id="isbn13">
    <div id="isbn13inputafter">
      <img id='bookcover'  >
      <div id="title"></div>
    </div>

  </div>

<div>
  <div class="borrowing_inputdate">
        <label for="borrowed_date">貸出日 </label>
        <input type="date" name="borrowed_date" id="borrowed_date">
  </div>

  <div class="borrowing_inputdate">
        <label for="return_date">返却期日 </label>
        <input type="date" name="return_date" id="return_date">
  </div>

  <div>
        <button id="button_borrowing">貸し出します！</button>
  </div>
</div>
</form>

<div id="borrowing_detail">
  <div id="left_user">
  </div>
  <div id="right_book">
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

//  ページ開いたらカーソルが合うように
$(document).ready(function() {
  $('#userid').focus();
});


 // useridを入力したら、名前を自動取得

        $(document).ready(function() {
            // #userid の入力イベントを監視
            $('#userid').on('input', function() {
                const userId = $(this).val(); // 入力されたユーザーIDを取得

                // 入力をAjaxリクエストを送る
                // または入力が空でなければ送る
                if (userId.length > 5) { // 空文字でないことを確認
                    $.ajax({
                        url: 'fetch_name.php', // ユーザー名を取得するためのPHPスクリプトのパス
                        type: 'GET', // GETリクエストでデータを送信
                        dataType: 'json', // サーバーからの応答をJSONとして期待
                        data: {
                            userid: userId // 'userid'という名前で入力値を送信
                        },
                        success: function(response) {
                            if (response.username) {
                                // ユーザー名が見つかった場合
                                $('#username').text(response.username);
                            } else {
                                // ユーザー名が見つからなかった場合
                                $('#username').text('ユーザーが見つかりません');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Ajaxリクエストエラー:", status, error);
                            $('#username').text('エラーが発生しました');
                        }
                    });
                } else {
                    // 入力が空になった場合、表示をリセット
                    $('#username').text('ユーザIDから自動入力されます');
                }
            });
        });


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



// useridの状況確認
// is_borrowedが１で、borrower_nameがuseridに一致するレコードを探す
//そのレコードの、titleとisbn13とreturn_dateを表示

$(document).ready(function() {
    // #useridの入力欄で文字が入力されるたびに処理を実行
    $('#userid').on('input', function() {
        const userId = $(this).val(); // 現在の入力値を取得

        // 入力値が6文字になったら
        if (userId.length === 6) {
            // 文字列が英数字のみで構成されているかチェック（オプション）
            // /^[a-zA-Z0-9]+$/ は、英数字以外の文字が含まれていないかをチェックする正規表現です。
            if (/^[a-zA-Z0-9]+$/.test(userId)) {
                fetchBorrowedBooks(); // 借りている本の情報を取得する関数を実行
            } else {
                // 英数字以外の文字が含まれている場合の処理 (任意)
                // alert('ユーザーIDは英数字のみで入力してください。');
                // もし、文字数のみで発火させ、英数字チェックはサーバーサイドに任せるならこのelseは不要です。
            }
        }
    });
    function fetchBorrowedBooks() {
        const userId = $('#userid').val(); // #useridから値を取得
       // Ajaxリクエストを送信
        $.ajax({
            url: 'get_borrowed_books.php', // サーバーサイドのスクリプトのURL
            type: 'GET', // GETまたはPOST、ここではGETを使用
            data: {
                userid: userId // サーバーに送信するデータ
            },
            dataType: 'json', // サーバーからJSON形式のデータを受け取ることを期待
            success: function(response) {
                // #left_userの表示をクリア
                $('#left_user').empty();

                if (response.length > 0) {
                    // レコードが複数ある場合、それぞれのレコードに対して処理
                    $.each(response, function(index, book) {
                        const bookInfoHtml = `
                            <div>
                                <p>タイトル: ${book.title}</p>
                                <p>ISBN13: ${book.isbn13}</p>
                                <p>返却予定日: ${book.return_date}</p>
                            </div>
                            <hr> `;
                        $('#left_user').append(bookInfoHtml);
                    });
                } else {
                    $('#left_user').append('<p style="color: green; font-weight: bold;">ユーザが借りている本はありません。</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajaxリクエストエラー:', status, error);
                $('#left_user').empty().append('<p>情報を取得できませんでした。</p>');
            }
        });
    }
});




//isbn13状態確認
//reserved_byが入力されていれば→「予約されています」
// is_borrowedが１：貸出中、→「返却処理がされていません」
//「正常に返却され、予約もないので貸出できます。」

$(document).ready(function() {
    // #isbn13の入力欄で文字が入力されるたびに処理を実行
    $('#isbn13').on('input', function() {
        const isbn13 = $(this).val(); // 現在の入力値を取得

        // ISBN13は通常13桁なので、文字数が13になったら処理を実行
        if (isbn13.length === 13) {
            // 入力値が数字のみで構成されているかチェック（オプション）
            if (/^\d{13}$/.test(isbn13)) { // 13桁の数字であるか
                checkBookStatus(isbn13); // 本の状態をチェックする関数を実行
            } else {
                // 出力先を #right_book に変更
                $('#right_book').empty().append('<p style="color: red;">ISBNは13桁の数字で入力してください。</p>');
            }
        } else {
            // 13桁ではない場合は表示をクリアするか、何も表示しない
            $('#right_book').empty(); // 出力先を #right_book に変更
            // また、もし #bookcover や #title が #isbn13inputafter の中にあり、
            // それらが #right_book とは独立した要素であれば、そのクリアも検討
            // $('#bookcover').removeAttr('src');
            // $('#title').empty();
        }
    });

    function checkBookStatus(isbn13) {
        // Ajaxリクエストを送信
        $.ajax({
            url: 'check_book_status.php', // サーバーサイドのスクリプトのURL
            type: 'GET', // GETまたはPOST、ここではGETを使用
            data: {
                isbn13: isbn13 // サーバーに送信するデータ
            },
            dataType: 'json', // サーバーからJSON形式のデータを受け取ることを期待
            success: function(response) {
                // 出力先を #right_book に変更
                const displayArea = $('#right_book');
                displayArea.empty(); // 前の表示をクリア

                // サーバーからのレスポンスを元にメッセージを表示
                if (response.status) {
                    let message = '';
                    let messageColor = '';

                    switch (response.status) {
                        case 'reserved':
                            message = '予約されています';
                            messageColor = 'orange';
                            break;
                        case 'borrowed':
                            message = '返却処理がされていません';
                            messageColor = 'red';
                            break;
                        case 'available':
                            message = '正常に返却され、予約もないので貸出できます。';
                            messageColor = 'green';
                            break;
                        case 'not_found':
                            message = 'このISBNの本は見つかりませんでした。';
                            messageColor = 'gray';
                            break;
                        default:
                            message = '不明な状態です。';
                            messageColor = 'black';
                    }
                    displayArea.append(`<p style="color: ${messageColor}; font-weight: bold;">${message}</p>`);

                    
                } else if (response.error) {
                    displayArea.append(`<p style="color: red;">エラー: ${response.error}</p>`);
                } else {
                    displayArea.append('<p style="color: red;">本の状態を取得できませんでした。</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajaxリクエストエラー:', status, error);
                $('#right_book').empty().append('<p style="color: red;">本の情報を取得中にエラーが発生しました。</p>');
            }
        });
    }
});





</script>

</body>
</html>