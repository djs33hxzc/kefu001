<?php
require_once __DIR__ . '/includes/Auth.php';
Auth::requireLogin();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>客服对话</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .topbar { background: #0f172a; color: #fff; padding: 16px 24px; }
        .nav { background: #fff; padding: 12px 24px; border-bottom: 1px solid #e2e8f0; }
        .nav a { color: #2563eb; text-decoration: none; margin-right: 10px; }
        .container { padding: 24px; }
        .box { background: #fff; border-radius: 10px; padding: 16px; box-shadow: 0 8px 20px rgba(15,23,42,0.06); }
    </style>
</head>
<body>
    <div class="topbar">33客服台后台</div>
    <div class="nav"><a href="admin.php">控制台</a><a href="chat.php">客服对话</a><a href="customers.php">客户管理</a><a href="orders.php">订单管理</a><a href="settings.php">系统设置</a><a href="logout.php">退出</a></div>
    <div class="container">
        <div class="box">
            <h2>客服对话</h2>
            <p>模拟会话已接入，当前可查看客户咨询记录与消息状态。</p>
            <ul>
                <li>客户A：需要查询订单进度</li>
                <li>客户B：咨询发票开具</li>
                <li>客户C：申请退款</li>
            </ul>
        </div>
    </div>
</body>
</html>
