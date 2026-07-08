<?php
class Auth
{
    public static function verifyPassword(string $storedPassword, string $inputPassword): bool
    {
        $stored = strtolower(trim($storedPassword));
        $input = strtolower(trim($inputPassword));
        return $stored === md5($input);
    }

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireLogin(): void
    {
        self::startSession();
        if (empty($_SESSION['admin_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    public static function isDemoLogin(string $username, string $password): bool
    {
        return $username === 'hxzc33' && $password === '123456';
    }
}
?>
