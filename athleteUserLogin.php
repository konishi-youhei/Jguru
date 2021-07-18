<?php

//共通変数 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 ログインページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//================================
//ログイン画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');

  // 変数にユーザ情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  //email形式チェック
  validEmail($email, 'email');
  //emailの最大文字数チェック
  validMaxLen($email, 'email');

  //パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  //パスワードの最大文字数チェック
  validMaxLen($pass, 'pass');
  //パスワードの最小文字数チェック
  validMinLen($pass, 'pass');

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションOKです');

    try{
      $dbh = dbConnect();
      $sql = 'SELECT password, id FROM athlete_users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);

      $stmt = queryPost($dbh, $sql, $data);

      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：'.print_r($result, true));

      if(!empty($result) && password_verify($pass, array_shift($result))){
        debug('パスワードがマッチしました');

        $sesLimit = 60*60;

        $_SESSION['login_date'] = time();

        if($pass_save){
          debug('ログイン保持にチェックがあります');

          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
          debug('ログイン保持にチェックがありません');

          $_SESSION['login_limit'] = $sesLimit;
        }
        $_SESSION['user_id'] = $result['id'];

        debug('セッション変数の中身：'.print_r($_SESSION, true));
        debug('マイページへ遷移します');
        header("Location:index.php");
      }else{
        debug('パスワードがアンマッチです');
        $err_msg['common'] = MSG09;
      }
    }catch(Exception $e) {
      error_log('エラー発生:' .$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>


 <?php
   $siteTitle = 'アスリートログイン';
   require('基本パーツ/head.php');
 ?>
 <body class="page-login page-1colum">

   <!--ヘッダー-->
   <?php
     require('基本パーツ/header_athlete.php');
   ?>
   <!--エラーメッセージ-->
   <p id="js-show-msg" style="display:none;" class="msg-slide">
     <?php echo getSessionFlash('msg_success'); ?>
   </p>

   <!--メインコンテンツ-->
   <div id="contents" class="site-width">

     <!--Main-->
     <section id="main">
       <div class="form-container">
         <form action="" method="post" class="form">
           <h2 class="title">アスリート専用ログイン</h2>
           <div class="area-msg">
             <?php
               if(!empty($err_msg['common'])) echo $err_msg['common'];
             ?>
           </div>
           <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
             メールアドレス
             <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
           </label>
           <div class="area-msg">
             <?php
               if(!empty($err_msg['email'])) echo $err_msg['email'];
             ?>
           </div>
           <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
             パスワード
             <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
           </label>
           <div class="area-msg">
             <?php
               if(!empty($err_msg['pass'])) echo $err_msg['pass'];
             ?>
           </div>
           <label>
             <input type="checkbox" name="pass_save">次回ログインを省略する
           </label>
           <div class="btn-container">
             <input type="submit" class="btn btn-mid" value="ログイン">
           </div>
           パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a>
         </form>
       </div>
     </section>
   </div>

   <!--footer-->
   <?php
     require('基本パーツ/footer.php');
   ?>
