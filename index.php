<?php
// twitteroauth の読み込み
require "twitteroauth-master/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

//Twitterのコンシュマーキー(APIキー)等読み込み
define('TWITTER_API_KEY', 'vfg11QI936tPNaSAJZh823icE');
define('TWITTER_API_SECRET', 'JSYFMLanuYvI0ZwyiacoVzp1P1QlFoEZZNqs2X7deIyFY1gc1Y');
$access_token = '1229996056600952834-8gloY3Zrs3CPpVJth2oI5xElxQ1U14';
$access_token_secret = 'HdErux4o5IljBl2V7exoI9Zoa0PLQdXU1vhMm2C5m9PGs';

//コールバックページのURL
define('CALLBACK_URL', 'https://follwerkeywordsearch.herokuapp.com/callback.php');


//「abraham/twitteroauth」ライブラリのインスタンスを生成し、Twitterからリクエストトークンを取得する
$connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $access_token, $access_token_secret);
$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => CALLBACK_URL));

//リクエストトークンはコールバックページでも利用するためセッションに格納しておく
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

//Twitterの認証画面のURL
$oauthUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
	<title>login</title>
	<meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="stylesheet.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
	</head>
	<body>
    <header>
        <h1>フォロワーキーワード検索アプリ</h1>
    </header>
    <div class="container">
      <p>検索したキーワードがプロフィールに含まれるフォロワー一覧を表示します</p>
  		<a href="<?php echo $oauthUrl; ?>" ><div class="btn"><i class="fab fa-twitter"></i>Twitterでログイン</div></a>
    </div>
  </body>
  <footer><p>Copyright (C) 2020 Ryo. all rights reserved.</p></footer>
</html>
