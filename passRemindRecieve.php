<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行認証キー入力ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし（ログインできない人が使う画面なので）

//SESSIONに認証キーがあるか確認、なければリダイレクト
if(empty($_SESSION['auth_key'])){
  header("Location:passRemingSend");
}
//================================
//画面処理
//================================
//POST送信されていた場合
if(!empty($_POST)){
  debug('POST情報があります。');
  debug('POST情報:'.print_r($_POST, true));
  
  $auth_key = $_POST['token'];
  
  validRequired($auth_key, 'token');
  
  if(empty($err_msg)){
    debug('未入力チェックOK');
    
    validLength($auth_key, 'token');
    validHalf($auth_key, 'token');
    
    if(empty($err_msg)){
      debug('バリデーションOK');
      
      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG15;
      }
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['common'] = MSG16;
      }
      
      if(empty($err_msg)){
        debug('認証OK');
        
        $pass = makeRandKey(); //パスワード生成
        
        // 例外処理
        try {
          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :password WHERE email = :email AND delete_flg = 0';
          $data = array(':email' => $_SESSION['auth_email'], ':password' => password_hash($pass, PASSWORD_DEFAULT));
          
          $stmt = queryPost($dbh, $sql, $data);
          
          // クエリ成功の場合
          if($stmt){
            debug('クエリ成功');
            
            // メールを送信
            $from = '1104crf14@gmail.com';
            $to = $_SESSION['auth_email'];
            $subject = '[パスワード再発行完了] | Jグル';
            $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：http://localhost:8888/ウェブサOP(Jグル)/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

//////////////////////////////////////
Jグルカスタマーセンター
url http://jguru.index.php/
E-mail info@jguru.com
//////////////////////////////////////
EOT;
            sendMail($from, $to, $subject, $comment);
            
            // セッション削除
            session_unset();
            $_SESSION['msg_success'] = SUC03;
            debug('セッション変数の中身:'.print_r($_SESSION, true));
            
            header("Location:login.php");
          }else{
            debug('クエリに失敗しました');
            $err_msg['common'] = MSG07;
          }
        }catch(Exception $e){
          error_log('エラー発生:'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}
?>
<?php
$siteTitle = 'パスワード再発行認証';
require('基本パーツ/head.php'); 
?>

  <body class="page-signup page-1colum">

    <!-- メニュー -->
    <?php
      require('基本パーツ/header.php'); 
    ?>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

      <!-- Main -->
      <section id="main" >

        <div class="form-container">

          <form action="" method="post" class="form">
            <p>ご指定のメールアドレスお送りした【パスワード再発行認証】メール内にある「認証キー」をご入力ください。</p>
            <div class="area-msg">
             <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label class="<?php if(!empty($err_msg['token'])) echo 'err'; ?>">
              認証キー
              <input type="text" name="token" value="<?php echo getFormData('token'); ?>">
            </label>
            <div class="area-msg">
             <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="再発行する">
            </div>
          </form>
        </div>
        <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
      </section>

    </div>

    <!-- footer -->
    <?php
    require('基本パーツ/footer.php'); 
    ?>