<?php

//共通変数 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 プロフィール編集ページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getAthleteUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILE,true));

  //変数にユーザー情報を代入
  $athlete_username = $_POST['athlete_username'];
  $tel = $_POST['tel'];
  $team = $_POST['team'];
  $email = $_POST['email'];
  //画像をアップロードし、パスを格納
  $user_icon = ( !empty($_FILES['user_icon']['name']) ) ? uploadImg($_FILES['user_icon'],'user_icon') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $user_icon = ( empty($user_icon) && !empty($dbFormData['user_icon']) ) ? $dbFormData['user_icon'] : $user_icon;
  debug(print_r($user_icon, true));

  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['athlete_username'] !== $athlete_username){
    //名前の最大文字数チェック
    validMaxLen($athlete_username, 'athlete_username');
  }
  if($dbFormData['tel'] !== $tel){
    //TEL形式チェック
    validTel($tel, 'tel');
  }
  if($dbFormData['email'] !== $email){
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    if(empty($err_msg['email'])){
      //emailの重複チェック
      validEmailDup($email);
    }
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの未入力チェック
    validRequired($email, 'email');
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE athlete_users SET athlete_username = :u_name, tel = :tel, team = :team, email = :email, user_icon = :user_icon WHERE id = :u_id';
      $data = array(':u_name' => $athlete_username , ':tel' => $tel, ':team' => $team, ':email' => $email, ':user_icon' => $user_icon, ':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        debug('マイページへ遷移します。');
        header("Location:athleteMypage.php"); //マイページへ
      }
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'プロフィール編集';
require('基本パーツ/head.php');
?>

<body class="page-profEdit page-2colum page-logined">

  <!-- メニュー -->
  <?php
  require('基本パーツ/header_athlete.php');
  ?>

  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">
    <h1 class="page-title">プロフィール編集</h1>
    <!-- Main -->
    <section id="main" >
      <div class="form-container">
        <form action="" method="post" class="form" enctype="multipart/form-data">
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['athlete_username'])) echo 'err'; ?>">
            名前
            <input type="text" name="athlete_username" value="<?php echo getFormData('athlete_username'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['username'])) echo $err_msg['username'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
            TEL<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span>
            <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['tel'])) echo $err_msg['tel'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['team'])) echo 'err'; ?>">
            所属チーム<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span>
            <input type="text" name="team" value="<?php if( !empty(getFormData('team')) ){ echo getFormData('team'); } ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['team'])) echo $err_msg['zip'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            Email
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          アイコン
          <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="height:370px;line-height:370px;">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="user_icon" class="input-file" style="height:370px;">
            <img src="<?php echo getFormData('user_icon'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('user_icon'))) echo 'display:none;' ?>">
              ドラッグ＆ドロップ
          </label>

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
