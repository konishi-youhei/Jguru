<?php
  $siteTitle = 'HOME';
  require('基本パーツ/head.php');
?>

<body class="page-home">
  <style>
    header {
      position: fixed;
      top: 0px;
      left: 0px;
    }
    body{
      background-color: #000000;
    }
    #toppage-main {
      width: 1154px;
      margin: 0 auto;
      margin-bottom: 50px;
    }
    .main-img {
      width: 100%;
      height: 390px;
      margin-top: 100px;
      margin: 0 auto;
      position: relative;
    }
    .main-img-container {
      display: flex;
      width: 100vw;
      height: 300px;
      overflow: hidden;
      position: absolute;
      left: -400px;
    }
    .main-img-container img {
      width: 20%;
      height: 100%;
    }

    .top-text {
      margin-top: 200px;
    }
    h1 {
      color: #ffffff;
      text-align: center;
      font-size: 70px;
      margin: 100px;
    }
    .button {
      text-align: center;
    }
    button {
      padding: 10px 30px;
      background: #b6a489;
      border: none;
      border-radius: 3px;
    }
    button a {
      color: white;
      font-size: 20px;
      text-decoration: none;
    }
    button:hover {
      font-weight: bold;
      padding: 12px 32px;
    }
    .athlete_button {
      margin-top: 30px;

      text-align: center;
    }
    .athlete_button a {
      font-size: 20px;
    }
  </style>
  <!--ヘッダー-->
  <?php
    require('基本パーツ/header.php');
  ?>

  <!--メインコンテンツ-->
  <div class="background">
    <div id="toppage-main">
      <div class="top-text">
        <h1>好きな選手の好きな店</h1>
        <h1>サッカーの力で街を元気に</h1>
      </div>
      <div class="main-img">
        <div class="main-img-container">
          <img src="images/料理.jpg" alt="">
          <img src="images/羊.jpg" alt="">
          <img src="images/店.jpg" alt="">
          <img src="images/魚.jpg" alt="">
          <img src="images/フレンチ.jpg" alt="">
        </div>
      </div>
      <div class="button">
        <button><a href="index.php">早速探す</a></button>
      </div>
      <div class="athlete_button">
        <a href="athleteUserLogin.php">アスリート専用ページはこちら</a>
      </div>
    </div>
  </div>

  <!--フッター-->
  <?php
    require('基本パーツ/footer.php');
  ?>
