<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['storeId']) && isset($_SESSION['user_id'])){
  debug('POST送信があります。');
  $s_id = $_POST['storeId'];
  debug('店ID:'.$s_id);
  // 例外処理
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM like_store WHERE store_id = :s_id AND user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':s_id' => $s_id);
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);
    // レコード1件でもある場合
    if(!empty($resultCount)) {
      $sql = 'DELETE FROM like_store WHERE store_id = :s_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':s_id' => $s_id);
      $stmt = queryPost($dbh, $sql, $data);
    }else{
      $sql = 'INSERT INTO like_store(store_id, user_id, create_date) VALUES (:s_id, :u_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':s_id' => $s_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
  }
}
debug('Ajax処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>