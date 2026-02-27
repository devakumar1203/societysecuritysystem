<?php
declare(strict_types=1);



const DB_HOST = 'localhost';
const DB_NAME = 'society_security';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

function db(): mysqli {
    static $c = null;
    if ($c instanceof mysqli) return $c;
    $c = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($c->connect_errno) { echo 'DB connection failed'; exit; }
    $c->set_charset(DB_CHARSET);
    return $c;
}

function start_app_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
}

function require_role_simple(string $role): void {
    start_app_session();
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header('Location: index.php');
        exit;
    }
}

function json_out(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}


