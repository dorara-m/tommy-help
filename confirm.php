<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>確認画面</title>
        <link rel="stylesheet" type="text/css" media="screen" href="./assets/main.css">
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
        //print var_dump($confirm);
        //print var_dump($confirmnew);
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
                        <?php
                        $html = "";
                        if(!empty($confirm)){
                            $html .= "<h2 class=\"c-title\">延長する図書</h2>";
                            $html .= "<table class=\"c-table\">";

                                $html .= "<tr>";
                                $html .= "<th>id</th>";
                                $html .= "<th>図書番号</th>";
                                $html .= "<th>タイトル</th>";
                                $html .= "<th>貸出期限</th>";
                                $html .= "</tr>";
                            
                            $i = 1;
                            foreach($confirm as $array){
                                $html .= "<tr>";
                                $html .= "<td>".$i."</td>";
                                $html .= "<td>".$array['rental']->bookcode."</td>";
                                $html .= "<td>".$array['rental']->book->booktitle."</td>";
                                $html .= "<td>".$array['rental']->schreturn."</td>";
                                $html .= "</tr>";
                                $i++;
                            }
                            print $html;
                            }
                            ?>
                        </table>
                    </section>
                    <section>
                        <?php
                        $html = "";
                        if(!empty($confirmnew)){
                            $html .= "<h2 class=\"c-title\">新規貸出の図書</h2>";
                            $html .= "<table class=\"c-table\">";
                                $html .= "<tr>";
                                $html .= "<th>id</th>";
                                $html .= "<th>図書番号</th>";
                                $html .= "<th>タイトル</th>";
                                $html .= "<th>貸出期限</th>";
                                $html .= "</tr>";
                                                     
                            $i = 1;
                            foreach($confirmnew as $array){
                                $html .= "<tr>";
                                $html .= "<td>".$i."</td>";
                                $html .= "<td>".$array['book']->bookcode."</td>";
                                $html .= "<td>".$array['book']->booktitle."</td>";
                                $html .= "<td>".$array['schreturn']."</td>";
                                $html .= "</tr>";
                                $i++;
                            }
                            $html .= "</table>";
                            print $html;
                            }
                            ?>
                        
                    </section>
                    <div class="confirm-area">
                        <p>以上の内容で登録しますか？</p>
                        <div class="btns">
                            <a href="./user.php" class="c-submit__disable">キャンセル</a>
                            <form action="check_user_class.php" method="post">
                                <input name="url" type="hidden" value="complete">
                                <input type="submit" value="登録" class="c-submit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>