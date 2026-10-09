<?php
session_start();

#if (!isset($_SESSION['user'])) {
#    header('Location: index.php');
#    exit;
#}

// ⚙️ MySQLへの接続設定
$dsn = 'mysql:host=localhost;dbname=bakery_demo;charset=utf8mb4';
$db_user = 'user1';
$db_pass = 'passwordA1!';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

// ✨ ログインユーザーの登録情報（決済情報含む）をDBから引っ張ってくる
if (isset($_SESSION['user'])) {
    $user_stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $user_stmt->execute([':username' => $_SESSION['user']]);
    $current_user_info = $user_stmt->fetch(PDO::FETCH_ASSOC);
}

// カート追加処理
if (isset($_POST['add_to_cart'])) {
    $username = $_SESSION['user'];
    $_SESSION['cart'][$username][] = [
        'id' => $_POST['product_id'],
        'name' => $_POST['product_name'],
        'price' => $_POST['product_price']
    ];
    header('Location: shop.php?action=cart');
    exit;
}

// 口コミの投稿処理
if (isset($_POST['submit_review'])) {
    $comment = $_POST['comment'];
    $rating = intval($_POST['rating']); // 星の数を取得
    $p_id = intval($_GET['product_id']); // どの商品への口コミかを取得
    $username = $_SESSION['user']; // 誰が書いたか（ログインユーザー）を取得

    $stmt = $db->prepare("INSERT INTO reviews (product_id, username, rating, comment) VALUES (:product_id, :username, :rating, :comment)");
    $stmt->execute([
        ':product_id' => $p_id,
        ':username'   => $username,
        ':rating'     => $rating,
        ':comment'    => $comment
    ]);
    header('Location: shop.php?product_id=' . $p_id);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>まごころパン屋</title>
    <style>
        /* --- 全体のリセットとベースデザイン --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif; 
            background-color: #fcf9f5; 
            color: #4a3e3d; 
            line-height: 1.6; 
            padding-bottom: 60px;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            padding: 0 20px; 
        }

        /* --- ヘッダー --- */
        .header-bg {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(74, 62, 61, 0.05);
            margin-bottom: 40px;
            border-bottom: 3px solid #e0a96d;
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 20px 0; 
        }
        .header h1 a { 
            text-decoration: none; 
            color: #8c6239; 
            font-size: 28px; 
            font-weight: 700;
            letter-spacing: 1px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-links span {
            font-size: 14px;
            color: #6e5d5c;
        }
        .nav-links strong {
            color: #8c6239;
        }
        .nav-links a { 
            text-decoration: none; 
            color: #8c6239; 
            font-weight: bold; 
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 20px;
            background-color: #f7ede2;
            transition: all 0.3s ease;
        }
        .nav-links a:hover {
            background-color: #e0a96d;
            color: #ffffff;
        }

        /* --- タイトル・見出し --- */
        h2 {
            font-size: 24px;
            color: #5c4033;
            margin-bottom: 25px;
            border-left: 5px solid #e0a96d;
            padding-left: 12px;
        }
        h3 {
            font-size: 18px;
            color: #5c4033;
            margin: 30px 0 15px 0;
        }

        /* --- 商品一覧（グリッド） --- */
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 30px; 
            margin-top: 20px; 
        }
        .product-card { 
            background: #ffffff;
            border-radius: 12px; 
            padding: 0; 
            text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.04); 
            cursor: pointer; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            border: 1px solid #f0e6df;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(140, 98, 57, 0.15);
        }
        .product-card img { 
            width: 100%; 
            height: 180px; 
            object-fit: cover; 
        }
        .product-info-box {
            padding: 15px;
        }
        .product-card h3 { 
            font-size: 16px;
            margin: 0 0 8px 0;
            color: #4a3e3d;
        }
        .product-price { 
            color: #d97706; 
            font-weight: 700; 
            font-size: 18px;
        }

        /* --- ボタン共通 --- */
        .btn { 
            background: #e0a96d; 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold;
            text-decoration: none; 
            display: inline-block; 
            transition: background 0.3s ease;
        }
        .btn:hover { background: #c99255; }
        .btn-success { background: #5cb85c; }
        .btn-success:hover { background: #4cae4c; }

        /* --- 詳細画面レイアウト --- */
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #8c6239;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
        .detail-container {
            display: flex; 
            gap: 40px; 
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border: 1px solid #f0e6df;
            flex-wrap: wrap;
        }
        .detail-img { 
            width: 350px; 
            height: 300px; 
            object-fit: cover; 
            border-radius: 8px; 
        }
        .detail-info {
            flex: 1;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .detail-description {
            margin: 20px 0;
            color: #6e5d5c;
            font-size: 15px;
        }

        /* --- 口コミ・フォームセクション --- */
        textarea { 
            width: 100%; 
            max-width: 600px; 
            padding: 12px;
            border: 1px solid #d1c7bd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 15px;
            margin-bottom: 10px;
            background-color: #faf8f5;
        }
        textarea:focus {
            outline: none;
            border-color: #e0a96d;
            background-color: #ffffff;
        }
        .review-list { 
            margin-top: 20px; 
            background: #ffffff;
            border-radius: 12px;
            padding: 10px 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border: 1px solid #f0e6df;
        }
        .review-item { 
            border-bottom: 1px dashed #e0d5cb; 
            padding: 15px 0; 
        }
        .review-item:last-child { border-bottom: none; }

        /* --- カート・テーブル --- */
        .cart-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px; 
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border: 1px solid #f0e6df;
        }
        .cart-table th, .cart-table td { 
            padding: 16px; 
            text-align: left;
        }
        .cart-table th { 
            background-color: #f7ede2; 
            color: #5c4033;
            font-weight: bold;
        }
        .cart-table tr { border-bottom: 1px solid #f0e6df; }
        .cart-table tr:last-child { border-bottom: none; }
        
        /* --- 購入手続き（チェックアウト） --- */
        .checkout-box { 
            background: #ffffff; 
            border: 1px solid #f0e6df; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #5c4033;
            font-size: 15px;
        }
        .checkout-box input[type="text"], 
        .checkout-box input[type="date"], 
        .checkout-box select {
            padding: 10px;
            border: 1px solid #d1c7bd;
            border-radius: 6px;
            font-size: 15px;
            background-color: #faf8f5;
        }
        .checkout-box input:focus, .checkout-box select:focus {
            outline: none;
            border-color: #e0a96d;
            background-color: #ffffff;
        }
        .card-inline {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        hr {
            border: 0;
            height: 1px;
            background: #e0d5cb;
            margin: 30px 0;
        }
    </style>
</head>
<body>

    <div class="header-bg">
        <div class="container header">
            <h1><a href="shop.php">まごころパン屋</a></h1>
            <div class="nav-links">
                <span>ログイン中: <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span>
                
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1): ?>
                    <a href="admin.php" style="background-color: #d97706; color: white;">🛠️ 管理者画面</a>
                <?php endif; ?>

                <a href="shop.php?action=cart">🛒 カート (<?php echo isset($_SESSION['cart'][$_SESSION['user']]) ? count($_SESSION['cart'][$_SESSION['user']]) : 0; ?>)</a>
                <a href="logout.php">ログアウト</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($action === 'list' && $product_id === 0): ?>
            <h2>商品一覧</h2>
            <div class="product-grid">
                <?php
                $products = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($products as $p):
                ?>
                    <div class="product-card" onclick="window.location.href='shop.php?product_id=<?php echo $p['id']; ?>'">
                        <img src="<?php echo $p['image_url']; ?>">
                        <div class="product-info-box">
                            <h3><?php echo $p['name']; ?></h3>
                            <p class="product-price"><?php echo $p['price']; ?>円</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($product_id > 0): 
            $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => $product_id]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
            <a href="shop.php" class="back-link">← 商品一覧に戻る</a>
            
            <div class="detail-container">
                <img src="<?php echo $p['image_url']; ?>" class="detail-img">
                <div class="detail-info">
                    <div>
                        <h2><?php echo $p['name']; ?></h2>
                        <p style="font-size: 24px; color: #d97706; font-weight: bold; margin-top: 10px;"><?php echo $p['price']; ?>円</p>
                        <p class="detail-description"><?php echo $p['description']; ?></p>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $p['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $p['price']; ?>">
                        <button type="submit" name="add_to_cart" class="btn" style="width: 100%;">🛒 カートに入れる</button>
                    </form>
                </div>
            </div>

            <h3 style="margin-top: 50px;">カスタマーレビュー（口コミ）</h3>
            <form method="POST" action="">
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; color: #5c4033; display: block; margin-bottom: 5px;">評価（星の数）:</label>
                    <select name="rating" required style="padding: 8px; border-radius: 6px; border: 1px solid #d1c7bd; background: #faf8f5; font-size: 15px;">
                        <option value="5">★★★★★ (5)</option>
                        <option value="4">★★★★☆ (4)</option>
                        <option value="3">★★★☆☆ (3)</option>
                        <option value="2">★★☆☆☆ (2)</option>
                        <option value="1">★☆☆☆☆ (1)</option>
                    </select>
                </div>

                <div style="margin-bottom: 10px;">
                    <label style="font-weight: bold; color: #5c4033; display: block; margin-bottom: 5px;">コメント:</label>
                    <textarea name="comment" rows="3" placeholder="パンの感想を書いてね" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn">口コミを投稿</button>
            </form>

            <div style="margin-top: 30px;">
                <h4>みんなの口コミ一覧</h4>
                <div class="review-list">
                    <?php 
                    // 商品ID（$product_id）に紐づく口コミを取得
                    $review_stmt = $db->prepare("SELECT * FROM reviews WHERE product_id = :product_id ORDER BY created_at DESC");
                    $review_stmt->execute([':product_id' => $product_id]);
                    $reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($reviews)): 
                    ?>
                        <p style="color: #888; text-align: center; padding: 15px 0;">この商品への口コミはまだありません。</p>
                    <?php 
                    else:
                        foreach ($reviews as $review): 
                            $stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);
                    ?>
                            <div class="review-item" style="border-bottom: 1px dashed #e0d5cb; padding: 15px 0;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                    <span style="font-weight: bold; color: #8c6239;">
                                        👤 <?php echo htmlspecialchars($review['username']); ?> さん
                                    </span>
                                    <span style="color: #d97706; font-weight: bold; letter-spacing: 1px;">
                                        <?php echo $stars; ?> (<?php echo $review['rating']; ?>)
                                    </span>
                                </div>
                                <p style="white-space: pre-wrap; color: #4a3e3d; margin-top: 5px;"><?php echo $review['comment']; ?></p>
                                <span style="font-size: 11px; color: #aaa; display: block; text-align: right; margin-top: 5px;">
                                    <?php echo $review['created_at']; ?>
                                </span>
                            </div>
                    <?php 
                        endforeach; 
                    endif;
                    ?>
                </div>
            </div>

        <?php elseif ($action === 'cart'): ?>
            <h2>ショッピングカート一覧</h2>
            <?php 
            $user_cart = isset($_SESSION['cart'][$_SESSION['user']]) ? $_SESSION['cart'][$_SESSION['user']] : [];
            if (empty($user_cart)): 
            ?>
                <p style="text-align: center; padding: 40px 0; color: #6e5d5c;">カートは空です。</p>
                <p style="text-align: center;"><a href="shop.php" class="btn">商品一覧へ戻る</a></p>
            <?php else: ?>
                <table class="cart-table">
                    <tr>
                        <th>商品名</th>
                        <th>価格</th>
                    </tr>
                    <?php 
                    $total = 0;
                    foreach ($user_cart as $item): 
                        $total += $item['price'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td style="font-weight: bold; color: #4a3e3d;"><?php echo htmlspecialchars($item['price']); ?>円</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background-color: #faf8f5;">
                        <td style="text-align: right; font-weight: bold; padding: 20px;">合計金額:</td>
                        <td style="font-weight: bold; color: #d97706; font-size: 20px; padding: 20px;"><?php echo $total; ?>円</td>
                    </tr>
                </table>
                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <a href="shop.php" class="btn" style="background:#b5a499;">お買い物を続ける</a>
                    <a href="shop.php?action=checkout" class="btn btn-success">購入手続きへ進む →</a>
                </div>
            <?php endif; ?>

        <?php elseif ($action === 'checkout'): ?>
            <h2>ご購入手続き</h2>
            <div class="checkout-box">
                <h3>お受け取り日時の指定</h3>
                <form action="complete.php" method="POST">
                    <div class="form-group" style="margin-top: 15px;">
                        <label>受取日</label>
                        <input type="date" name="pickup_date" required min="<?php echo date('Y-m-d'); ?>" style="width: 200px;">
                    </div>
                    <div class="form-group">
                        <label>受取時間</label>
                        <select name="pickup_time" required style="width: 200px;">
                            <option value="">選択してください</option>
                            <option value="09:00-11:00">09:00 〜 11:00</option>
                            <option value="11:00-13:00">11:00 〜 13:00</option>
                            <option value="13:00-15:00">13:00 〜 15:00</option>
                            <option value="15:00-17:00">15:00 〜 17:00</option>
                            <option value="17:00-19:00">17:00 〜 19:00</option>
                        </select>
                    </div>
                    
                    <hr>
                    
                    <h3>クレジットカード情報の入力</h3>
                    <p style="font-size:13px; color:#2e7d32; margin-bottom: 15px; background: #e8f5e9; padding: 10px; border-radius: 6px;">
                        ※会員情報に登録済みの場合は自動入力されています。変更も可能です。
                    </p>
                    <div class="form-group">
                        <label>カード番号</label>
                        <input type="text" name="card_num" value="<?php echo htmlspecialchars($current_user_info['card_num'] ?? ''); ?>" required style="width:100%; max-width: 400px;" placeholder="1111222233334444">
                    </div>
                    <div class="card-inline">
                        <div class="form-group">
                            <label>有効期限</label>
                            <input type="text" name="card_expiry" value="<?php echo htmlspecialchars($current_user_info['card_expiry'] ?? ''); ?>" placeholder="MM/YY" required style="width:100px; text-align: center;">
                        </div> 
                        <div class="form-group">
                            <label>セキュリティコード (CVC)</label>
                            <input type="text" name="card_cvc" value="<?php echo htmlspecialchars($current_user_info['card_cvc'] ?? ''); ?>" placeholder="123" required style="width:80px; text-align: center;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 20px; padding: 15px;">注文を確定して領収書を発行する</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
