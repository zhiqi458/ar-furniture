<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_auth(): bool {
    return isset($_SESSION['admin_id']);
}

function require_auth(): void {
    if (!check_auth()) {
        header("Location: login.php");
        exit();
    }
}