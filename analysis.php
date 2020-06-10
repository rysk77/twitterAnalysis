<?php
  session_start();
  //結果取得
  $results;
  if(isset($_POST['keyword'])){
    //キーワード取得
    $keyword = $_POST['keyword'];
    $search_word = "%$keyword%";
    //SQL準備
    //ソート指定
    $sort = $_POST['sort'];
    switch ($sort) {
      case 1:
        $order = "\"follower\" DESC";
        break;
      case 2:
        $order = "\"follower\" ASC";
        break;
      case 3:
        $order = "\"friend\" DESC";
        break;
      case 4:
        $order = "\"riend\" ASC";
        break;
    }
    $table_name = $_SESSION['table_name'];
    $pdo = new PDO('pgsql:dbname=dcq9mmhagf14md host=ec2-3-222-30-53.compute-1.amazonaws.com port=5432','moeyszxjmvudsx','96786a380ccd8e1fc14824b34e77d4bb23193d42c740e8048902e554ee82e7d8');
    //検索条件指定
    $target = $_POST['target'];
    switch ($target) {
      case 1:
        $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE names LIKE ? ORDER BY $order ");
        $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
        break;
      case 2:
        $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE follower LIKE ? ORDER BY $order ");
        $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
        break;
      case 3:
        $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE name LIKE ? OR WHERE follower LIKE ? ORDER BY $order ");
        $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
        $stmt->bindParam(2, $search_word, PDO::PARAM_STR);
        break;
    }
    //SQL実行
    $stmt->execute();
    $count = $stmt->rowCount();
    $results = $stmt->fetchAll();
    $error = $pdo->errorInfo();
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
    <p><?= $error ?></p>
    <div class="container">
      <p>キーワード検索</p>
      <form  action="analysis.php" method="post" id="form">
        <input type="text" name="keyword" >
        <input class="submit" type="submit" name="submit" value="検索">
        <p>検索条件</p>
        <select  name="sort">
          <option value="1">フォロワー数が多い順</option>
          <option value="2">フォロワー数が少ない順</option>
          <option value="3">フォロー数が多い順</option>
          <option value="4">フォロー数が少ない順</option>
        </select>
        <br>
        <select  name="target">
          <option value="1">名前にキーワードが含まれる</option>
          <option value="2">プロフィールにキーワードが含まれる</option>
          <option value="3">名前かプロフィールにキーワードが含まれる</option>
        </select>
      </form>
      <?php if (isset($_POST['keyword'])) : ?>
        <p><?= $keyword ?>の検索結果 <?= $count ?>/<?= $_SESSION['followers_count']?>(<?= round($count/$_SESSION['followers_count']*100) ?>%)</p>
        <table>
          <tr>
            <th></th>
            <th class="name">名前</th>
            <th>フォロー</th>
            <th>フォロワー</th>
          </tr>
         <?php foreach ($results as $result) : ?>
            <tr>
              <td><img src="<?= $error ?>"></td>
              <td class="name"><a href="https://twitter.com/<?= $result['url'] ?>" target="_blank" rel="noopener noreferrer"><?= $result['name'] ?></a></td>
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
