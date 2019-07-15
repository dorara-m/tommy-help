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
        <form method="post" action="check_user_class.php">
            <br/>
            <!-- レンタルの数だけ貸出明細情報を表示-->
            <?php
            for($i=1;$i <= $rental->bookcount; $i++){
                print $i.",";
                print "図書番号:".$rental->{'rental'.$i}->bookcode.",";
                print "タイトル:".$rental->{'rental'.$i}->book->booktitle.",";
                print "期限:".$rental->{'rental'.$i}->schreturn.",";
                if($rental->{'rental'.$i}->exthistory == 0){
                    print "<input type=\"checkbox\" name=\"extends[]\" value=".$i.">".$i."を延長する";
                }
                print "<br/>";
            }
            $i = 1;
            while($i <= $_SESSION['availablerental']){
                print $i;
                print "<input name=\"".$i."\" type=\"text\" style=\"width:100px\"><br/>";
                $i++;
            }
            ?>


            <input name="url" type="hidden" value="confirm">
            <br/>
            <input type="submit" value="登録確認">
        </form>
    </body>
</html>