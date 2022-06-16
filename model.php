<?php
// **************************************
// データの書き込み処理
// **************************************
function write_image_data( $id, $file_name ) {

    // DB 接続
    $dbh = connectDb();
    if ( $dbh == null ) {
        return false;
    }

    $sql = <<<SQL
        update board set
            image = :image
            where row_no = :id
SQL;

    try {
        // SQL 文の準備
        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue( ':image', $file_name );
        $stmt->bindValue( ':id', $id );

        // 完成した SQL の実行
        $stmt->execute();

        file_put_contents("write_image_data.log", "{$sql} | {$id} | {$file_name}");

    }
    catch ( PDOException $e ) {
        file_put_contents("write_image_data.log", print_r($e,true));
        return false;
    }
    
    return true;

}

// *************************************
// 入力チェック
// *************************************
function check_post() {

    file_put_contents("check_post.log", print_r($_POST,true) ,FILE_APPEND );

    global $error;

    $GLOBALS["name"]    = trim( $_POST['name'] );
    $GLOBALS["subject"] = trim( $_POST['subject'] );
    $GLOBALS["text"]    = trim( $_POST['text'] );
    $GLOBALS["id"]      = trim( $_POST['id'] );

    // *************************************
    // エラー処理
    // *************************************
    if ( $GLOBALS["subject"] == '' ){
        $error['subject'] = 'タイトル入力してください';
    }
    if ( $GLOBALS["name"] == '' ){
        $error['name'] = 'お名前を入力してください';
    }
    if ( $GLOBALS["text"] == '' ){
        $error['text'] = '本文を入力してください';
    }

    file_put_contents("check_post.log", print_r($error,true), FILE_APPEND );

}

// **************************************
// データの書き込み処理
// **************************************
function write_data() {

    global $error;
    global $clear;

    // DB 接続
    $dbh = connectDb();
    if ( $dbh == null ) {
        return false;
    }

	// 新規
    if ( $_POST["id"] == "" ) {
        $sql = "insert into board
                (`from`, body, cdate, subject)
                values
                (:from, :body, now(), :subject)";
    }
	// 修正
    else {
        $sql = "update board set
                `from` = :from,
                body = :body,
                pdate = now(),
                subject = :subject where row_no = :id";
    }

    file_put_contents( "debug.log", $sql . "\n", FILE_APPEND );

    try {
        // SQL 文の準備
        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue( ':subject', $GLOBALS["subject"], PDO::PARAM_STR );
        $stmt->bindValue( ':from', $GLOBALS["name"], PDO::PARAM_STR );
        $stmt->bindValue( ':body', $GLOBALS["text"], PDO::PARAM_STR );

		// 修正時の :id に対するバインド
        if ( $_POST["id"] != "" ) {
            $stmt->bindValue( ':id', $GLOBALS["id"], PDO::PARAM_STR );
        }

        // 完成した SQL の実行
        $stmt->execute();

    }
    catch ( PDOException $e ) {
        $error['db'] = $e->getMessage();
        return false;
    }

    $clear = <<<SCRIPT

    parent.$("input[name='subject']").val("");
    parent.$("input[name='name']").val("");
    parent.$("textarea").val("");
    parent.$("#id").val("");

SCRIPT;

}

// **************************************
// データの表示処理
// **************************************
function read_data() {

    global $logfile,$kensu;

    // 埋め込み用データを global 宣言
    global $log_text;

    // DB 接続
    $dbh = connectDb();
    if ( $dbh == null ) {
        return false;
    }

    try {
        $stmt = $dbh->prepare("select * from board where dflg is null order by row_no desc");
        $stmt->execute();
    }
    catch ( PDOException $e ) {
        $error["db"] = $e->getMessage();
        return;
    }

    $log_text = "";
    $kensu = 0;
    file_put_contents("read_data.log", print_r($stmt,true));
    while( $entry = $stmt->fetch() ) {

        file_put_contents("read_data.log", print_r($entry,true), FILE_APPEND);

        foreach( $entry as $key => $value ) {

            // HTML 要素を無効にする
            $entry[$key] = htmlspecialchars( $value );

        }

        // **************************************
        // 本文の改行は br 要素で表現します
        // **************************************
        $entry['body'] = str_replace("\n", "<br>", $entry['body'] );

        // **************************************
        // 行毎に表示 HTML を作成
        // **************************************
        $log_text .= <<<LOG
<div
    class='title'
    id="disp{$entry['row_no']}"
    >
    <input
        type="button"
        id="delete{$entry['row_no']}"
        value="削除"
        style='float:right;width:100px;'
        class="btn btn-outline-dark btn-sm"
        >
    <span class='spanlink' id='row{$entry['row_no']}'>{$entry['subject']}</span>
    <span>( {$entry['from']} : {$entry['cdate']} ) </span>
    <div class="body_text">{$entry['body']}</div>
</div>

LOG;

        $kensu++;

    }


}

// *************************************
// データベース接続
// *************************************
function connectDb(){

    global $error;

    $result = null;

    try {
        $result = new PDO( $GLOBALS["connect_string"], $GLOBALS["user"], $GLOBALS["password"] );
    } 
    catch ( PDOException $e ) {
        $error["db"] = "<div>{$GLOBALS["connect_string"]}, {$GLOBALS["user"]}, {$GLOBALS["password"]}</div>"; 
        $error["db"] .= $e->getMessage();
        return $result;
    }
    // 接続以降で try ～ catch を有効にする設定
    $result->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $result;

}

// **************************
// デバッグ表示
// **************************
function debug_print() {

    print "<pre class=\"m-5\">";
    print_r( $_GET );
    print_r( $_POST );
    print_r( $_SESSION );
    print "</pre>";

}

?>
