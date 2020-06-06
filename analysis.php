<?php
  session_start();
  //結果取得
  $results;
  if(isset($_GET['keyword'])){
    $keyword = $_GET['keyword'];
    $search_word = "%$keyword%";
    $table_name = $_SESSION['table_name'];
    $pdo = new PDO('pgsql:dbname=dcq9mmhagf14md host=ec2-3-222-30-53.compute-1.amazonaws.com port=5432','moeyszxjmvudsx','96786a380ccd8e1fc14824b34e77d4bb23193d42c740e8048902e554ee82e7d8');
    $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE profile LIKE ? ");
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
    <link rel="stylesheet" href="stylesheet.css">
	</head>
	<body>
    <header>
        <h1>フォロワーキーワード検索アプリ</h1>
    </header>
    <div class="container">
      <p>キーワード検索</p>
      <form  action="analysis.php" method="get" id="form">
        <input type="text" name="keyword" >
        <input class="submit" type="submit" name="submit" value="検索">
      </form>
      <?php if (isset($_GET['keyword'])) : ?>
        <p><?= $keyword ?>の検索結果 <?= $count ?>/<?= $_SESSION['followers_count']?></p>
        <table>
          <tr>
            <th></th>
            <th class="name">名前</th>
            <th>フォロー</th>
            <th>フォロワー</th>
          </tr>
         <?php foreach ($results as $result) : ?>
            <tr>
              <td><img src="<?= $result['icon'] ?>"></td>
              <td class="name"><a href="https://twitter.com/<?= $result['url'] ?>"　target=”_blank”><?= $result['name'] ?></a></td>
              <td><?= $result['friend'] ?></td>
              <td><?= $result['follower'] ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  <footer><p>Copyright (C) 2020 Ryo. all rights reserved.</p></footer>
  </body>
</html>
