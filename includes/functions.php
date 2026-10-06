<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const USERS_FILE = __DIR__ . '/../data/users.json';
const MIN_PASSWORD_LENGTH = 8;

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function require_login(): void
{
    if (empty($_SESSION['user'])) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        redirect('login.php');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function load_users(): array
{
    if (!is_file(USERS_FILE)) {
        return [];
    }

    $raw = file_get_contents(USERS_FILE);
    if ($raw === false || $raw === '') {
        return [];
    }

    $users = json_decode($raw, true);
    return is_array($users) ? $users : [];
}

function save_users(array $users): bool
{
    $dir = dirname(USERS_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $json = json_encode(array_values($users), JSON_PRETTY_PRINT);
    return file_put_contents(USERS_FILE, $json, LOCK_EX) !== false;
}

function find_user(string $identifier): ?array
{
    $needle = strtolower(trim($identifier));

    foreach (load_users() as $user) {
        $username = strtolower((string) ($user['username'] ?? ''));
        $email = strtolower((string) ($user['email'] ?? ''));
        if ($needle === $username || $needle === $email) {
            return $user;
        }
    }

    return null;
}

function is_valid_email(string $value): bool
{
    return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
}

function looks_like_email(string $value): bool
{
    return str_contains($value, '@');
}

function validate_identifier(string $value, string $label = 'Email or username'): ?string
{
    if ($value === '') {
        return $label . ' is required.';
    }

    if (looks_like_email($value)) {
        if (!is_valid_email($value)) {
            return 'Please enter a valid email address.';
        }
        return null;
    }

    if (!preg_match('/^[A-Za-z0-9._-]{3,30}$/', $value)) {
        return 'Username must be 3–30 characters and may include letters, numbers, dots, hyphens, or underscores.';
    }

    return null;
}

function validate_password(string $password): ?string
{
    if ($password === '') {
        return 'Password is required.';
    }

    if (strlen($password) < MIN_PASSWORD_LENGTH) {
        return 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters.';
    }

    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        return 'Password must include at least one letter and one number.';
    }

    return null;
}

function validate_full_name(string $name): ?string
{
    if ($name === '') {
        return 'Full name is required.';
    }

    if (mb_strlen($name) < 2) {
        return 'Full name must be at least 2 characters.';
    }

    if (!preg_match("/^[\\p{L} .'-]+$/u", $name)) {
        return 'Full name may only contain letters, spaces, hyphens, apostrophes, and periods.';
    }

    return null;
}

function flash_success(): ?string
{
    $message = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_success']);
    return $message;
}

function flash_error(): ?string
{
    $message = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_error']);
    return $message;
}
