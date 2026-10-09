<?php
session_start();

// 🔒 管理者認証チェック
if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<script>
            alert('このページへのアクセス権限がありません。管理者アカウントでログインしてください。');
            window.location.href = 'shop.php';
          </script>";
    exit;
}

// ⚙️ MySQLへの接続設定
$dsn = 'mysql:host=localhost;dbname=bakery_demo;charset=utf8mb4';
$db_user = 'user1'; 
$db_pass = 'passwordA1!';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    // 連想配列形式（キー名）だけでなく、番号（0, 1, 2...）でも値を取得できるように設定
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

// 📊 予約データの集計
$query = "
    SELECT 
        pickup_date, 
        pickup_time, 
        product_name, 
        COUNT(*) as quantity, 
        SUM(product_price) as total_revenue
    FROM reservations
    GROUP BY pickup_date, pickup_time, product_name
    ORDER BY pickup_date ASC, pickup_time ASC, quantity DESC
";
$summary_res = $db->query($query)->fetchAll();

// 🔍 ユーザー検索の処理（脆弱性をそのまま残した状態）
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

if ($search_keyword !== '') {
    // ❌ 超危険な文字列結合（SQLインジェクションがそのまま通る）
    $detail_query = "SELECT * FROM reservations WHERE username LIKE '%" .$search_keyword . "%'";
    $detail_res = $db->query($detail_query)->fetchAll();
} else {
    $detail_query = "SELECT * FROM reservations ORDER BY pickup_date DESC, pickup_time DESC";
    $detail_res = $db->query($detail_query)->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>まごころパン屋 - 管理者画面 (デバッグ・検証用)</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Helvetica Neue', Arial, sans-serif; 
            background-color: #f8f9fa; 
            color: #333; 
            padding: 40px 20px;
        }
        .container { max-width: 1100px; margin: 0 auto; }
        h1 { color: #5c4033; margin-bottom: 30px; border-bottom: 3px solid #e0a96d; padding-bottom: 10px; }
        h2 { font-size: 20px; color: #8c6239; margin: 30px 0 15px 0; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
        th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #e0a96d; color: white; font-weight: bold; }
        tr:hover { background-color: #fcfaf7; }
        
        .badge {
            background-color: #d97706;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
        }
        .nav-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #8c6239;
            text-decoration: none;
            font-weight: bold;
        }
        .search-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .search-button {
            background-color: #8c6239;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="shop.php" class="nav-back">← ショップ画面へ戻る</a>
    <h1>🍞 まごころパン屋 管理者ダッシュボード</h1>

    <!-- 🔍 ユーザー検索フォーム -->
    <form method="GET" action="" class="search-container">
        <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($search_keyword, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="search-button">送信</button>
    </form>

    <h2>予約詳細・データ出力テーブル </h2>
    <?php if (empty($detail_res)): ?>
        <p style="background:#fff; padding:20px; border-radius:8px; text-align:center; color:#777;">該当するデータはありません。</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <!-- reservationsが全8列だとした場合の、左からの順序でのヘッダー定義 -->
                    <th>id</th>
                    <th>username</th>
                    <th>quantity</th>
                    <th>productname</th>
                    <th>price</th>
                    <th>date</th>
                    <th>time</th>
                    <th>reservation_time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail_res as $row): ?>
                    <tr>
                        <!-- 💡 ここが改造ポイント！
                             キー名(idやproduct_priceなど)での指定を完全にやめ、
                             データベースから返ってきたデータの「左からの順番 (0〜7)」でそのまま出力します。
                             また、number_format() も使わないため、文字列が入ってきても一切クラッシュしません。 -->
                        <td><?php echo isset($row[0]) ? htmlspecialchars($row[0]) : ''; ?></td>
                        <td><?php echo isset($row[1]) ? htmlspecialchars($row[1]) : ''; ?></td>
                        <td><strong><?php echo isset($row[2]) ? htmlspecialchars($row[2]) : ''; ?></strong></td>
                        <td><?php echo isset($row[3]) ? htmlspecialchars($row[3]) : ''; ?></td>
                        <td><?php echo isset($row[4]) ? htmlspecialchars($row[4]) : ''; ?></td>
                        <td><?php echo isset($row[5]) ? htmlspecialchars($row[5]) : ''; ?></td>
                        <td><?php echo isset($row[6]) ? htmlspecialchars($row[6]) : ''; ?></td>
                        <td><?php echo isset($row[7]) ? htmlspecialchars($row[7]) : ''; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
