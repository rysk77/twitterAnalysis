<?php
  session_start();
  //結果取得
  $results;
  if(isset($_GET['keyword'])){
    $keyword = $_GET['keyword'];
    $search_word = "%$keyword%";
    $table_name = $_SESSION['table_name'];
    $pdo = new PDO('mysql:host=localhost;dbname=followerAnalysis;charset=utf8','aaa','aaa');
    $stmt = $pdo->prepare("SELECT * from $table_name WHERE profile LIKE ? ");
    $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->rowCount();
    $results = $stmt->fetchAll();
    $pdo = null;
  }

 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	  <title>login</title>
	</head>
	<body>
    <h1>キーワード検索画面</h1>
    <p>プロフィール文にキーワードが含まれるフォロワーを表示します。</p>
    <form class="" action="analysis.php" method="get" id="form">
      <input type="text" name="keyword"　placeholder="検索キーワード" >
      <input type="submit" id="submit"　name="submit" value="検索">
    </form>
    <?php if (isset($_GET['keyword'])) : ?>
      <p><?= $keyword ?>の検索結果 <?= $count ?>/<?= $_SESSION['followers_count']?></p>
      <table>
        <tr>
          <th>アイコン</th>
          <th>名前</th>
          <th>フォロー</th>
          <th>フォロワー</th>
        </tr>
       <?php foreach ($results as $result) : ?>
          <tr>
            <th><img src="<?= $result['icon'] ?>"></th>
            <th><?= $result['name'] ?></th>
            <th><?= $result['friend'] ?></th>
            <th><?= $result['follower'] ?></th>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  <script type="text/javascript">
    //入力チェック
    $("#form").submit(function(){
      if ($("input[name='keyword']").val() == '') {
      alert('キーワードを入力してください');
      return false;
      } else {
        $("#form").submit();
      }
    });
  </script>
  </body>
</html>
