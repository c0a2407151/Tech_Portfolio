<?php
// セッションを開始
session_start();

// セッション変数をすべて解除する
$_SESSION = array();

// ブラウザのセッションクッキーも完全に削除する
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 最終的にセッションを完全に破棄
session_destroy();

// 指定された begin.php へリダイレクト（移動）
header("Location: begin.php");
exit;
