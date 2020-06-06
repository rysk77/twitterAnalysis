<?php
// twitteroauthの読み込み
require "twitteroauth-master/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

//Twitterのコンシュマーキー(APIキー)等読み込み
define('TWITTER_API_KEY', 'vfg11QI936tPNaSAJZh823icE');
define('TWITTER_API_SECRET', 'JSYFMLanuYvI0ZwyiacoVzp1P1QlFoEZZNqs2X7deIyFY1gc1Y');

session_start();

//リクエストトークンを使い、アクセストークンを取得する
$twitter_connect = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);
$access_token = $twitter_connect->oauth('oauth/access_token', array('oauth_verifier' => $_GET['oauth_verifier'], 'oauth_token'=> $_GET['oauth_token']));


//アクセストークンからユーザの情報を取得する
$user_connect = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$user_info = $user_connect->get('account/verify_credentials');//アカウントの有効性を確認するためのエンドポイント
$_SESSION['followers_count'] = $user_info->followers_count;


//ユーザーネームをテーブル名に設定
$table_name = $user_info->screen_name;
$_SESSION['table_name'] = $table_name;


//テーブルチェック
$pdo = new PDO('pgsql:dbname=dcq9mmhagf14md host=ec2-3-222-30-53.compute-1.amazonaws.com port=5432','moeyszxjmvudsx','96786a380ccd8e1fc14824b34e77d4bb23193d42c740e8048902e554ee82e7d8');
$sql = "select * from '"$table_name"' ";
$stmt = $pdo->query($sql);
if($stmt != false){
  $sql = "delete from '"$table_name"' ";
  $pdo->query($sql);
}
print_r($pdo->errorInfo());
//テーブルを作成　
$sql = "CREATE TABLE '"$table_name"' (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50),
        profile VARCHAR(200) ,
        friend INT(11),
        follower INT(11),
        icon TEXT,
        url TEXT
)";
$pdo->query($sql);
print_r($pdo->errorInfo());

$pdo = null;

//フォロワー情報取得準備
$params = [
    'cursor' => '-1',
    'count' => '200',
    'skip_status' => 'true',
];
$followers = [];
$flag = true;

//フォロワー情報取得
do {
    $response = $user_connect->get('followers/list', $params);
    if (!isset($response->users)) {
        echo 'TwitterAPIの制限がかかっちゃってる！ごめんなさい！' . PHP_EOL;
        $flag = false;
        break;
    }
    $followers = array_merge($followers, $response->users);
} while ($params['cursor'] = $response->next_cursor_str);

//DBにユーザー情報格納
$pdo = new PDO('pgsql:dbname=dcq9mmhagf14md host=ec2-3-222-30-53.compute-1.amazonaws.com port=5432','moeyszxjmvudsx','96786a380ccd8e1fc14824b34e77d4bb23193d42c740e8048902e554ee82e7d8');
$pdo->beginTransaction();
for($i=0; $i<count($followers); $i++){
  $name    = $followers[$i]->{'name'};
  $name    = str_replace(array("'","’"), '', $name);//シングルクオートを取り除く
  $profile = $followers[$i]->{'description'};
  $profile = str_replace(array("'","’"), '', $profile);//シングルクオートを取り除く
  $friend  = $followers[$i]->{'friends_count'};
  $fan     = $followers[$i]->{'followers_count'};
  $icon    = $followers[$i]->{'profile_image_url'};
  $url     = $followers[$i]->{'screen_name'};
  $stmt    = $pdo->prepare( "INSERT INTO '"$table_name"'(
                            	name, profile, friend, follower, icon, url)
                              VALUES ('$name', '$profile', '$friend', '$fan', '$icon', '$url')"
                            );
  $stmt->execute();
}
$pdo->commit();
print_r($pdo->errorInfo());

$pdo = null;
?>
