<?php

//共通変数 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 パスワード変更ページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');
 
//================================
//画面処理
//================================
//　DBからユーザーデータ取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報');

// POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報:'.print_r($_POST,true));
  
  // 変数にユーザー情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];
  
  // 未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');
  
  if(empty($err_msg)){
    debug('未入力チェックOK');
    
    // 古いパスワードのチェック
    validPass($pass_old, 'pass_old');
    // 新しいパスワードのチェック
    validPass($pass_new, 'pass_new');
    
    // 古いパスワードとDBパスワードが同じかチェック
    if(!password_verify($pass_old,$userData['password'])){
      $err_msg['pass_old'] = MSG12;
    }
    // 新しいパスワードとパスワード再入力が同じかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new');
    
    if(empty($err_msg)){
      debug('バリデーションOK');
      
      // 例外処理
      try {
        $dbh = dbConnect();
        $sql = 'UPDATE users SET password = :password WHERE id = :u_id';
        $data = array(':u_id'=>$_SESSION['user_id'], ':password'=>password_hash($pass_new, PASSWORD_DEFAULT));
        
        $stmt = queryPost($dbh, $sql, $data);
        
        // クエリ成功の場合
        if($stmt){
          $_SESSION['msg_success'] = SUC01;
          
          // メールを送信
          $username = ($userData['username']) ? $userData['username'] : '名無し';
          $from = '1104crf14@gmail.com';
          $to = $userData['email'];
          $subject = 'パスワード変更通知 | Jグル';
          $comment = <<<EOT
{$username} さん
パスワードが変更されました。

///////////////////////////////
Jグルカスタマーセンター
url http://jguru.index.php/
E-mail info@jguru.com
///////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);
          
          header("Location:mypage.php");
        }
      } catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
?>

<?php
$siteTitle = 'パスワード変更';
require('基本パーツ/head.php'); 
?>

  <body class="page-passEdit page-2colum .page-logined">
    <style>
      .form{
        margin-top: 50px;
      }
      #sidebar{
        margin: 0 0 0 20px;
      }
      #sidebar > a{
        display: block;
        margin-bottom: 15px;
      }
    </style>
    
    <!-- メニュー -->
    <?php
      require('基本パーツ/header.php'); 
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
      <h1 class="page-title">パスワード変更</h1>
      <!-- Main -->
      <section id="main" >
        <div class="form-container">
          <form action="" method="post" class="form">
           <div class="area-msg">
             <?php
               echo getErrMsg('common');
             ?>
           </div>
            <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
              古いパスワード
              <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
            </label>
            <div class="area-msg">
              <?php
               echo getErrMsg('pass_old');
             ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
              新しいパスワード
              <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
            </label>
            <div class="area-msg">
              <?php
               echo getErrMsg('pass_new');
             ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
              新しいパスワード（再入力）
              <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
            </label>
            <div class="area-msg">
              <?php
               echo getErrMsg('pass_new_re');
             ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="変更する">
            </div>
          </form>
        </div>
      </section>
      
      <!-- サイドバー -->
      <?php
      require('基本パーツ/sidebar.php');
      ?>
      
    </div>

    <!-- footer -->
    <?php
    require('基本パーツ/footer.php'); 
    ?>
