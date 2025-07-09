<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ジーズ図書　本の新規登録</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/styleregister.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap" rel="stylesheet"> 
</head>

<body>

<h2><span></span>ジーズ図書　本の新規登録</h2>

<div id="registerlistlink">
  <a href="register_read.php" target='_blank'>本の一覧画面へ</a>
</div>

<div>
  <label for="isbn13input">本のバーコードを読み込んでください: </label><input type="text" inputmode="numeric" pattern="^[0-9]+$"  id="isbn13input">
  <button id="display">表示します</button>
  <p class="explanation">読み取るバーコードは９７８からはじまるISBNです。</p>
  <p class="explanation">バーコードの情報を利用して、国会図書館などから書籍情報を取得します。</p>
  <p class="explanation">所蔵日・所蔵場所・備考を入力して、下のボタンから登録してください。 </p>
</div>  


<div id="input-detail">
  <div id="book_calligraphy">
    <img id="bookcover" >
  </div>

  <form action="register_create.php" method="POST" >
  <div id="form-inner">
    <div id="form-inner_left">
      <div>
        <!-- <label for="isbn13">タイトル:</label> -->
        <input type="hidden" name="isbn13" id="isbn13">
      </div>
      <div>
        <label for="title">タイトル:</label>
        <input type="text" name="title" id="title" readonly>
      </div>
      <div>
        <label for="author">著者:</label>
        <input type="text" name="author" id="author" readonly>
      </div>
      <div>
        <label for="publisher">出版社:</label>
        <input type="text" name="publisher" id="publisher" readonly>
      </div>
      <div>
        <label for="publication_year">出版時期:</label>
        <input type="text" name="publication_year" id="publication_year" readonly>
      </div>
      <div>
        <label for="ndc10">NDC:</label>
        <input type="text" name="ndc10" id="ndc10" readonly>
      </div>                        
      <div>
        <!-- <label for="detail_url">URL:</label> -->
        <input type="hidden" name="detail_url" id="detail_url" readonly> <br> <a id="link_display" href="#" target="_blank"></a> 
      </div>
    </div>

    <div id="inputplus">
      <div>
        <label for="acquired_date">所蔵した日: </label>
        <input type="date" name="acquired_date" id="acquired_date">
      </div>
      <div>    
        <label for="location">所蔵場所: </label>
        <select name="location" id="location">
          <option value="">選択してください</option>        
          <option value="入口側の棚">入口側の棚</option>
          <option value="教室側の棚">教室側の棚</option>
          <option value="机上のラック">机上のラック</option>
        </select>
      </div>
      <div>
        <label for="notes">備考:</label><br>
        <textarea id="notes" name="notes" rows="5" cols="40"></textarea>
      </div>
      <div>
          <button id="button_register">登録します！</button>
      </div>
    </div>
  </div>
  </form>
</div>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- axiosを使えるようにする -->
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
//  ページ開いたらカーソルが合うように
$(document).ready(function() {
  $('#isbn13input').focus();
});

//  ISBNを入力したら、書影を表示
$(document).ready(function() {
            $('#display').on('click', function() {
                const isbn13Value = $('#isbn13input').val(); // 入力された値を取得

                if (isbn13Value) { // 値が存在する場合のみ処理を実行
                    $('#bookcover').attr('src', "https://ndlsearch.ndl.go.jp/thumbnail/" + isbn13Value + ".jpg");
                } else {
                    $('#bookcover').attr('src', ""); // ISBN13が空になったら画像をクリア
                }
            });
        });

  // ISBNを入力したら、各情報を表示


$('#display').on('click', function() {
    const isbn13Value2 = $('#isbn13input').val(); // 入力された値を取得







    // 入力値が空でない場合にのみAPIを叩くなどのチェックを入れるとより良いでしょう
    if (isbn13Value2) { 
              const url = "https://ndlsearch.ndl.go.jp/api/opensearch?isbn=" + isbn13Value2;

          axios
            .get(url)
            .then(function (response) {
              const parser = new DOMParser();
              const xmlDoc = parser.parseFromString(response.data, "application/xml");

              console.log(xmlDoc)

              const item = xmlDoc.getElementsByTagName("item")[0];
              if (!item) {
                console.log("該当する書誌データが見つかりませんでした。");
                return;
              }

              // タグ取得用関数（名前空間を使わず取得）
              const getTagText = (tag) => {
                const el = item.getElementsByTagName(tag)[0];
                return el ? el.textContent : "";
              };

              const title = getTagText("title");
              const author = getTagText("dc:creator");
              const publisher = getTagText("dc:publisher");
              const pubDate = getTagText("dcterms:issued");
              const link = getTagText("link"); 
              
              const ndc10 = (() => {
                            const subjects = item.getElementsByTagName("dc:subject");
                            for (let i = 0; i < subjects.length; i++) {
                              const el = subjects[i];
                              if (el.getAttribute("xsi:type") === "dcndl:NDC10") {
                                return el.textContent;
                              }
                            }
                            return "";
                          })();

              console.log("タイトル:", title);
              console.log("著者:", author);
              console.log("出版社:", publisher);
              console.log("出版年:", pubDate);
              console.log("リンク:", link);
              console.log("NDC10:", ndc10);

              $('#isbn13').val($('#isbn13input').val());
              $('#title').val(title);
              $('#author').val(author);
              $('#publisher').val(publisher);
              $('#publication_year').val(pubDate);
              $('#ndc10').val(ndc10);
              $('#detail_url').val(link);  
              
              const $linkDisplay = $('#link_display'); // aタグの要素を取得
              $linkDisplay.attr('href', link);       // aタグのhref属性にURLを設定
              $linkDisplay.text("詳細はこちら");               // aタグのテキストにURLを表示（または「詳細を見る」など）



            })
            .catch(function (error) {
              console.error("エラーが発生しました:", error);
            });
    }
});

</script>

</body>
</html>