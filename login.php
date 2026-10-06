<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    redirect('dashboard.php');
}

$errors = [];
$identifier = '';
$password = '';
$success = flash_success();
$flashError = flash_error();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $identifierError = validate_identifier($identifier);
    $passwordError = validate_password($password);

    if ($identifierError) {
        $errors['identifier'] = $identifierError;
    }
    if ($passwordError) {
        $errors['password'] = $passwordError;
    }

    if (!$errors) {
        $user = find_user($identifier);
        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $errors['form'] = 'Incorrect email/username or password.';
        } else {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'username' => $user['username'],
                'email' => $user['email'],
            ];
            $_SESSION['flash_success'] = 'Welcome back, ' . $user['full_name'] . '! You are now logged in.';
            redirect('dashboard.php');
        }
    }
}

$pageTitle = 'Login';
$formClass = '';
require __DIR__ . '/includes/layout-start.php';
?>
                <h1>Login</h1>
            </header>
            <div class="auth-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= h($success) ?></div>
                <?php endif; ?>
                <?php if ($flashError): ?>
                    <div class="alert alert-error"><?= h($flashError) ?></div>
                <?php endif; ?>
                <?php if (!empty($errors['form'])): ?>
                    <div class="alert alert-error"><?= h($errors['form']) ?></div>
                <?php endif; ?>

                <form id="login-form" method="post" action="login.php" novalidate>
                    <div class="field">
                        <label for="identifier">Email or username</label>
                        <div class="input-wrap">
                            <input
                                type="text"
                                id="identifier"
                                name="identifier"
                                value="<?= h($identifier) ?>"
                                placeholder="Enter your username"
                                autocomplete="username"
                                class="<?= isset($errors['identifier']) ? 'is-invalid' : '' ?>"
                            >
                        </div>
                        <p class="field-error <?= isset($errors['identifier']) ? 'is-visible' : '' ?>" data-error-for="identifier">
                            <?= h($errors['identifier'] ?? '') ?>
                        </p>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                class="<?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            >
                            <button class="toggle-password" type="button" data-toggle-password="password" aria-label="Show password">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                        <p class="field-error <?= isset($errors['password']) ? 'is-visible' : '' ?>" data-error-for="password">
                            <?= h($errors['password'] ?? '') ?>
                        </p>
                    </div>

                    <button class="btn" type="submit">Login</button>
                </form>

                <p class="switch-text">Don't have an account? <a href="register.php">Sign up</a></p>
            </div>
<?php
require __DIR__ . '/includes/layout-end.php';
