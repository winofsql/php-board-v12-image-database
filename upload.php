<?php
require_once("setting.php");

header( "Content-Type: application/json; charset=utf-8" );

require_once("model.php");

// ファイルを移動するフォルダ
$data_path = "./myfiles";

// フォルダが無ければ作成
if ( !is_dir( $data_path ) ) {

    mkdir( $data_path );

}


$image_target = "image";

if ( $_FILES[$image_target]["error"] == 0 ) {

    // *************************************
    // 画像フォーマットの取得
    // *************************************
    $type_string = image_type_to_mime_type( exif_imagetype( $_FILES[$image_target]['tmp_name'] ) );

    // *************************************
    // 保存ファイル名を作成
    //   a) 拡張子決定
    //   b) uniqid() でファイル目をユニーク
    // *************************************
    $target = "";
    if ( $type_string == "image/jpeg" ) {
        $target = uniqid() . ".jpg";
    }
    if ( $type_string == "image/gif" ) {
        $target = uniqid() . ".gif";
    }
    if ( $type_string == "image/png" ) {
        $target = uniqid() . ".png";
    }
    if ( $target == "" ) {
        $target = uniqid() . "_{$_FILES[$image_target]['name']}";
    }

    // *************************************
    // アップロードファイルの保存
    // *************************************
    if ( @move_uploaded_file( $_FILES[$image_target]['tmp_name'], "{$data_path}/{$target}" ) ) {
        $_FILES[$image_target]["result"] = "アップロードに成功しました";
        // データベースに登録
        write_image_data( $_POST["id"], $target );
    }
    else {
        // なんらかの環境エラー
        $_FILES[$image_target]["result"] = "アップロードに失敗しました";
    }
}
else {
    switch($_FILES[$image_target]["error"]){
        case 1:
            $_FILES[$image_target]["result"] = "php.ini の upload_max_filesize ディレクティブの値を超えています";
            break;
        case 2:
            $_FILES[$image_target]["result"] = "HTML フォームで指定された MAX_FILE_SIZE を超えています";
            break;
        case 3:
            $_FILES[$image_target]["result"] = "一部のみしかアップロードされていません";
            break;
        case 4:
            $_FILES[$image_target]["result"] = "アップロードされませんでした";
            break;
        case 6:
            $_FILES[$image_target]["result"] = "テンポラリフォルダがありません";
            break;
        case 7:
            $_FILES[$image_target]["result"] = "ディスクへの書き込みに失敗しました";
            break;
        case 8:
            $_FILES[$image_target]["result"] = "PHP の拡張モジュールがファイルのアップロードを中止しました";
            break;
        default:
            $_FILES[$image_target]["result"] = "不明なエラーです";
    }
    
}

print json_encode($_FILES)


?>
