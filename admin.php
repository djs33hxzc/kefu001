<?php
require_once __DIR__ . '/includes/Auth.php';
Auth::requireLogin();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>33客服台后台</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .topbar { background: #0f172a; color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .nav { background: #fff; padding: 12px 24px; display: flex; gap: 12px; border-bottom: 1px solid #e2e8f0; }
        .nav a { color: #2563eb; text-decoration: none; padding: 6px 10px; border-radius: 6px; }
        .nav a:hover { background: #eff6ff; }
        .container { padding: 24px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .card { background: #fff; border-radius: 10px; padding: 16px; box-shadow: 0 8px 20px rgba(15,23,42,0.06); }
        .big { font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="topbar">
        <div><strong>33客服台后台</strong></div>
        <div>欢迎，<?php echo htmlspecialchars($_SESSION['admin_username'] ?? '管理员', ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <div class="nav">
        <a href="admin.php">控制台</a>
        <a href="chat.php">客服对话</a>
        <a href="customers.php">客户管理</a>
        <a href="orders.php">订单管理</a>
        <a href="settings.php">系统设置</a>
        <a href="logout.php">退出登录</a>
    </div>
    <div class="container">
        <h2>运营控制台</h2>
        <div class="grid">
            <div class="card"><div>在线客服</div><div class="big">8</div></div>
            <div class="card"><div>今日会话</div><div class="big">126</div></div>
            <div class="card"><div>待处理订单</div><div class="big">19</div></div>
            <div class="card"><div>系统状态</div><div class="big">正常</div></div>
        </div>
        <div class="card" style="margin-top:16px;">
            <h3>当前模拟功能</h3>
            <p>登录已成功，后台导航已可切换，客服、客户、订单和设置页面均已准备好。</p>
        </div>
    </div>
</body>
</html>
