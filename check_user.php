<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PHP基礎</title>
    </head>
    <body>
        <?php
        $usercode=$_POST['usercode'];
        if($usercode==''){  //1.1.1入力の有無のチェック
            print '会員IDが入力されていません';
            print '<form>';
            print '<input type="button" onclick="history.back()" value="戻る">';
            print '</form>';
            exit(1);
        } else {   //1.1.2 会員情報の取得
        $dsn = 'mysql:dbname=book;host=localhost';
        $user = 'root';
        $password = '';
        $dbh = new PDO($dsn, $user, $password);
        $dbh->query('SET NAMES utf8');

        $sql = 'SELECT * FROM user WHERE usercode='.$usercode;
        $stmt = $dbh->prepare($sql);
        $data[]=$usercode;
        $stmt->execute($data);
        //while(1)
        //{
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);//ここまで
            if($rec==false) //1.1.3 会員情報登録の有無をチェック
            {
                print '会員IDが存在しません';
                print '<form>';
                print '<input type="button" onclick="history.back()" value="戻る">';
                print '</form>';
                exit(1);
            }
            print $rec['usercode'];
            print $rec['name'];
            print $rec['number'];
            print $rec['rank'];
            print '<br />';
        //}
        $dbh = null;
        }

        ?>
    </body>
</html>