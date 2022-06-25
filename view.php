<!DOCTYPE html>
<html lang="ja">
<head>
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
    <title>掲示板 v12 画像名をDBに登録</title>

<?php require_once("iframe-css.php") ?>
    <link rel="stylesheet" href="client.css?_=<?= time() ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <link id="link" rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



<script>
jQuery.isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
toastr.options={"closeButton":false,"debug":false,"newestOnTop":false,"progressBar":false,"positionClass":"toast-bottom-center","preventDuplicates":false,"onclick":null,"showDuration":"300","hideDuration":"1000","timeOut":"3000","extendedTimeOut":"1000","showEasing":"swing","hideEasing":"linear","showMethod":"fadeIn","hideMethod":"fadeOut"};
if ( !$.isMobile ) {
    toastr.options.positionClass = "toast-top-center";
}

$( function(){

    // 実行完了時に localStorage に保存された名前を復帰
    if ( typeof(localStorage["name"]) != 'undefined' ) {
        $("#name").val( localStorage["name"] );
    }

    // フォーム送信イベント
    $("form").on("submit", function(){

        var name = $("#name").val();
        name = name.trim();
        if ( name == "" ) {
            // 本来の送信処理はキャンセルする
            event.preventDefault();
            toastr.error("お名前を入力してください");
        }

        var text = $("#text").val();
        text = text.trim();
        if ( text == "" ) {
            // 本来の送信処理はキャンセルする
            event.preventDefault();
            toastr.error("本文を入力してください");
        }


        // 実行完了時に localStorage に保存
        localStorage["name"] = name;

    });


    // *************************************
    // ファイル選択
    // *************************************
    $("#file").on( "change", function(){
        if ( $("#file").get(0).files.length == 1 ) {
            $("#upload").prop( "disabled", false );
        }
        else {
            $("#upload").prop( "disabled", true );
        }
    });

    // *************************************
    // アップロード処理
    // *************************************
    $("#upload").on( "click", function(){

        // if ( !confirm("アップロードを開始してもよろしいですか?") ) {
        //     return;
        // }

        $( "#dialog-message" ).dialog({
            modal: true,
            title: "ダイアログのタイトルです",
            close: function() {
                $( this ).dialog( "close" );
            },
            buttons: [
                { 
                    text: "OK",
                    click: function() {
                        $( this ).dialog( "close" );
                        file_upload();
                    }
                },
                {
                    text: "キャンセル",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });		

    });

    // キーボードのキーが押された場合
    $("#text").keydown(function( e ){

        var code = e.keyCode;
        console.log(code + " が押されました" );

        // F2 キー
        if ( code == 113 ) {
            var text = $(this).val();
            // IFRAME 内のテキストを検索
            $("#extend").contents().find(".body_text").each( function( index, element ){
                var target = $(this).text();
                if ( target.indexOf(text) != -1 ) {
                    $("#extend").contents().scrollTop( $(element).position().top );
                    // ループ終了
                    return false;
                }
            });
        }
    });

});

// *************************************
// $.ajax ファイルアップロード
// *************************************
function file_upload() {

    var formData = new FormData();

    // 画像データサイズの制限
    formData.append("MAX_FILE_SIZE", 10000000);

    // formData に画像ファイルを追加
    formData.append("image", $("#file").get(0).files[0]);
    formData.append("id", $("#id").val() );

    $.ajax({
        url: "./upload.php",
        type: "POST",
        data: formData,
        processData: false,  // jQuery がデータを処理しないよう指定
        contentType: false   // jQuery が contentType を設定しないよう指定
    })
    .done(function( data, textStatus ){
        console.log( "status:" + textStatus );
        console.log( "data:" + JSON.stringify(data, null, "    ") );
        
        if ( data.image.error != 0 ) {
            toastr.error(data.image.result);
        }

        $("#subject").val("");
        $("#name").val("");
        $("#text").val("");
        $("#id").val("");

        $("#file").val("");
        $("#upload").prop("disabled", true);

        $('#extend').get(0).contentWindow.location.reload(true);

    })
    .fail(function(jqXHR, textStatus, errorThrown ){
        console.log( "status:" + textStatus );
        console.log( "errorThrown:" + errorThrown );
    })
    .always(function() {

        // 操作不可を解除
        $("#content input").prop("disabled", false);
    })
    ;
}

</script>
</head>

<body>
<div id="bbs">
    <h3 class="alert alert-primary">
        <a href="control.php" style="color:black;">掲示板 ( MySQL )</a>
        <a href=".." style="float:right;text-decoration:none;">📂</a>
    </h3>
    <div id="content"
        >
        <form action=""
            target="myframe"
            method="POST">
            <div>
                <span class="title_entry">
                    タイトル
                </span>
                <input
                    type="text"
                    name="subject"
                    id="subject"
                    pattern=".*\S+.*"
                    required
                    >
            </div>
            <div>
                <span class="title_entry">
                    名前
                </span>
                <input
                    type="text"
                    name="name"
                    id="name"
                    pattern="[ぁ-んァ-ン一-龥 　]+"
                    required
                    >
            </div>
            <div>
                <textarea name="text" id="text"></textarea>
            </div>
            <div>
                <input type="submit" name="send" id="send" value="送信">
                <input type="file" name="file" id="file" accept=".jpg,.jpeg">
                <input type="button" id="upload" value="画像アップロード" disabled>
            </div>
            <input type="hidden" name="datetime" id="datetime">
            <input type="hidden" name="id" id="id">
        </form>
    </div>
</div>

<iframe id="extend" src="control.php?page=init" name="myframe"></iframe>

<div id="dialog-message" style='display:none;'>
アップロードを開始してもよろしいですか?
</div>
</body>
</html>
