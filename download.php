<?php
session_start();

// セッションから取得
$pickup_date = $_SESSION['pickup_date'] ?? '未指定';
$pickup_time = $_SESSION['pickup_time'] ?? '未指定';
$card_num = $_SESSION['card_num'] ?? '';

// カード番号をマスク
$card_num = preg_replace('/\D/', '', $card_num);
$last4 = substr($card_num, -4);
$masked_card = str_repeat('*', max(strlen($card_num)-4,0)) . $last4;

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="電子領収書.txt"');

echo "===================================\n";
echo "       まごころパン屋 電子領収書      \n";
echo "===================================\n";
echo "ご注文、誠にありがとうございました。\n\n";

echo "【お受け取り日時】\n";
echo " 日時: ".$pickup_date." (".$pickup_time.")\n";
echo " 場所: まごころパン屋 本店窓口\n\n";

echo "【お支払い情報】\n";
echo " カード番号: ".$masked_card."\n\n";

echo "【ご注文内容】\n";

if(isset($_SESSION['user'])){

    // 注文確定前に保存しておいたカートを取得
    $user_cart = $_SESSION['last_order'] ?? [];

    $total = 0;

    foreach($user_cart as $item){

        echo " ・".$item['name']." : ".$item['price']."円\n";
        $total += $item['price'];

    }

    echo "-----------------------------------\n";
    echo " 合計金額: ".$total."円\n";
}

echo "===================================\n";

exit;
