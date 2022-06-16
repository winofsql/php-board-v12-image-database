<!DOCTYPE html>
<html lang="ja">
<head>
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <meta charset="utf-8">
    <title>掲示板</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="client.css?_=<?= time() ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
    

<script>
// **********************************************
// クリップボードデータ用変数
// **********************************************
var clipbpardText = "";

$(function(){

    // **********************************************
    // クリップボード処理オブジェクト
    // **********************************************
    var clipboard = 
        // clipboard_btn クラスに持つボタンが対象
        new ClipboardJS('.body_text' , {
            text: function(trigger) {

                console.log( clipbpardText );

                // clipboard.js に渡す( このデータがクリップポードに転送される )
                return clipbpardText;
            }
        });	

    $(".body_text").on("click", function(){

        clipbpardText = $(this).text();

    });


    $(".spanlink").on("click",function(){
        var id = $(this).prop("id");
        id = id.replace(/row/g,"");
        parent.$("#id").val( id );

        var subject = $(this).text();
        parent.$("#subject").val(subject);
        
        var name = $(this).nextAll().eq(0).text();
        name = name.replace(/[ \(]/g,"");
        var awork = name.split(":");
        parent.$("#name").val(awork[0]);

        var text = $(this).nextAll().eq(1).text();
        parent.$("#text").val(text);
    });

    $(".btn-outline-dark").on("click",function(){

        if ( !confirm("削除してもよろしいですか?") ) {
            return;
        }

        var id = $(this).prop("id");
        id = id.replace(/delete/g,"");

        var formData = new FormData();

        formData.append("id", id );

        $.ajax({
            url: "./delete.php",
            type: "POST",
            data: formData,
            processData: false,  // jQuery がデータを処理しないよう指定
            contentType: false   // jQuery が contentType を設定しないよう指定
        })
        .done(function( data, textStatus ){
            console.log( "status:" + textStatus );
            console.log( "data:" + JSON.stringify(data, null, "    ") );

            var kensu = parseInt( $("#data_head").data("kensu") );
            kensu--;
            $("#data_head").data("kensu", kensu);
            $("#data_head").text( "投稿一覧 (" + kensu + "件)" );
            
            $('#disp' + data.id).fadeOut(800);

        })
        .fail(function(jqXHR, textStatus, errorThrown ){
            console.log( "status:" + textStatus );
            console.log( "errorThrown:" + errorThrown );
        })
        .always(function() {

        })
        ;
    });

    <?= $clear ?>

});
</script>

</head>

<body>
<div id="data_head" data-kensu="<?= $kensu ?>" class="alert alert-primary">投稿一覧 (<?= $kensu ?>件)</div>
<div id="data_body">
    <span style='color:red'>
        <?php
            foreach( $error as $err ) {
                print "{$err}<br>";
            }
        ?>
    </span>
    <div id="data_entry">
        <?= $log_text ?>
    </div>
</div>
</body>
</html>
