<?php
declare(strict_types=1);
session_start();
date_default_timezone_set('Africa/Nairobi');

const DB_FILE = __DIR__ . '/nyota.sqlite';
const APP_NAME = 'NYOTA ICT Training Assessment';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("PRAGMA foreign_keys = ON");
    }
    return $pdo;
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function require_login(): void {
    if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
}
function require_admin(): void {
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') { http_response_code(403); exit('Access denied'); }
}
function require_trainee(): void {
    require_login();
    if ($_SESSION['user']['role'] !== 'trainee') { header('Location: admin.php'); exit; }
}
function flash(string $type, string $msg): void { $_SESSION['flash'] = [$type,$msg]; }
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        [$type,$msg] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert '.e($type).'">'.e($msg).'</div>';
    }
}
function grade(float $pct): string {
    if ($pct >= 80) return 'A';
    if ($pct >= 70) return 'B';
    if ($pct >= 60) return 'C';
    if ($pct >= 50) return 'D';
    return 'E';
}
