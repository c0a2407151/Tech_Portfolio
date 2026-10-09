<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// MySQL接続
$dsn = 'mysql:host=localhost;dbname=bakery_demo;charset=utf8mb4';
$db_user = 'user1'; // 環境に合わせて変更してください
$db_pass = 'passwordA1!';     // 環境に合わせて変更してください

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. クレジットカード情報の更新（POSTされた場合）
    if (isset($_POST['card_num'])) {
        $stmt = $db->prepare("
            UPDATE users
            SET card_num = :card_num, card_expiry = :card_expiry, card_cvc = :card_cvc
            WHERE username = :username
        ");
        $stmt->execute([
            ':card_num' => $_POST['card_num'],
            ':card_expiry' => $_POST['card_expiry'],
            ':card_cvc' => $_POST['card_cvc'],
            ':username' => $_SESSION['user']
        ]);
    }

    // 2. 【新規追加】予約情報を reservations テーブルに保存する処理
    $user_cart = $_SESSION['cart'][$_SESSION['user']] ?? [];
    if (!empty($user_cart) && isset($_POST['pickup_date']) && isset($_POST['pickup_time'])) {
        
        // 複数商品を一括でインサートするための準備
        $ins_stmt = $db->prepare("
            INSERT INTO reservations (username, product_id, product_name, product_price, pickup_date, pickup_time)
            VALUES (:username, :product_id, :product_name, :product_price, :pickup_date, :pickup_time)
        ");

        foreach ($user_cart as $item) {
            $ins_stmt->execute([
                ':username'     => $_SESSION['user'],
                ':product_id'   => $item['id'],
                ':product_name' => $item['name'],
                ':product_price'=> $item['price'],
                ':pickup_date'  => $_POST['pickup_date'],
                ':pickup_time'  => $_POST['pickup_time']
            ]);
        }
    }

} catch(PDOException $e){
    // デバッグ用（本番環境では die させずログに出すのが望ましいです）
    die("エラーが発生しました: " . $e->getMessage());
}

// 領収書用データをセッションに保存
$_SESSION['pickup_date'] = $_POST['pickup_date'] ?? '未指定';
$_SESSION['pickup_time'] = $_POST['pickup_time'] ?? '未指定';
$_SESSION['card_num']    = $_POST['card_num']    ?? '';

// 注文内容を保存
$_SESSION['last_order'] = $_SESSION['cart'][$_SESSION['user']] ?? [];

// 注文完了なのでカートを空にする
$_SESSION['cart'][$_SESSION['user']] = [];
?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>注文完了 | まごころパン屋</title>

<style>
*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    font-family:'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif;
    background:#fcf9f5;
    color:#4a3e3d;
}

.container{
    max-width:1000px;
    margin:0 auto;
    padding:0 20px;
}

/* ヘッダー */
.header-bg{
    background:#fff;
    border-bottom:3px solid #e0a96d;
    box-shadow:0 2px 10px rgba(74,62,61,.05);
    margin-bottom:40px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 0;
}

.header h1 a{
    text-decoration:none;
    color:#8c6239;
    font-size:28px;
    font-weight:bold;
}

.nav-links{
    display:flex;
    align-items:center;
    gap:20px;
}

.nav-links span{
    font-size:14px;
    color:#6e5d5c;
}

.nav-links strong{
    color:#8c6239;
}

.nav-links a{
    text-decoration:none;
    color:#8c6239;
    font-weight:bold;
    font-size:14px;
    padding:8px 16px;
    border-radius:20px;
    background:#f7ede2;
}

.nav-links a:hover{
    background:#e0a96d;
    color:#fff;
}

/* メイン */
.complete-box{
    background:#fff;
    border:1px solid #f0e6df;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,.04);
    padding:50px;
    text-align:center;
}

.check{
    font-size:70px;
    margin-bottom:20px;
}

.complete-box h2{
    color:#5c4033;
    margin-bottom:20px;
    font-size:28px;
}

.complete-box p{
    color:#6e5d5c;
    margin-bottom:15px;
    line-height:1.8;
}

.button-area{
    margin-top:40px;
    display:flex;
    justify-content:center;
    gap:20px;
    flex-wrap:wrap;
}

.btn{
    background:#e0a96d;
    color:#fff;
    text-decoration:none;
    padding:14px 30px;
    border-radius:8px;
    font-weight:bold;
    transition:.3s;
}

.btn:hover{
    background:#c99255;
}

.btn-green{
    background:#5cb85c;
}

.btn-green:hover{
    background:#4cae4c;
}
</style>

</head>
<body>

<div class="header-bg">
    <div class="container header">

        <h1><a href="shop.php">まごころパン屋</a></h1>

        <div class="nav-links">
            <span>
                ログイン中：
                <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>
            </span>

            <a href="logout.php">ログアウト</a>
        </div>

    </div>
</div>

<div class="container">

    <div class="complete-box">

        <h2>ご注文ありがとうございました！</h2>

        <p>
            ご注文を受け付けました。<br>
            商品のお受け取りをお待ちしております。
        </p>

        <p>
            電子領収書が必要な場合は、<br>
            下のボタンからダウンロードしてください。
        </p>

        <div class="button-area">

            <a href="download.php" class="btn btn-green">
                  電子領収書をダウンロード
            </a>

            <a href="shop.php" class="btn">
                 商品一覧へ戻る
            </a>

        </div>

    </div>

</div>

</body>
</html>
