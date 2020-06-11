<?php
//テーブル削除
$_SESSION['table_name'] = $table_name;
$pdo = new PDO('pgsql:dbname=dcq9mmhagf14md host=ec2-3-222-30-53.compute-1.amazonaws.com port=5432','moeyszxjmvudsx','96786a380ccd8e1fc14824b34e77d4bb23193d42c740e8048902e554ee82e7d8');
$sql = "delete from \"$table_name\" ";
$stmt = $pdo->query($sql);
$pdo = null;
?>
