<?php
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
        $this->usercode = $rec['usercode'];
        $this->name = $rec['name'];
        $this->number = $rec['number'];
        $this->email = $rec['email'];
        $this->tel = $rec['tel'];
        $this->rank = $rec['rank'];

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
        //$this->book1 = RentalDetail::create($usercode, 1);
        $dsn = 'mysql:dbname=book;host=localhost';
        $user = 'root';
        $password = '';
        $dbh = new PDO($dsn, $user, $password);
        $dbh->query('SET NAMES utf8');

        $sql = 'SELECT rentalcode FROM rental WHERE (usercode='.$usercode .' AND isreturn=FALSE)';//会員IDかつ返却されてない書籍を取得（全件でなく貸出IDだけにする）
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        //貸出IDの取得
        $this->bookcount = 0;
        while($rec = $stmt->fetch(PDO::FETCH_ASSOC)){
            $this->bookcount++;
            $rentalcode = $rec['rentalcode'];
            $this->{'rental'.$this->bookcount} = RentalDetail::create($rentalcode);
        }//ここまで
        $dbh = null;


        /*貸出冊数分のインスタンスを作成（旧方式）
        for($i = 1; $i <=6; $i++){
            $this->{'rental'.$i} = RentalDetail::create($usercode, $i);
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

            $this->chkout = $rec['checkoutdate'];
            $this->schreturn = $rec['scheduledreturndate'];
            $this->extrental = $rec['extrental'];
            $this->exthistory = $rec['exthistory'];
            $this->return = $rec['returndate'];
            $this->bookcode = $rec['bookcode'];
            
            $this->book = Book::create($this->bookcode);
            

            //$this->bookinstance = Book::create($bookcode);仕様外

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
    //↓図書カタログ情報
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
        $this->bookcode = $rec['bookcode'];
        $this->bookstat = $rec['bookstatus'];
        $this->bookpurchase = $rec['bookpurchase'];
        $this->bookcatalogcode = $rec['bookcatalogcode'];



        //図書カタログの取得

        $dsn = 'mysql:dbname=book;host=localhost';
        $user = 'root';
        $password = '';
        $dbh = new PDO($dsn, $user, $password);
        $dbh->query('SET NAMES utf8');

        $sql = 'SELECT * FROM bookcatalog WHERE bookcatalogcode='.$this->bookcatalogcode;
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

        $this->booktitle = $rec['title'];
        $this->bookISBN = $rec['ISBN'];
        $this->bookauthor = $rec['author'];
        $this->bookpub = $rec['publisher'];
        $this->bookpubdate = $rec['publishdate'];
        $this->booktype = $rec['type'];


        return true; // インスタンス生成成功
    }
}

    /**
     * インスタンスの生成
     * @param int $bookcode 
     * @return self|null
     */
class Confirm
{
    //インスタンス変数
    //getter setter(privateの場合)
    public $schreturn;
    public function createRentalDetail($i, $rentalcode)
    {
        $this->{'rental'.$i} = RentalDetail::create($rentalcode);
    }

    public function createBook($i, $bookcode)
    {
        $this->{'book'.$i} = Book::create($bookcode);
    }
    /*
    public function checkrental($bookcode)
    {
        $dsn = 'mysql:dbname=book;host=localhost';
        $user = 'root';
        $password = '';
        $dbh = new PDO($dsn, $user, $password);
        $dbh->query('SET NAMES utf8');

        $sql = 'SELECT * FROM rental WHERE (bookcode='.$bookcode .' AND isreturn=FALSE)'
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);//ここまで
        $dbh = null;
        if($rec==true) //1.1.3 貸出明細登録の有無をチェック
        {
            return false;
        }
        return true;
    }
    */
    /**
     * newでのインスタンス生成を禁止
     */
    final private function __construct() {}

    /**
     * インスタンスの生成
     * @param int $num 
     * @return self|null
     */
    final public static function create()
    {
        // 自分自身のインスタンス生成
        $instance = new self();

        // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
        return $instance->init() ? $instance : null;
    }

    /**
     * インスタンス初期化処理
     * @param int $num
     * @return bool
     */
    private function init()
    {
        // インスタンス生成失敗条件

        return true; // インスタンス生成成功
    }
}


class Reservation
{
    //インスタンス変数
    //getter setter(privateの場合)
    public $rec;

    function checkusercode($usercode){
        $_SESSION['rec'] = $this->rec;
        foreach($this->rec as $row){
            if($row['usercode'] == $usercode){
                return $row;
            }
        }
        return null;
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
    final public static function create($bookcatalogcode)
    {
        // 自分自身のインスタンス生成
        $instance = new self();

        // インスタンス初期化メソッド呼び出し、失敗時はnullを返す
        return $instance->init($bookcatalogcode) ? $instance : null;
    }

    /**
     * インスタンス初期化処理
     * @param int $num
     * @return bool
     */
    private function init($bookcatalogcode)
    {
        $dsn = 'mysql:dbname=book;host=localhost';
        $user = 'root';
        $password = '';
        $dbh = new PDO($dsn, $user, $password);
        $dbh->query('SET NAMES utf8');

        $sql = 'SELECT * FROM reservation WHERE bookcatalogcode='.$bookcatalogcode .' AND informreservationdate IS NOT NULL AND isavailable=TRUE';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $this->rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //ここまで
        $dbh = null;
        // インスタンス生成失敗条件
        if(empty($this->rec)){
            return false;
        }
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

?>