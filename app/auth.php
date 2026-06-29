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
