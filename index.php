<?php
// エラーを画面に強制表示させる設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// ⚙️ MySQLへの接続設定（環境に合わせて root や パスワード を変更してください）
$dsn = 'mysql:host=localhost;dbname=bakery_demo;charset=utf8mb4';
$db_user = 'user1';
$db_pass = 'passwordA1!'; // パスワードがある場合は入力

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

// 🔑 ログイン処理
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // ⚠️ 脆弱なSQL文（SQLインジェクション用）
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $db->query($query);
    $users = $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

    // 結果の配列が空でなければ（1行以上マッチすれば）ログイン成功
    if (!empty($users)) {
        // マッチした最初の1行目のデータをログインユーザーとして扱う
        $user = $users[0];
        
        $_SESSION['user'] = $user['username'];
        // 【重要】ログインしたユーザーの管理者フラグをセッションに保存する
        $_SESSION['is_admin'] = intval($user['is_admin']); 
        
        header('Location: shop.php');
        exit;
    } else {
        $error = "ユーザーIDまたはパスワードが違います。";
    }
}

// 📝 新規会員登録処理
if (isset($_POST['register'])) {
    $reg_username = $_POST['reg_username'];
    $reg_password = $_POST['reg_password'];
    $reg_card_num = $_POST['reg_card_num'];
    $reg_card_expiry = $_POST['reg_card_expiry'];
    $reg_card_cvc = $_POST['reg_card_cvc'];

    if (!empty($reg_username) && !empty($reg_password)) {
        // ⚠️ 決済情報も含めて平文でデータベースに保存
        $stmt = $db->prepare("INSERT INTO users (username, password, card_num, card_expiry, card_cvc) VALUES (:username, :password, :card_num, :card_expiry, :card_cvc)");
        $stmt->execute([
            ':username' => $reg_username, 
            ':password' => $reg_password,
            ':card_num' => $reg_card_num,
            ':card_expiry' => $reg_card_expiry,
            ':card_cvc' => $reg_card_cvc
        ]);
        $message = "会員登録（決済情報含む）が完了しました！上のフォームからログインしてください。";
    } else {
        $error = "ユーザーIDとパスワードは必須です。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>まごころパン屋 - ログイン・会員登録</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px;">
    
    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2>① ログイン</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
        
        <form method="POST" action="">
            <p>ユーザーID: <input type="text" name="username"  style="width:100%; padding:5px;"></p>
            <p>パスワード: <input type="password" name="password" style="width:100%; padding:5px;"></p>
            <p><button type="submit" name="login" style="width:100%; padding:8px; background:#f0a04b; color:white; border:none; border-radius:4px; cursor:pointer;">ログイン</button></p>
        </form>
    </div>

    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px;">
    <h2>② 新規会員登録 (決済情報入力)</h2>
    <form method="POST" action="">
        <p>希望ユーザーID: <input type="text" name="reg_username" required style="width:100%; padding:5px;"></p>
        <p>パスワード: <input type="password" name="reg_password" required style="width:100%; padding:5px;"></p>
        <hr>
        <p style="font-size:12px; color:gray;">※登録しておくと次回以降の決済情報入力が省略されます。</p>
        <p>カード番号: <input type="text" name="reg_card_num" placeholder="1111222233334444" style="width:100%; padding:5px;"></p>
        <p>有効期限: <input type="text" name="reg_card_expiry" placeholder="MM/YY" style="width:45%; padding:5px;"> 
           CVC: <input type="text" name="reg_card_cvc" placeholder="123" style="width:45%; padding:5px;"></p>
        <p><button type="submit" name="register" style="width:100%; padding:8px; background:#5cb85c; color:white; border:none; border-radius:4px; cursor:pointer;">会員登録する</button></p>
    </form>
</div>

    <hr style="margin-top:30px;">
    <h4>💡 SQLインジェクションのハック方法</h4>
    <p>ログインのユーザーID欄に以下を入力し、パスワード欄は空欄のままログインボタンを押してください。</p>
    <code>admin' #</code>
</body>
</html>
