<header>
  <div class="site-width">
    <?php
      if(empty($_SESSION['user_id'])){
    ?>
    <h1><a href="toppage.php">Jグル</a></h1>
    <?php
      }else{
    ?>
        <h1><a href="index.php">Jグル</a></h1>
    <?php
      }
    ?>
    <nav id="top-nav">
      <ul>
        <?php
          if(empty($_SESSION['user_id'])){
        ?>
            <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
            <li><a href="login.php">ログイン</a></li>
        <?php
          }else{
        ?>
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="mypage.php">マイページ</a>
        <?php
          }
        ?>
      </ul>
    </nav>
  </div>
</header>
