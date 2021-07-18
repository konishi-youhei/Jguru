<?php

//共通変数 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 マイページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
//画面処理
//================================

debug(print_r($_SESSION['user_id'], true));

try {
  $dbh = dbConnect();
  $sql = 'SELECT store.id, store.pic1, store.name, athlete_users.athlete_username, athlete_users.user_icon, athlete_users.team, athlete_users.user_icon FROM store INNER JOIN athlete_users ON athlete_users.id = store.athlete_user_id WHERE athlete_user_id = :u_id';
  $data = array(':u_id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);

   if($stmt){
     $result = $stmt->fetchAll();
     $result['total'] = $stmt->rowCount();
   } else {
     $result = '';
   }
   debug('表示データ:' .print_r($result, true));
} catch (Exception $e) {
  error_log('エラー発生：'.$e->getMessage());
}

?>
 <?php
  $siteTitle = 'アスリートマイページ';
  require('基本パーツ/head.php');
?>

  <body  class="page-home page-2colum">
    <style>
      .list{
        overflow: hidden;
      }
      .left-img{
        float: left;
        margin-right: 30px;
      }
      .left-img img{
        width: 200px;
        height: 200px;
      }
      .right-contents{
        float: right;
        width: 400px;
        text-align: left;
      }
      .right-contents p{
        font-size: 20px;
        padding: 20px;
      }
      .user-icon{
        width: 100px;
        height: 100px;
        float: left;
        border-radius: 50%;
        position: relative;
        top: 40px;
        margin-right: 20px;
      }
      .user-icon img{
        width: 100%;
        height: 100%;
        border-radius: 50%;
        float: right;
      }
      .user-icon p{
        text-align: center;
      }
    </style>
    <!--ヘッダー-->
    <?php
      require('基本パーツ/header.php');
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <!--メイン-->
      <section id="main">
        <div class="search-title">
          <div class="search-left">
            <span class="total-num"><?php echo sanitize($result['total']); ?></span>件の投稿があります
          </div>
        </div>
        <?php
          foreach($result as $key => $val):
          debug(print_r($val, true));
        ?>
        <div class="list">
          <div class="left-img">
            <a href="recommendDetail.php<?php echo (!empty(appendGEtParam())) ? appendGetParam().'&s_id='.$val['id'] : '?s_id='.$val['id']; ?>" class="panel">
              <img src="<?php echo $val['pic1'] ?>" alt="">
            </a>
          </div>
          <div class="user-icon">
            <img src="<?php echo $val['user_icon'] ?>" alt="">
          </div>
          <div class="right-contents">
            <p>選手名: <?php echo $val['athlete_username'] ?></p>
            <p>所属チーム: <?php echo $val['team'] ?></p>
            <a href="recommendDetail.php<?php echo (!empty(appendGEtParam())) ? appendGetParam().'&s_id='.$val['id'] : '?s_id='.$val['id']; ?>" class="panel">
            <p>おすすめの店名: <?php echo sanitize($val['name']); ?></p>
            </a>
          </div>
        </div>
        <?php
          endforeach;
        ?>
      </section>

      <?php require('基本パーツ/athleteSidebar.php'); ?>
    </div>

    <!--フッター-->
    <?php
      require('基本パーツ/footer.php');
    ?>
