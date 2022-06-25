
$(function(){

    // *************************************
    // アップロード処理
    // *************************************
    $("#upload").on( "click", function(){

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
    
    $(".btn-outline-dark").on("click",function(){

        var target = $(this)
        // parent.$ : 親ウインドウに対してダイアログを表示
        // modalDialog : 親ウインドウのオブジェクトを後で操作する為に保存
        var modalDialog = parent.$( "#dialog-message" ).dialog({
            modal: true,
            title: "ダイアログのタイトルです",
            close: function() {
                // 親ウインドウのオブジェクトで閉じる
                modalDialog.dialog( "close" );
            },
            buttons: [
                { 
                    text: "OK",
                    click: function() {
                        // 親ウインドウのオブジェクトで閉じる
                        modalDialog.dialog( "close" );
                        entry_delete( target );
                    }
                },
                {
                    text: "キャンセル",
                    click: function() {
                        // 親ウインドウのオブジェクトで閉じる
                        modalDialog.dialog( "close" );
                    }
                }
            ]
        });

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

// *************************************
// $.ajax 記事削除
// *************************************
function entry_delete( target ) {

    var id = target.prop("id");
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

}