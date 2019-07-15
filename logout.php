<?php
session_start();
$_SESSION=array();
if(isset($_COOKIE[session_name()])==true)
{
    setcookie(session_name(), '', time()-42000,'/');
}
session_destroy();
header('Location: input.html');
?>
<!DOCTYPE html PUBLIC "-W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PHP基礎</title>
    </head>
    <body>
        ログアウトしました<br/>
        <br/>
        <a href="index.html">ログイン画面へ</a>
    </body>
</html>