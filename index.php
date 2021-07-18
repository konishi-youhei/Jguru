<?php

//共通変数 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 トップページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
//画面処理
//================================

//画面表示用データ取得
//================================
// GETパラメータを取得
//----------------------------------
debug(print_r($_GET, true));
debug(print_r($_GET['p'], true));
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;//デフォルトは１ページ目
debug(print_r($currentPageNum, true));
debug(print_r(gettype($currentPageNum) ,true));
// カテゴリー
$prefecture = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// パラメータに不正な値が入っているかチェック

// 表示件数
$listSpan = 20;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);
debug(print_r($currentMinNum, true));
debug(print_r(gettype($currentMinNum) ,true));
// DBから店舗データを取得
$dbStoreData = getStoreList($currentMinNum, $prefecture);
// DBから県名データを取得
$dbPrefectureData = getPrefecture();
debug('dbPrefectureData'.print_r($dbPrefectureData, true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
  $siteTitle = 'HOME';
  require('基本パーツ/head.php');
?>
  <body  class="page-home page-2colum">
    <style>
      .list{
        overflow: hidden;
        margin-bottom: 10px;
        margin-left: 5px;
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

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <!--サイドバー-->
      <section id="sidebar">
        <form name="" method="get">
          <h1 class="title">県名</h1>
          <div class="selectbox">
            <span class="icn-select"></span>
            <select name="c_id" id="">
              <option value="0" <?php if(getFormData('c_id',true) == 0){ echo 'selected'; } ?>>選択して下さい</option>
              <?php
                foreach($dbPrefectureData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id',true) == $val['id'] ){ echo 'selected'; } ?> >
                  <?php echo $val['prefecture_name']; ?>
                </option>
              <?php
                }
              ?>
            </select>
          </div>
          <input type="submit" value="検索">
        </form>
      </section>

      <!--メイン-->
      <section id="main">
        <div class="search-title">
          <div class="search-left">
            <span class="total-num"><?php echo sanitize($dbStoreData['total']); ?></span>件のおすすめが見つかりました
          </div>
          <div class="search-right">
            <span class="num"><?php echo (!empty($dbStoreData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbStoreData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbStoreData['total']); ?></span>件中
          </div>
        </div>

        <?php
          foreach($dbStoreData['data'] as $key => $val):
          debug(print_r($val, true));
        ?>
        <div class="list">
          <div class="left-img">
          <a href="recommendDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$val['id'] : '?s_id='.$val['id']; ?>" class="panel">
            <img src="<?php echo sanitize($val['pic1']); ?>" alt="">
          </a>
          </div>
          <div class="user-icon">
            <img src="<?php echo $val['user_icon'] ?>" alt="">
          </div>
          <div class="right-contents">
            <p>選手名: <?php echo sanitize($val['athlete_username']) ?></p>
            <p>所属チーム: <?php echo sanitize($val['team']) ?></p>
            <a href="recommendDetail.php<?php echo (!empty(appendGEtParam())) ? appendGetParam().'&s_id='.$val['id'] : '?s_id='.$val['id']; ?>" class="panel">
            <p>おすすめの店名: <?php echo sanitize($val['name']); ?></p>
            </a>
          </div>
        </div>
        <?php
          endforeach;
        ?>

        <?php pagination($currentPageNum, $dbStoreData['total_page']); ?>

      </section>
    </div>

    <!--フッター-->
    <?php
      require('基本パーツ/footer.php');
    ?>
