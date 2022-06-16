<?php
require_once("setting.php");

header( "Content-Type: text/html; charset=utf-8" );

require_once("model.php");

// **************************************
// $_POST['send'] != "" は送信ボタンが
// クリックされた事を示します
// さらに、テキストエリアに何か入力され
// た場合に処理を行います
// **************************************
$_POST['text'] = preg_replace( "/^[　\s]+/u", "", $_POST['text'] );
$_POST['text'] = preg_replace( "/[　\s]+$/u", "", $_POST['text'] );
if ( $_POST['send'] != "" ) {

    check_post();
    if ( count( $error ) == 0 ) {
        write_data();
    }

}

// *************************************
// 画面
// *************************************
if ( $_SERVER["REQUEST_METHOD"] == "GET" && $_GET["page"] != "init" ) {
    require_once("view.php");
}
if ( $_SERVER["REQUEST_METHOD"] == "GET" && $_GET["page"] == "init" ) {
    read_data( );
    require_once("view2.php");
}
if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    read_data( );
    require_once("view2.php");
}


//debug_print();
?>
