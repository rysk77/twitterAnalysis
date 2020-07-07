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
    switch ($_POST['sort']) {
      case "a":
        $column = "follower";
        $order  = "DESC";
        break;
      case "b":
        $column = "follower";
        $order  = "ASC";
        break;
      case "c":
        $column = "friend";
        $order  = "DESC";
        break;
      case "d":
        $column = "friend";
        $order  = "ASC";
        break;
    }
    $table_name = $_SESSION['table_name'];
    $pdo = new PDO('pgsql:dbname=dcq9mmhagf14md host=ec2-3-222-30-53.compute-1.amazonaws.com port=5432','moeyszxjmvudsx','96786a380ccd8e1fc14824b34e77d4bb23193d42c740e8048902e554ee82e7d8');
    //検索条件指定
    switch ($_POST['target']) {
      case "a":
        $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE name LIKE ? ORDER BY \"$column\" $order ");
        $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
        break;
      case "b":
        $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE profile LIKE ? ORDER BY \"$column\" $order ");
        $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
        break;
      case "c":
        $stmt = $pdo->prepare("SELECT * from \"$table_name\" WHERE name LIKE ? OR profile LIKE ? ORDER BY \"$column\" $order ");
        $stmt->bindParam(1, $search_word, PDO::PARAM_STR);
        $stmt->bindParam(2, $search_word, PDO::PARAM_STR);
        break;
    }
    //SQL実行
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
     <meta name="viewport" content="width=device-width,initial-scale=1.0">
 	  <title>login</title>
     <link rel="stylesheet" href="stylesheet.css">
 	</head>
 	<body>
     <header>
         <h1>フォロワーキーワード検索アプリ</h1>
     </header>
     <div class="container">
       <p>キーワード検索</p>
       <form  action="analysis.php" method="post" id="form">
         <input class="search" type="text" name="keyword" >
         <input class="submit" type="submit" name="submit" value="検索">
         <p>検索条件</p>
         <select  name="target">
           <option value="a" >キーワードが名前に含まれる</option>
           <option value="b" <?php if($_POST['target'] == "b"):?>selected<?php endif; ?>>キーワードがプロフィールに含まれる</option>
           <option value="c" <?php if($_POST['target'] == "c"):?>selected<?php endif; ?>>キーワードが名前かプロフィールに含まれる</option>
         </select>
         <br>
         <select  name="sort">
           <option value="a" >フォロワー数が多い順</option>
           <option value="d" <?php if($_POST['sort'] == "b"):?>selected<?php endif; ?>>フォロワー数が少ない順</option>
           <option value="c" <?php if($_POST['sort'] == "c"):?>selected<?php endif; ?>>フォロー数が多い順</option>
           <option value="d" <?php if($_POST['sort'] == "d"):?>selected<?php endif; ?>>フォロー数が少ない順</option>
         </select>
       </form>
       <a href="ranking.php">キーワードランキングを見る</a>
       <?php if (isset($_POST['keyword'])) : ?>
         <p><?= $keyword ?>の検索結果<?= $count ?>/<?= $_SESSION['followers_count']?>(<?= round($count/$_SESSION['followers_count']*100) ?>%)</p>
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
               <td class="name"><a href="https://twitter.com/<?= $result['url'] ?>" target="_blank" rel="noopener noreferrer"><?= $result['name'] ?></a></td>
               <td><?= $result['friend'] ?></td>
               <td><?= $result['follower'] ?></td>
             </tr>
           <?php endforeach; ?>
         </table>
       <?php endif; ?>
     </div>
   <footer><p>Copyright (C) 2020 FukaFuka. all rights reserved.</p></footer>
   </body>
 </html>
