<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PHP基礎</title>
    </head>
    <body>
        <?php
        /*
        session配列
            boo login ログインの有無
            str error エラーメッセージ
            str caution 注意メッセージ
            obj user Userオブジェクト
            obj rental rentalオブジェクト
            int availablerental 貸出可能冊数
        */


        require_once('class.php');

        function sequence1($usercode){

            if($usercode==''){  //1.1.1入力の有無のチェック
                $_SESSION['error']='会員IDが入力されていません';
                exit(1);
            }
            
            //1.1.2 会員情報の取得
            $user = User::create($usercode);
            if (is_null($user)){
                $_SESSION['error']='会員IDが存在しません';
                exit(1);            
            }
            $_SESSION['user']=serialize($user);
            /*
            print $user->usercode;
            print $user->name;
            print $user->number;
            print $user->rank;
            print '<br />'; 
            */
            
            $rental = Rental::create($usercode);
            if (is_null($rental)){//貸出情報が？通常うごかない
                print '貸出情報が存在しません';
                print '<form>';
                print '<input type="button" onclick="history.back()" value="戻る">';
                print '</form>';
                exit(1);            
            }
            $_SESSION['rental']=serialize($rental);
            /*ここからテスト
            print var_dump($rental);
            print '<br />'; 
            print $rental->rental1->chkout;
            print '<br />'; 
            print $rental->rental1->book->bookpub;
            */


            $date = date('Y-m-d');
            for($i = 1; $i <= $rental->bookcount; $i++){
                if(is_null($rental->{'rental'.$i})){//NULLオブジェクトの排除(つかわない)
                    continue;
                }

                if($rental->{'rental'.$i}->schreturn < $date ){//返却期限超過本の有無をチェック
                    $_SESSION['error'].= "書籍が返却期限を超過しています<br/>
                    タイトル:".$rental->{'rental'.$i}->book->booktitle."<br/>
                    返却予定日:".$rental->{'rental'.$i}->schreturn."<br/>";

                }
                //if($rental->{'rental'.$i}->isreturn == FALSE){//貸出期間超過の確認//}

                if($rental->{'rental'.$i}->exthistory){
                    print $i."番目の書籍が延長できません";
                    print '<br/>';
                }


            }
            if($user->rank == "学生"){//新規貸出可能な冊数を取得する
                $availablerental = 6 - $rental->bookcount;
            }elseif($user->rank == "教員"){
                $availablerental = 12 - $rental->bookcount;
            }
            if($availablerental==0){
                $_SESSION['caution'] .= "貸出上限に達しています";
            }
            $_SESSION['availablerental'] = $availablerental;

        }

        function sequence2(){
            //延長貸出
            $user = unserialize($_SESSION['user']);
            $rental = unserialize($_SESSION['rental']);
            $_SESSION['caution'] = '';
            $confirm = array();

            $extends = array();
            $number = array();
            if (isset($_POST['extends']) && is_array($_POST['extends'])) {
                $_POST['extends'] = $extends;
            }
            for($i=1; $i <= 12; $i++){
                if(isset($_POST[$i]) && !($_POST[$i] == ""))
                $number[$i] = $_POST[$i];
            }
            print var_dump($number);
            //2.1.1 貸出明細情報の取得 rental->rental*
            if (isset($extends)) {
                foreach( $extends as $i ){
                    //print "{$value}, ";
            //2.1.2 延長期間を算出
                    //print $i;
                    $confirm[$i]['rental'] = $rental->{'rental'.$i};
                    $confirm[$i]['rental']->schreturn = date("Y-m-d",strtotime($rental->{'rental'.$i}->schreturn . "+3 week"));
                }
              }
            

            //新規貸し出し
            //2.1.3 図書IDの入力有無をチェック
            $chkcode = 0;

            for($i=$rental->bookcount+1;$i <= $_SESSION['availablerental'];$i++){
                if(isset($number)){
                    if($number[$i] == $chkcode){
                        $_SESSION['caution'] .= "IDが重複しています";
                        header('Location: user.php');
                        exit(1);
                    }
                    $chkcode=$number[$i];
                }
            }
            if($chkcode = 0){//入力がなかった場合処理を止める
                if(!isset($extends)){
                    $_SESSION['caution'] .= "入力がありません"; 
                    header('Location: user.php');
                    exit(1);
                }
                //$_SESSION['confirm'] = $confirm;
                //header('Location: confirm.php');
            }
            //print $_SESSION['availablerental'];
            //print '新規貸出図書開始<br/>';
            //print var_dump($number);
            //2.1.4 図書情報の取得
            for($i=$rental->bookcount+1;$i <= $rental->bookcount+$_SESSION['availablerental'];$i++){
                //$_SESSION['caution'] .= $number[$i];
                if(!isset($number[$i])){
                    continue;
                }
                $bookcode = $number[$i];
                //$book = Book::create($bookcode);
                $confirm[$i]['book'] = Book::create($bookcode);
                //$_SESSION["newbook{$i}"]=serialize(${'newbook'.$i});

            //2.1.5 図書情報登録の有無をチェック
                if(is_null($confirm[$i]['book'])){
                    $_SESSION['caution'] .= "#".$i."指定された図書IDは存在しません"; 
                    header('Location: user.php');
                    continue;
                }
            //2.1.6 貸出可能な図書かチェック 学生の場合は雑誌は不可
                if($user->rank == "学生" && $confirm[$i]['book']->booktype == 1){
                    $_SESSION['caution'] .= "#".$i."学生会員は雑誌の貸し出しができません"; 
                    header('Location: user.php');
                    continue;
                }
            //2.1.6.2 貸出中の図書か確認
                if($confirm[$i]['book']->bookstat){
                    $_SESSION['caution'] .= "#".$i."指定された図書は貸出中です"; 
                    header('Location: user.php');
                    continue;
                }
            //2.1.7 予約情報の取得　
            //print $confirm[$i]['book']->bookcatalogcode;
                $reservation = Reservation::create($confirm[$i]['book']->bookcatalogcode);
            //2.1.8 予約が適正かチェック
                //予約情報が存在する場合
                if(!empty($reservation)){
                    //会員IDに該当するレコードをconfirm配列に代入
                    $confirm[$i]['reservation'] = $reservation->checkusercode($user->usercode);
                    //該当するレコードが存在しない場合、エラーを表示
                    if(empty($confirm[$i]['reservation'])){
                        $_SESSION['caution'] .= "#".$i."指定された図書は他の人が予約中です"; 
                        header('Location: user.php');
                        continue;
                    }
                }
            //2.1.9 返却予定日を設定
                $confirm[$i]['schreturn'] = date("Y-m-d",strtotime("+3 week"));
            
            }

            $_SESSION['confirm'] = $confirm;
            //print var_dump($confirm);
            //print $_SESSION['caution'];
            //header('Location: confirm.php');



        }

        function sequence3(){



            if(isset(${'reservation'.$i}->usercode)){
                ${'reservation'.$i}::Deleteresv();
            }
            
        }
        
        /*
        メイン部分
        @param int $_POST['url']
            URLの転送元を示す 1:index.html 2:user.html
        @param int $_POST['usercode']
            会員IDを表す
        */
        session_start();

        $url=$_POST['url'];
        if($url == 'userinput'){
            $usercode=$_POST['usercode'];
            $_SESSION['login']=1;
            sequence1($usercode);
            header('Location: user.php');
        }elseif($url == 'confirm'){


            //print var_dump($number);
            sequence2();
        }
        //リダイレクトにはheader();を使う

        ?>
    </body>
</html>