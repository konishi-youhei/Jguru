<?php

//共通変数 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 商品詳細ページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');
 
//================================
//画面処理
//================================

//画面表示用データ取得
//================================
//商品IDのGETパラメータを取得
$s_id = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
//DBから商品データを取得
$viewData = getStoreOne($s_id);
//パラメータに不正な値が入っているかチェック
if(empty($viewData)){
  error_log('エラー発生:指定ページに不正な値が入りました。');
  header("Location:index.php");
}
debug('取得したDBデータ:'.print_r($viewData, true));

?>
<?php
  $siteTitle = 'おすすめ詳細';
  require('基本パーツ/head.php');
?>

<body class="page-productDetail page-1colum">
 <style>
      .badge{
        padding: 5px 10px;
        color: white;
        background: #7acee6;
        margin-right: 10px;
        font-size: 16px;
        vertical-align: middle;
        position: relative;
        top: -4px;
      }
      #main .title{
        font-size: 28px;
        padding: 10px 0;
      }
      .product-img-container{
        overflow: hidden;
      }
      .product-img-container img{
        width: 100%;
      }
      .product-img-container .img-main{
        width: 750px;
        height: 500px;
        float: left;
        padding-right: 15px;
        box-sizing: border-box;
      }
      .img-main img{
        width: 100%;
        height: 100%;
      }
      .product-img-container .img-sub{
        width: 230px;
        float: left;
        background: #f6f5f4;
        padding: 15px;
        box-sizing: border-box;
      }
      .product-img-container .img-sub:hover{
        cursor: pointer;
      }
      .product-img-container .img-sub img{
        margin-bottom: 15px;
      }
      .product-img-container .img-sub img:last-child{
        margin-bottom: 0;
      }
      .product-detail{
        background: #f6f5f4;
        padding: 15px;
        margin-top: 15px;
        min-height: 150px;
      }
      .product-buy{
        overflow: hidden;
        margin-top: 15px;
        margin-bottom: 50px;
        height: 50px;
        line-height: 50px;
      }
      .product-buy .item-left{
        float: left;
      }
      .product-buy .item-right{
        float: right;
      }
      .product-buy .price{
        font-size: 32px;
        margin-right: 30px;
      }
      .product-buy .btn{
        border: none;
        font-size: 18px;
        padding: 10px 30px;
      }
      .product-buy .btn:hover{
        cursor: pointer;
      }
      /*お気に入りアイコン*/
      .icn-like{
        float:right;
        color: #ddd;
      }
      .icn-like:hover{
        cursor: pointer;
      }
      .icn-like.active{
        float:right;
        color: #fe8a8b;
      }
      .aj{
        background-color: red !important;
      }
    </style>
  
<!-- ヘッダー -->
    <?php
      require('基本パーツ/header.php'); 
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

      <!-- Main -->
      <seiction id="main">
        <div class="title">
          <span class="badge"><?php echo sanitize($viewData['prefecture_name']); ?></span>
          <?php echo sanitize($viewData['name']); ?>
        </div>
        <div class="product-img-container">
          <div class="img-main">
            <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="メイン画像:<?php echo sanitize($viewData['name']); ?>" id="js-switch-img-main">
          </div>
          <div class="img-sub">
            <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="" class="js-switch-img-sub">
          </div>
        </div>
        <div class="product-detail">
          <p><?php echo sanitize($viewData['comment']); ?></p>
        </div>
        <div class="product-buy">
          <div class="item-left">
            <a href="index.php<?php appendGetParam(array('p_id')); ?>">&lt; おすすめ一覧に戻る</a>
          </div>
          <div class="item-right">
            <button data-storeid="<?php echo sanitize($viewData['id']); ?>" class="btn btn-primary js-click-like <?php if(isLike($_SESSION['user_id'], $viewData['id'])){ echo 'aj'; } ?>" style="margin-top:0;">お気に入り</button>
          </div>
        </div>
      </seiction>
    </div>
    
   <!--footer-->
   <?php
     require('基本パーツ/footer.php');
  ?>