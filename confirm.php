<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>図書貸し出し</title>
    </head>
    <body>
        <?php
        require_once('class.php');
        session_start();
        print '<a href="logout.php">ログアウト</a><br/>';
        if(!isset($_SESSION['login'])){
        print 'ログインしなおしてください';
        print '<form>';
        print '<input type="button" onclick="history.back()" value="戻る">';
        print '</form>';
        exit(1);
        }
        $user = unserialize($_SESSION['user']);
        $rental = unserialize($_SESSION['rental']);
        $confirm = $_SESSION['confirm'];
        $confirmnew = $_SESSION['confirmnew'];
        print var_dump($confirm);
        print var_dump($confirmnew);
        ?>
        <form action="check_user_class.php" method="post">
          <input name="url" type="hidden" value="complete">
          <input type="submit" value="このIDで検索する" class="c-submit">
        </form>
    </body>
</html>