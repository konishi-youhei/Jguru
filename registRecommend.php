<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品出品登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETデータを格納
$s_id = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
// DBから商品データを取得
$dbFormData = (!empty($p_id)) ? getStore($_SESSION['user_id'], $s_id) : '';
// 新規登録画面か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
// DBからカテゴリデータを取得
$dbPrefectureData = getPrefecture();
debug('商品ID：'.$s_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('県名データ：'.print_r($dbPrefectureData,true));

// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる
if(!empty($s_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ遷移します。');
  header("Location:mypage.php"); //マイページへ
}

// POST送信時処理
//================================
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $prefecture = $_POST['prefecture_id'];
  $address = $_POST['address'];
  $comment = $_POST['comment'];
  //画像をアップロードし、パスを格納
  $pic1 = ( !empty($_FILES['pic1']['name']) ) ? uploadImg($_FILES['pic1'],'pic1') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic1 = ( empty($pic1) && !empty($dbFormData['pic1']) ) ? $dbFormData['pic1'] : $pic1;
  $pic2 = ( !empty($_FILES['pic2']['name']) ) ? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic2 = ( empty($pic2) && !empty($dbFormData['pic2']) ) ? $dbFormData['pic2'] : $pic2;
  $pic3 = ( !empty($_FILES['pic3']['name']) ) ? uploadImg($_FILES['pic3'],'pic3') : '';
  $pic3 = ( empty($pic3) && !empty($dbFormData['pic3']) ) ? $dbFormData['pic3'] : $pic3;

  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    //未入力チェック
    validRequired($name, 'name');
    //最大文字数チェック
    validMaxLen($name, 'name');
    //セレクトボックスチェック
    validSelect($prefecture, 'prefecture_id');
    //最大文字数チェック
    validMaxLen($comment, 'comment', 500);
  }else{
    if($dbFormData['name'] !== $name){
      //未入力チェック
      validRequired($name, 'name');
      //最大文字数チェック
      validMaxLen($name, 'name');
    }
    if($dbFormData['Prefecture_id'] !== $prefecture){
      //セレクトボックスチェック
      validSelect($prefecture, 'prefecture_id');
    }
    if($dbFormData['comment'] !== $comment){
      //最大文字数チェック
      validMaxLen($comment, 'comment', 500);
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
      if($edit_flg){
        debug('DB更新です。');
        $sql = 'UPDATE store SET name = :name, prefecture_id = :prefecture, address = :address, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE athlete_user_id = :u_id AND id = :s_id';
        $data = array(':name' => $name , ':prefecture_id' => $prefecture, ':address' => $address, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':s_id' => $s_id);
      }else{
        debug('DB新規登録です。');
        $sql = 'insert into store (name, prefecture_id, address, comment, pic1, pic2, pic3, athlete_user_id, create_date ) values (:name, :prefecture_id, :address, :comment,  :pic1, :pic2, :pic3, :u_id, :date)';
        $data = array(':name' => $name , ':prefecture_id' => $prefecture, ':address' => $address, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：'.$sql);
      debug('流し込みデータ：'.print_r($data,true));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC04;
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
  $siteTitle = (!$edit_flg) ? 'おすすめ店登録' : 'おすすめ店編集';
  require('基本パーツ/head.php');
?>

<body class="page-profEdit page-2colum page-logined">

  <!--ヘッダー-->
  <?php
    require('基本パーツ/header_athlete.php');
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">
    <h1 class="page-title"><?php echo (!$edit_flg) ? 'おすすめ店を登録する' : 'おすすめ店を編集する'; ?></h1>
    <!--Main-->
    <section id="main">
      <div class="form-container">
        <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
          <div class="area-msg">
            <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
            店名<span class="label-require">必須</span>
            <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['name'])) echo $err_msg['name'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['prefecture'])) echo 'err'; ?>">
              県名<span class="label-require">必須</span>
              <select name="prefecture_id" id="">
                <option value="0" <?php if(getFormData('prefecture_id') == 0 ){ echo 'selected'; } ?> >選択してください</option>
                <?php
                  foreach($dbPrefectureData as $key => $val){
                ?>
                  <option value="<?php echo $val['id'] ?>" <?php if(getFormData('prefecture_id') == $val['id'] ){ echo 'selected'; } ?> >
                    <?php echo $val['prefecture_name']; ?>
                  </option>
                <?php
                  }
                ?>
              </select>
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['prefecture_id'])) echo $err_msg['prefecture_id'];
              ?>
          </div>
          <label class="<?php if(!empty($err_msg['address'])) echo 'err'; ?>">
            住所<span class="label-require">必須</span>
            <input type="text" name="address" value="<?php echo getFormData('address'); ?>">
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['address'])) echo $err_msg['address'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
            おすすめポイント
            <textarea name="comment" id="js-count" cols="30" rows="10" style="height:150px;"><?php echo getFormData('comment'); ?></textarea>
          </label>
          <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['comment'])) echo $err_msg['comment'];
              ?>
          </div>
          <div style="overflow:hidden;">
            <div class="imgDrop-container">
              画像1
              <label class="area-drop <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic1" class="input-file">
                <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];
                ?>
              </div>
              </div>
              <div class="imgDrop-container">
                画像２
                <label class="area-drop <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic2" class="input-file">
                  <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;' ?>">
                    ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                  <?php
                  if(!empty($err_msg['pic2'])) echo $err_msg['pic2'];
                  ?>
                </div>
              </div>
              <div class="imgDrop-container">
                画像３
                <label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic3" class="input-file">
                  <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;' ?>">
                    ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                  <?php
                  if(!empty($err_msg['pic3'])) echo $err_msg['pic3'];
                  ?>
                </div>
            </div>
          </div>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="登録">
          </div>
        </form>
      </div>
    </section>

    <!-- サイドバー -->
      <?php
      require('基本パーツ/athleteSidebar.php');
      ?>
  </div>
  <!-- footer -->
    <?php
    require('基本パーツ/footer.php');
    ?>
