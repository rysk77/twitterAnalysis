<?php
require_once("lib/Igo.php");
session_start();
$igo = new Igo("ipadic", "UTF-8");
$text = $_SESSION['profiles'];
$results = $igo->parse($text);
$words =  array();
foreach ($results as $result) {
  if(strpos($result->{'feature'},'名詞') !== false
    && mb_strlen($result->{'surface'}, 'UTF-8') != 1
    && strpos($result->{'feature'},'数') === false)
    {$words[] = $result->{'surface'};}
}
  $counts = array_count_values( $words );
  arsort($counts);
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
    <div class="contents">
      <p>フォロワーのプロフィールによく含まれる単語BEST50</p>
        <table>
          <tr>
            <th>単語</th>
            <th>数</th>
          </tr>
          <?php $i = 1; ?>
         <?php foreach ($counts as $word => $count) : ?>
           <?php if( $i>=50 ){
             break;
           } ?>
            <tr>
              <td><?= $word ?></td>
              <td><?= $count ?></td>
            </tr>
            <?php $i++; ?>
          <?php endforeach; ?>
        </table>
        <a href="analysis.php">キーワード検索画面に戻る</a>
    </div>
  <footer><p>Copyright (C) 2020 FukaFuka. all rights reserved.</p></footer>
  </body>
</html>
