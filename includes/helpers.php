<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim($GLOBALS['config']['app']['base_url'] ?? '', '/');

    $path = '/' . ltrim($path, '/');

    return $base . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post(string $key, string $default = ''): string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

function cfg(string $section, ?string $key = null)
{
    $section = $GLOBALS['config'][$section] ?? null;
    if ($key === null) {
        return $section;
    }
    return $section[$key] ?? null;
}

function validate_donor_payload(array $in): array
{
    $errors = [];

    if (!preg_match('/^[A-Za-z][A-Za-z .\'\-]{1,118}$/', $in['full_name'] ?? '')) {
        $errors[] = 'Full name must be 2–120 letters (no digits).';
    }
    if (!preg_match('/^\d{1,3}$/', $in['age'] ?? '') || (int)$in['age'] < 18 || (int)$in['age'] > 65) {
        $errors[] = 'Age must be between 18 and 65.';
    }
    if (!in_array($in['blood_group'] ?? '', ['A+','A-','B+','B-','AB+','AB-','O+','O-'], true)) {
        $errors[] = 'Blood group must be one of A+, A-, B+, B-, AB+, AB-, O+, O-.';
    }
    if (!preg_match('/^[6-9]\d{9}$/', $in['phone'] ?? '')) {
        $errors[] = 'Phone must be a 10-digit number starting with 6, 7, 8 or 9.';
    }
    if (!filter_var($in['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email address looks invalid.';
    }
    if (!preg_match('/^\d{6}$/', $in['pincode'] ?? '')) {
        $errors[] = 'Pincode must be exactly 6 digits.';
    }
    $pwd = $in['password'] ?? '';
    if (strlen($pwd) < 8 || !preg_match('/[A-Z]/', $pwd) || !preg_match('/[^A-Za-z0-9]/', $pwd)) {
        $errors[] = 'Password must be at least 8 characters with one uppercase letter and one symbol.';
    }
    if (($in['password'] ?? '') !== ($in['confirm_password'] ?? '')) {
        $errors[] = 'Passwords do not match.';
    }

    return $errors;
}
