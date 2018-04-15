<?php
$sid = $_REQUEST["CallSid"];// 通話時に発行されるID
$status =  $_REQUEST["CallStatus"];// ステータス
$duration = $_REQUEST["CallDuration"];// 通話時間
$digits = $_REQUEST["Digits"];// 押した番号
$from = $_REQUEST["From"];// 相手電話番号

try {
//データベース接続
$pdo = new PDO('mysql:host=localhost;dbname=ivr;charset=utf8','root','');
} catch (PDOException $e) {
 exit('データベース接続失敗。'.$e->getMessage());
}

//SQL文作成
$sqlInsert = "INSERT INTO regist VALUES(:id, :tel, :push)";

//テーブル(regist)に登録
$stml = $pdo->prepare($sqlInsert);
$stml -> bindValue(':id', 00, PDO::PARAM_INT);
$stml -> bindValue(':tel', $from, PDO::PARAM_STR);
$stml -> bindValue(':push', $digits, PDO::PARAM_INT);
$stml -> execute();

// ログデータ準備
$log_data = 'Call_Sid='.$_REQUEST["CallSid"]."\n";
$log_data .= 'DialCallStatus='.$_REQUEST["CallStatus"]."\n";
$log_data .= 'AccountSid='.$_REQUEST["AccountSid"]."\n";
$log_data .= 'CallDuration='.$_REQUEST["CallDuration"]."\n";
$log_data .= 'PushNumber='.$_REQUEST["Digits"]."\n";
$log_data .= 'FromNumber='.$_REQUEST["From"]."\n";
$log_data .= '----------終了-------------'."\n";
 
// ログデータをファイルに保存
$fp = fopen('./filename.txt', 'ab');
if ($fp){
	fwrite($fp,  $log_data);
}
fclose($fp);


if($_POST['Digits'] == 1):
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo '<Response>';
	echo '<Say language="ja-jp">夕飯は「必要」が選択されました。ご利用ありがとうございました。</Say>';
	echo '</Response>';
elseif($_POST['Digits'] == 2):
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo '<Response>';
	echo '<Say language="ja-jp">夕飯は「不要」が選択されました。ご利用ありがとうございました。</Say>';
	echo '</Response>';
else:
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo '<Response>';
	echo '<Say language="ja-jp">1、または、2、を選択してください。</Say>';
	echo '<Redirect method="POST">/ivr_twiml.xml</Redirect>';
	echo '</Response>';
endif;
?>