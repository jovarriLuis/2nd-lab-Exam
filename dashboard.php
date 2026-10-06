<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_login();

$user = current_user();
$success = flash_success();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="page">
        <section class="dashboard">
            <header class="auth-header">
                <h1>Welcome</h1>
            </header>
            <div class="dashboard-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= h($success) ?></div>
                <?php endif; ?>
                <p>You are logged in as <strong><?= h((string) $user['full_name']) ?></strong>.</p>
                <p class="meta">Username: <?= h((string) $user['username']) ?></p>
                <?php if (!empty($user['email'])): ?>
                    <p class="meta">Email: <?= h((string) $user['email']) ?></p>
                <?php endif; ?>
                <a class="btn btn-link" href="logout.php">Log out</a>
            </div>
        </section>
    </main>
</body>
</html>
