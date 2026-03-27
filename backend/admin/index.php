<?php

declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';

startSessionIfNeeded();

if (currentUser() && (currentUser()['role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;
