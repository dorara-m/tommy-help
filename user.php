<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>貸出画面</title>
        <link rel="stylesheet" type="text/css" media="screen" href="./assets/main.css">
    </head>
    <body>
        <div class="l-wrap">
            <header class="l-header">
                <div class="l-container">
                    <p>図書貸出システム</p>
                </div>
            </header>

            <?php
            require_once('class.php');
            session_start();
            if(!isset($_SESSION['login'])){
                print 'ログインしなおしてください';
                print '<form>';
                print '<input type="button" onclick="history.back()" value="戻る">';
                print '</form>';
                exit(1);
            }
            if(isset($_SESSION['error'])){
                print $_SESSION['error'];
                print '<form>';
                print '<input type="button" onclick="history.back()" value="戻る">';
                print '</form>';
                exit(1);
            }
            if(isset($_SESSION['caution'])){
                print $_SESSION['caution'];
                print '<br/>';
                //print var_dump($_SESSION['rec']);
            }
            $user = unserialize($_SESSION['user']);
            $rental = unserialize($_SESSION['rental']);
            /*テスト
            print var_dump($rental);
            print '<br />'; 
            print $rental->rental1->chkout;
            print '<br />'; 
            print $rental->rental1->book->bookpub;
            */


            ?>

            <!-- pageContents -->
            <div class="p-user l-content">
                <div class="l-container">
                    <h1><?php print $user->name ?>さんの貸出画面</h1>
                    <form method="post" action="check_user_class.php">
                        <h2>貸出済み</h2>
                        <!-- レンタルの数だけ貸出明細情報を表示-->
                        <table>
                            <tr>
                                <th>id</th>
                                <th>図書番号</th>
                                <th>タイトル</th>
                                <th>貸出期限</th>
                                <th>延長</th>
                            </tr>
                            <?php
                                for($i=1;$i <= $rental->bookcount; $i++){
                                    $html = "";
                                    $html .= "<tr>";
                                    $html .= "<td>".$i."</td>";
                                    $html .= "<td>".$rental->{'rental'.$i}->bookcode."</td>";
                                    $html .= "<td>".$rental->{'rental'.$i}->book->booktitle."</td>";
                                    $html .= "<td>".$rental->{'rental'.$i}->schreturn."</td>";
                                    if($rental->{'rental'.$i}->exthistory == 0){
                                        $html .= "<td><label><input type=\"checkbox\" name=\"extends[]\" value=".$i.">".$i."を延長する</label></td>";
                                    }
                                    $html .= "</tr>";
                                }
                                print $html;
                            ?>
                        </table>

                        <h2>新規に貸出</h2>
                        <?php
                            $i = 1;
                            while($i <= $_SESSION['availablerental']){                            
                                print $i;
                                print "<input name=\"".$i."\" type=\"text\">";
                                $i++;
                            }
                        ?>


                        <input name="url" type="hidden" value="confirm">
                        <input type="submit" value="この内容で登録" class="c-submit">
                    </form>
                    
                    <div class="logout">
                        <a href="logout.php">検索画面に戻る</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>