<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>
    <body>
        
<?php
//データベースへの接続
$dsn = 'mysql:dbname=tb230428db;host=localhost';
$user = 'tb-230428';
$password = 'dNpSnnfFS5';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS table1"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"//ナンバリング
    ."name char(32),"//名前
    ."comment TEXT,"//コメント
    ."date TEXT,"//日付
    ."pass char(32)"//パスワード
    .");";
    $stmt = $pdo->query($sql); 
    
    //各種データの取得
    if(isset($_POST["name"],$_POST["comment"],$_POST["pass"])){ //エラー防止
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $pass=$_POST["pass"];
    }
    $newname="";
    $newcomment="";
    $date = date('Y/m/d H:i:s');

//1.編集ボタンを押した場合
if (isset($_POST["EDIT"],$_POST["edipass"])){
    $edit=$_POST["edit"];
    $edipass=$_POST["edipass"];
    $passChecker=0;
    $numChecker=0;
    //エラー防止
    if(strlen($edit)&&strlen($edipass)){
    //データレコードの抽出
    $sql ='SELECT * FROM table1';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
      foreach ($results as $row){
        //編集番号とパスワードの一致を確認
        if($row["id"]==$edit){
            if($row["pass"]==$edipass){
                $passChecker++;
            }else{
                echo "パスワードが一致しません。<br>";
                echo "<hr>";
                $numChecker++;
            }
        }
      }
    }
    
    if($passChecker>0){
       //データレコードの抽出
       $sql ='SELECT * FROM table1 WHERE id= '.$edit.'';
       $stmt = $pdo->query($sql);
       $results = $stmt->fetchAll();
       foreach ($results as $row){
        //フォームに表示するデータ
        $newname = $row["name"];
        $newcomment = $row["comment"];
       }
        //編集モードに設定
        $mode="$edit";
    }
}

//2.送信ボタンを押した場合
if(isset($_POST["submit"],$_POST["pass"])){
    //a.編集
      if($_POST["editt"]!=0){
             //データレコードの更新
             $id=$_POST["editt"];
             $sql = 'UPDATE table1 SET name=:name,comment=:comment WHERE id=:id';
             $stmt = $pdo->prepare($sql);
             $stmt->bindParam(':name', $name, PDO::PARAM_STR);
             $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
             $stmt->bindParam(':id', $id, PDO::PARAM_INT);
             $stmt->execute();
             $mode="new";
             echo $id."番を編集しました。";
             echo "<hr>";
      }   
    //b.新規投稿
       else{
    //データレコードの挿入
    $sql = $pdo -> prepare("INSERT INTO table1 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date('Y/m/d H:i:s');
    $pass = $_POST["pass"];
    $sql -> execute();
        }
}
    
//3.削除ボタンを押した場合
if (isset( $_POST["DELETE"],$_POST["delpass"])){
    $delete = $_POST["delete"];
    $delpass=$_POST["delpass"];
    $passChecker=0;
    $numChecker=0;
    //エラー防止
    if(strlen($delete)&&strlen($delpass)){
    //データレコードの抽出
    $sql ='SELECT * FROM table1';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
      foreach ($results as $row){
        //削除番号とパスワードの一致を確認
        if($row["id"]==$delete){
            if($row["pass"]==$delpass){
                $passChecker++;
            }else{
                echo "パスワードが一致しません。<br>";
                echo "<hr>";
                $numChecker++;
            }
        }
      }
     if($passChecker>0){
     //データレコードの削除
     $id = $delete;
     $sql = 'delete from table1 where id=:id';
     $stmt = $pdo->prepare($sql);
     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
     $stmt->execute();
        echo $id."番を削除しました。";
        echo "<hr>";
     }
    }
}
?>

<form action="" method="post">
    <p>名前：<input type="text" name="name" value="<?php echo  $newname ?>">
    コメント：<input type="text" name="comment" value="<?php echo  $newcomment ?>">
    <input type="submit" name="submit"></p>
    <p><input type="text" name="pass" placeholder="パスワード"></p>
    <p>削除対象番号：<input type="number" name="delete">
    <input type="text" name="delpass"placeholder="パスワード">
    <input type="submit" name="DELETE" value="削除"></p>
    <p>編集対象番号：<input type="number" name="edit">
    <input type="text" name="edipass"placeholder="パスワード">
    <input type="submit" name="EDIT" value="編集"></p>
    <input type="hidden" name="editt"value="<?php echo  $edit ?>">
    </form>

<?php
//テーブル内のデータを抽出して表示
$sql = 'SELECT * FROM table1';
    $stmt = $pdo->query($sql);
    //すべて抽出
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].',';
        echo $row['pass'].'<br>';
    echo "<hr>";
    }
?>

</body>
</html>