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
            var error エラーメッセージ
            obj user Userオブジェクト
            obj rental rentalオブジェクト
        */




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
            //$_SESSION['user']=serialize($user);
            //
            print $user->usercode;
            print $user->name;
            print $user->number;
            print $user->rank;
            print '<br />'; 
            //
            
            $rental = Rental::create($usercode);
            if (is_null($rental)){//貸出情報が？通常うごかない
                print '貸出情報が存在しません';
                print '<form>';
                print '<input type="button" onclick="history.back()" value="戻る">';
                print '</form>';
                exit(1);            
            }
            //$_SESSION['rental']=serialize($rental);
            //ここからテスト
            print var_dump($rental);
            print '<br />'; 
            print $rental->rental1->chkout;
            print '<br />'; 
            print $rental->rental1->book->bookpub;
            //


            $date = date('Y-m-d');
            for($i = 1; $i <= $rental->bookcount; $i++){
                if(is_null($rental->{'rental'.$i})){//NULLオブジェクトの排除(つかわない)
                    continue;
                }

                if($rental->{'rental'.$i}->schreturn < $date ){//返却期限超過本の有無をチェック
                    print $i."番目の書籍が期限超過しています";
                    print '<br/>';
                }
                //if($rental->{'rental'.$i}->isreturn == FALSE){//貸出期間超過の確認//}

                if($rental->{'rental'.$i}->exthistory){
                    print $i."番目の書籍が延長できません";
                    print '<br/>';
                }

                if($user->rank == "学生"){//新規貸出可能な冊数を取得する
                    $availablerental = 6 - $rental->bookcount;
                }elseif($user->rank == "教員"){
                    $availablerental = 6 - $rental->bookcount;
                }


            }
            print $availablerental."冊貸出可能";
            print '<br/>';
        }
        class User
        {
            public $usercode;
            public $name;
            public $number;
            public $email;
            public $tel;
            public $rank;
            final private function __construct() {}

            /**
             * インスタンスの生成
             * @param int $usercode
             * @return self|null
             */
            final public static function create($usercode)
            {
                // 自分自身のインスタンス生成
                $instance = new self();
        
                // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
                return $instance->init($usercode) ? $instance : null;
            }
        
            /**
             * インスタンス初期化処理
             * @param int $usercode
             * @return bool
             */
            function init($usercode)
            {
                $dsn = 'mysql:dbname=book;host=localhost';
                $user = 'root';
                $password = '';
                $dbh = new PDO($dsn, $user, $password);
                $dbh->query('SET NAMES utf8');
        
                $sql = 'SELECT * FROM user WHERE usercode='.$usercode;
                $stmt = $dbh->prepare($sql);
                //$data[]=$usercode;
                //$stmt->execute($data);
                $stmt->execute();
                //while(1)
                //{
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);//ここまで
                $dbh = null;
                if($rec==false) //1.1.3 会員情報登録の有無をチェック
                {
                    return false;
                }
                $usercode = $rec['usercode'];
                $name = $rec['name'];
                $number = $rec['number'];
                $email = $rec['email'];
                $tel = $rec['tel'];
                $rank = $rec['rank'];

                return true;
            }
        }
        class Rental
        {
            /*
            @param int usercode
            @return obj rental
            property:
                obj $book1~12;
                int $bookcount
            */
    
            final private function __construct() {}

            /**
             * インスタンスの生成
             * @param int $usercode
             * @return self|null
             */
            final public static function create($usercode)
            {
                // 自分自身のインスタンス生成
                $instance = new self();
        
                // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
                return $instance->init($usercode) ? $instance : null;
            }
        
            /**
             * インスタンス初期化処理
             * @param int $usercode
             * @return bool
             */
            function init($usercode)
            {
                //$book1 = RentalDetail::create($usercode, 1);
                $dsn = 'mysql:dbname=book;host=localhost';
                $user = 'root';
                $password = '';
                $dbh = new PDO($dsn, $user, $password);
                $dbh->query('SET NAMES utf8');
        
                $sql = 'SELECT rentalcode FROM rental WHERE (usercode='.$usercode .' AND isreturn=FALSE)';//会員IDかつ返却されてない書籍を取得（全件でなく貸出IDだけにする）
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                //貸出IDの取得
                $bookcount = 0;
                while($rec = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $bookcount++;
                    $rentalcode = $rec['rentalcode'];
                    ${'rental'.$bookcount} = RentalDetail::create($rentalcode);
                }//ここまで
                $dbh = null;


                /*貸出冊数分のインスタンスを作成（旧方式）
                for($i = 1; $i <=6; $i++){
                    ${'rental'.$i} = RentalDetail::create($usercode, $i);
                }
                */
                return true; ///大事
            }
        

        }

        class RentalDetail
        {
            /*
            @param int rentalcode
            @return obj rental*
            property:
            */
            public $chkout; //貸出日
            public $schreturn;//返却予定日
            public $extrental;//延長希望
            public $exthistory;//延長履歴
            public $return;//返却日
            public $bookcode;//図書ID
            public $book;//図書情報オブジェクト

            final private function __construct() {}

            /**
             * インスタンスの生成
             * @param int $rentalcode
             * @return self|null
             */
            final public static function create($rentalcode)
            {
                // 自分自身のインスタンス生成
                $instance = new self();
        
                // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
                return $instance->init($rentalcode) ? $instance : null;
            }
        
            /**
             * インスタンス初期化処理
             * @param int $rentalcode
             * @return bool
             */
            function init($rentalcode)
            {
                $dsn = 'mysql:dbname=book;host=localhost';
                $user = 'root';
                $password = '';
                $dbh = new PDO($dsn, $user, $password);
                $dbh->query('SET NAMES utf8');
        
                $sql = 'SELECT * FROM rental WHERE rentalcode='.$rentalcode;
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);//ここまで
                $dbh = null;
                if($rec==false) //1.1.3 貸出明細登録の有無をチェック
                {
                    return false;
                }

                $chkout = $rec['checkoutdate'];
                $schreturn = $rec['scheduledreturndate'];
                $extrental = $rec['extrental'];
                $exthistory = $rec['exthistory'];
                $return = $rec['returndate'];
                $bookcode = $rec['bookcode'];
                
                $book = Book::create($bookcode);
                

                //$bookinstance = Book::create($bookcode);仕様外

                return true;
            }
        }
        class Book
        {
            /*
            @param int bookcode
            @return obj book
            property:
            */
            public $bookcode;//int
            public $bookstat;//boolean
            public $bookpurchase;//date
            public $bookcatalogcode;//int
            //↓図書カタログ
            public $booktitle;//str
            public $bookISBN;//int
            public $bookauthor;//str
            public $bookpub;//str
            public $bookpubdate;//date
            public $booktype;//int
            
            //getter setter(privateの場合)

            /**
             * newでのインスタンス生成を禁止
             */
            final private function __construct() {}

            /**
             * インスタンスの生成
             * @param int $bookcode 
             * @return self|null
             */
            final public static function create($bookcode)
            {
                // 自分自身のインスタンス生成
                $instance = new self();

                // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
                return $instance->init($bookcode) ? $instance : null;
            }

            /**
             * インスタンス初期化処理
             * @param int $bookcode
             * @return bool
             */
            private function init($bookcode)
            {
                $dsn = 'mysql:dbname=book;host=localhost';
                $user = 'root';
                $password = '';
                $dbh = new PDO($dsn, $user, $password);
                $dbh->query('SET NAMES utf8');
        
                $sql = 'SELECT * FROM book WHERE bookcode='.$bookcode;
                /////////////////複合主キーの問い合わせ
                $stmt = $dbh->prepare($sql);
                //$data[]=$usercode;
                $stmt->execute();
                //while(1)
                //{
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);//ここまで
                $dbh = null;
                if($rec==false) //1.1.3 貸出明細登録の有無をチェック
                {
                    return false;
                }
                $bookcode = $rec['bookcode'];
                $bookstat = $rec['bookstatus'];
                $bookpurchase = $rec['bookpurchase'];
                $bookcatalogcode = $rec['bookcatalogcode'];



                //図書カタログの取得

                $dsn = 'mysql:dbname=book;host=localhost';
                $user = 'root';
                $password = '';
                $dbh = new PDO($dsn, $user, $password);
                $dbh->query('SET NAMES utf8');
        
                $sql = 'SELECT * FROM bookcatalog WHERE bookcatalogcode='.$bookcatalogcode;
                /////////////////複合主キーの問い合わせ
                $stmt = $dbh->prepare($sql);
                //$data[]=$usercode;
                $stmt->execute();
                //while(1)
                //{
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);//ここまで
                $dbh = null;
                if($rec==false) //取得できなければ終了
                {
                    return false;
                }
                // インスタンス生成失敗条件

                $booktitle = $rec['title'];
                $bookISBN = $rec['ISBN'];
                $bookauthor = $rec['author'];
                $bookpub = $rec['publisher'];
                $bookpubdate = $rec['publishdate'];
                $booktype = $rec['type'];


                return true; // インスタンス生成成功
            }
        }





        class hoge
        {
            //インスタンス変数

            //getter setter(privateの場合)
            public function getNumber()
            {
                return $num;
            }

            /**
             * newでのインスタンス生成を禁止
             */
            final private function __construct() {}

            /**
             * インスタンスの生成
             * @param int $num 
             * @return self|null
             */
            final public static function create($num)
            {
                // 自分自身のインスタンス生成
                $instance = new self();

                // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
                return $instance->init($num) ? $instance : null;
            }

            /**
             * インスタンス初期化処理
             * @param int $num
             * @return bool
             */
            private function init($num)
            {
                // インスタンス生成失敗条件
                if (!is_numeric($num)){
                    return false;
                }

                return true; // インスタンス生成成功
            }
        }
        /*
        メイン部分
        @param int $_POST['url']
            URLの転送元を示す 1:index.html 2:user.html
        @param int $_POST['usercode']
            会員IDを表す
        */
        //session_start();
        $usercode=$_POST['usercode'];
        $url=$_POST['url'];
        if($url == 'userinput'){
            $_SESSION['login']=1;
            sequence1($usercode);
            //header('Location: user.html');
        }
        //リダイレクトにはheader();を使う

        ?>
    </body>
</html>