<?php
  //2重投稿防止
  if (!empty($_POST)) {
    header("Location: {$_SERVER['REQUEST_URI']}");
  }
  //データベース接続
  $dsn = 'データベース名';
  $user = 'ユーザ名';
  $password = 'パスワード';
  $pdo = new PDO($dsn,$user,$password);
  //テーブル作成
  $sql = "CREATE TABLE bbs4(id INT, name char(32), comment TEXT, Con_time DateTime, pass TEXT)";
  $stmt = $pdo -> query($sql);

//投稿処理
if ($_POST['write'] && $_POST['mode'] != 'editmode' && isset($_POST['name']) && isset($_POST['comment']) && isset($_POST['pass'])) {
  //データベースへ書き込み
  $sql = $pdo -> prepare("INSERT INTO bbs4(id, name, comment, Con_time, pass) VALUES(:id, :name, :comment, now(), :pass)");
  $sql -> bindParam(':id', $no, PDO::PARAM_INT);
  $sql -> bindParam(':name', $name, PDO::PARAM_STR);
  $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
  $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
  //投稿番号
  $sql2 = 'SELECT Count(*) FROM bbs4';
  $stmt = $pdo -> query($sql2);
  $no = ($stmt->fetchColumn()) + 1;
  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $pass = $_POST['pass'];

  $sql -> execute();
}

//削除処理
if ($_POST['delete'] && isset($_POST['del_no']) && isset($_POST['del_pass'])) {
  //id取得
  $id = $_POST['del_no'];
  //pass取得
  $pass = $_POST['del_pass'];
  //削除
  $sql = "delete from bbs4 where id=$id AND pass=$pass";
  $stmt = $pdo -> query($sql);
}

//編集ボタンを押したときの処理
if ($_POST['edit'] && isset($_POST['edi_no']) && isset($_POST['edi_pass'])) {
  //id取得
  $edi_no = $_POST['edi_no'];
  //pass取得
  $edi_pass = $_POST['edi_pass'];
  //データベースから値を取得
  $sql = "SELECT * FROM bbs4 where id=$edi_no AND pass=$edi_pass";
  $result = $pdo -> query($sql);
  //表示
  foreach($result as $row){
    $edi_name = $row['name'];
    $edi_co = $row['comment'];
    $edi_pss = $row['pass'];
  }
}

//編集内容を変更するときの処理
if ($_POST['write'] && $_POST['mode'] == 'editmode' && isset($_POST['name']) && isset($_POST['comment']) && isset($_POST['pass'])) {
  //name取得
  $name = $_POST['name'];
  //comment取得
  $comment = $_POST['comment'];
  //pass取得
  $pass = $_POST['pass'];
  //時間取得
  $time = date("Y/m/d H:i:s");
  //編集処理
  $id = $_POST['edi_no'];
  $edit_name = $name;
  $edit_com = $comment;
  $sql = "update bbs4 set name='$edit_name', comment='$edit_com' where id = $id";
  $result = $pdo -> query($sql);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>bbs</title>
</head>
<body>
<!-- 投稿フォーム -->
  <form method="post" action="">
    <input type="text" name="name" placeholder="お名前" value="<?php echo $edi_name; ?>"><br>
    <input type="text" name="comment" placeholder="コメント" value="<?php echo $edi_co; ?>"><br>

    <?php
    if($_POST['edi_no']){
      $edi_no = $_POST['edi_no'];
      echo '<input type="hidden" name="mode" value="editmode">';
      echo '<input type="hidden" name="edi_no" value="' . $edi_no . '">';
    }
    ?>

    <input type="password" name="pass" placeholder="パスワード" value="<?php echo $edi_pss; ?>">
    <input type="submit" name="write" value="送信">
  </form>
  <hr>

  <!-- 削除フォーム -->
  <form method="post" action="">
    <input type="text" name="del_no" placeholder="削除指定番号"><br>
    <input type="password" name="del_pass" placeholder="パスワード">
    <input type="submit" name="delete" value="削除">
  </form>
  <hr>

  <!-- 編集フォーム -->
  <form method="post" action="">
    <input type="text" name="edi_no" placeholder="編集指定番号"><br>
    <input type="password" name="edi_pass" placeholder="パスワード">
    <input type="submit" name="edit" value="編集">
  </form>
  <hr>

  <?php
  $sql = 'SELECT * FROM bbs4';
  $result = $pdo -> query($sql);

  foreach ($result as $row){
    //$rowの中にはカラムが入る
    echo $row['id'] . ' ';
    echo "名前：" . $row['name'] . '：';
    echo $row['Con_time']  . '<br>';
    echo '　　' . $row['comment'] .  '<br><br>';
  }
  ?>

</body>
</html>
