<?php

require_once __DIR__ . '/helpers.php';

function admin_is_logged_in(): bool
{
    ensure_session_started();
    return !empty($_SESSION['admin_logged_in']);
}

function admin_login(string $username, string $password): bool
{
    ensure_session_started();

    if (app_is_installed()) {
        $statement = db()->prepare('SELECT * FROM admins WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $admin = $statement->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
    }

    $fallback = app_config()['admin'];

    if ($username === $fallback['username'] && $password === $fallback['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }

    return false;
}

function admin_logout(): void
{
    ensure_session_started();
    unset($_SESSION['admin_logged_in'], $_SESSION['admin_username']);
}

function require_admin(): void
{
    if (!admin_is_logged_in()) {
        flash('error', 'Silakan login dulu.');
        redirect(route_url('admin/login.php'));
    }
}

function admin_change_password(string $username, string $currentPassword, string $newPassword): array
{
    if ($newPassword === '' || strlen($newPassword) < 8) {
        return ['success' => false, 'message' => 'Password baru minimal 8 karakter.'];
    }

    $statement = db()->prepare('SELECT * FROM admins WHERE username = :username LIMIT 1');
    $statement->execute(['username' => $username]);
    $admin = $statement->fetch();

    if (!$admin || !password_verify($currentPassword, $admin['password_hash'])) {
        return ['success' => false, 'message' => 'Password saat ini tidak cocok.'];
    }

    $update = db()->prepare('UPDATE admins SET password_hash = :password_hash WHERE id = :id');
    $update->execute([
        'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        'id' => $admin['id'],
    ]);

    return ['success' => true, 'message' => 'Password admin berhasil diperbarui.'];
}
