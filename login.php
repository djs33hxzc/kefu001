<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';

Auth::startSession();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        $authenticated = false;
        try {
            $database = new Database();
            $connection = $database->getConnection();
            $statement = $connection->prepare('SELECT id, username, password FROM admin WHERE username = ? LIMIT 1');
            $statement->bind_param('s', $username);
            $statement->execute();
            $result = $statement->get_result();
            $row = $result->fetch_assoc();

            if ($row && Auth::verifyPassword((string) $row['password'], $password)) {
                $authenticated = true;
                $_SESSION['admin_id'] = (int) $row['id'];
                $_SESSION['admin_username'] = (string) $row['username'];
            }
        } catch (Throwable $e) {
            $authenticated = Auth::isDemoLogin($username, $password);
        }

        if ($authenticated) {
            if (empty($_SESSION['admin_id'])) {
                $_SESSION['admin_id'] = 1;
                $_SESSION['admin_username'] = $username;
            }
            header('Location: admin.php');
            exit;
        }

        $error = '用户名或密码错误';
    } else {
        $error = '请输入用户名和密码';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>33客服台登录</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #eff6ff, #f8fafc); margin: 0; display: grid; place-items: center; min-height: 100vh; }
        .card { background: #fff; padding: 24px; border-radius: 12px; width: min(92vw, 380px); box-shadow: 0 15px 40px rgba(15,23,42,0.12); }
        input { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #d8dce6; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #2563eb; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; }
        .error { color: #dc2626; margin-bottom: 12px; }
        .hint { color: #64748b; font-size: 13px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>33客服台后台登录</h2>
        <p class="hint">本地演示环境已开启，默认账号为 hxzc33 / 123456</p>
        <?php if ($error !== ''): ?><div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="用户名" value="hxzc33" required>
            <input type="password" name="password" placeholder="密码" value="123456" required>
            <button type="submit">登录</button>
        </form>
    </div>
</body>
</html>
