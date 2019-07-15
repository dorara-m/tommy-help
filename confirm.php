<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>確認画面</title>
        <link rel="stylesheet" type="text/css" media="screen" href="./assets/main.css">
    </head>
    <body>
        <?php
        // require_once('class.php');
        // session_start();
        // print '<a href="logout.php">ログアウト</a><br/>';
        // if(!isset($_SESSION['login'])){
        // print 'ログインしなおしてください';
        // print '<form>';
        // print '<input type="button" onclick="history.back()" value="戻る">';
        // print '</form>';
        // exit(1);
        // }
        // $user = unserialize($_SESSION['user']);
        // $rental = unserialize($_SESSION['rental']);
        // $confirm = $_SESSION['confirm'];
        // print var_dump($confirm);
        ?>
        <div class="l-wrap">
            <header class="l-header">
                <div class="l-container">
                    <p>図書貸出システム</p>
                </div>
            </header>
            <div class="p-confirm l-content">
                <div class="l-container">
                    <section>
                        <h2 class="c-title">延長する図書</h2>
                        <table class="c-table">
                            <tr>
                                <th>id</th>
                                <th>図書番号</th>
                                <th>タイトル</th>
                                <th>貸出期限</th>
                            </tr>
                            <!-- 以下、trごとループする -->
                            <tr>
                                <td>1</td>
                                <td>10002</td>
                                <td>進撃の巨人 1巻</td>
                                <td>2019-01-01</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>10002</td>
                                <td>進撃の巨人 1巻</td>
                                <td>2019-01-01</td>
                            </tr>
                        </table>
                    </section>
                    <section>
                        <h2 class="c-title">新規貸出の図書</h2>
                        <table class="c-table">
                            <tr>
                                <th>id</th>
                                <th>図書番号</th>
                                <th>タイトル</th>
                                <th>貸出期限</th>
                            </tr>
                            <!-- 以下、trごとループする -->
                            <tr>
                                <td>1</td>
                                <td>10002</td>
                                <td>進撃の巨人 1巻</td>
                                <td>2019-01-01</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>10002</td>
                                <td>進撃の巨人 1巻</td>
                                <td>2019-01-01</td>
                            </tr>
                        </table>
                    </section>
                    <div class="confirm-area">
                        <p>以上の内容で送信しますか？</p>
                        <div class="btns">
                            <a href="./user.php" class="c-submit__disable">キャンセル</a>
                            <form action="">
                                <input type="submit" value="送信" class="c-submit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>