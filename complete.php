<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>貸出し完了</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="./assets/main.css">
</head>
<body>
  <?php 
  require_once('class.php');
  session_start();
  $user = unserialize($_SESSION['user']);
  ?>
  <div class="l-wrap">
    <header class="l-header">
      <div class="l-container">
        <p>図書貸出システム</p>
      </div>
    </header>
    <div class="p-complete l-content">
      <div class="l-container">
        <p>登録を受け付けました。</p>
        <div class="btns">
          <form action="logout.php" method="post" class="btn">
            <input type="submit" value="終了する" class="c-submit__disable">
          </form>
          <form action="check_user_class.php" method="post">
            <input name="usercode" type="hidden" value="<?php print $user->usercode;?>">
            <input name="url" type="hidden" value="userinput">
            <input type="submit" value="続けて入力する" class="c-submit">
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>