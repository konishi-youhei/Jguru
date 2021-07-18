<?php

require('function.php');

// POST送信されていた場合
if (!empty($_POST)) {

  // 変数にユーザ情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
  $name = $_POST['name'];
  $tel = $_POST['tel'];
  $team = $_POST['team'];

  // 未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');
  validRequired($email, 'name');
  validRequired($pass, 'tel');
  validRequired($pass_re, 'team');

  if (empty($err_msg)) {
    // email形式チェック
    validEmail($email, 'email');
    // email最大文字数チェック
    validMaxLen($email, 'email');
    // email重複チェック
    validEmailDup($email);

    //パスワード半角英数字チェック
    validHalf($pass, 'pass');
    // パスワード最大文字数チェック
    validMaxLen($pass, 'pass');
    // パスワード最小文字数チェック
    validMinLen($pass, 'pass');

    // パスワード(再入力)最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    // パスワード(再入力)最小文字数チェック
    validMinLen($pass_re, 'pass_re');

    //TEL形式チェック
    validTel($tel, 'tel');


    if (empty($err_msg)) {
      validMatch($pass, $pass_re, 'pass_re');

      if (empty($err_msg)) {

        // 例外処理
        try {
          $dbh = dbConnect();
          $sql = 'INSERT INTO athlete_users (athlete_username, email, password, tel, team, login_time, create_date) VALUES(:name, :email, :password, :tel, :team, :login_time, :create_date)';
          $date = array(':name' => $name, ':email' => $email, ':password' => password_hash($pass, PASSWORD_DEFAULT), ':tel' => $tel, 'team' => $team, 'login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));

          $stmt = queryPost($dbh, $sql, $date);

          if ($stmt) {
            $sesLimit = 60*60;
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身:'.print_r($_SESSION, true));
            header("Location:mypage.php");
          }
        } catch (Exception $e) {
          error_log('エラー発生:' .$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}

?>

<?php
  $siteTitle = 'アスリートユーザ登録';
  require('基本パーツ/head.php');
?>
<body class="page-siginup page-1colum">

  <!--ヘッダー-->
  <?php
    require('基本パーツ/header.php');
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <!--Main-->
    <section id="main">
      <div class="form-container">
        <form action="" method="post" class="form">
          <h2>アスリート登録</h2>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
            名前
            <input type="text" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>">
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['name'])) echo $err_msg['name'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['team'])) echo 'err'; ?>">
            所属チーム
            <input type="text" name="team" value="<?php if(!empty($_POST['team'])) echo $_POST['team']; ?>">
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['team'])) echo $err_msg['team'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
            TEL<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span>
            <input type="text" name="tel" value="<?php if(!empty($_POST['tel'])) echo $_POST['tel']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['tel'])) echo $err_msg['tel'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            Email
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
          <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
            パスワード(再入力)
            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="登録する">
          </div>
        </form>
      </div>
    </section>

  </div>

</body>
