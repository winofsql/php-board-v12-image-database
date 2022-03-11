<?php
require_once("setting.php");

header( "Content-Type: application/json; charset=utf-8" );

require_once("model.php");

// *************************************
// DB 接続
// *************************************
$dbh = connectDb();

$id = (int)$_POST['id'];

$dbh->query("update board set dflg = 'D' where row_no = {$id}");

echo <<<JSON
{ 
    "id": {$id}
}
JSON;

?>
