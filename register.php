<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    redirect('dashboard.php');
}

$errors = [];
$fullName = '';
$identifier = '';
$password = '';
$confirmPassword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $nameError = validate_full_name($fullName);
    $identifierError = validate_identifier($identifier);
    $passwordError = validate_password($password);

    if ($nameError) {
        $errors['full_name'] = $nameError;
    }
    if ($identifierError) {
        $errors['identifier'] = $identifierError;
    }
    if ($passwordError) {
        $errors['password'] = $passwordError;
    }
    if ($confirmPassword === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $existing = find_user($identifier);
        if ($existing) {
            $errors['identifier'] = 'That email or username is already registered.';
        } else {
            $users = load_users();
            $isEmail = looks_like_email($identifier);
            $users[] = [
                'id' => bin2hex(random_bytes(8)),
                'full_name' => $fullName,
                'username' => $isEmail ? explode('@', $identifier)[0] : $identifier,
                'email' => $isEmail ? strtolower($identifier) : '',
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('c'),
            ];

            if (!save_users($users)) {
                $errors['form'] = 'Unable to save your account. Please try again.';
            } else {
                $_SESSION['flash_success'] = 'Registration successful. You can now log in.';
                redirect('login.php');
            }
        }
    }
}

$pageTitle = 'Register';
$formClass = 'auth-card--register';
require __DIR__ . '/includes/layout-start.php';
?>
                <h1>Register</h1>
            </header>
            <div class="auth-body">
                <?php if (!empty($errors['form'])): ?>
                    <div class="alert alert-error"><?= h($errors['form']) ?></div>
                <?php endif; ?>

                <form id="register-form" method="post" action="register.php" novalidate>
                    <div class="field">
                        <label for="full_name">Full name</label>
                        <div class="input-wrap">
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?= h($fullName) ?>"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                class="<?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                            >
                        </div>
                        <p class="field-error <?= isset($errors['full_name']) ? 'is-visible' : '' ?>" data-error-for="full_name">
                            <?= h($errors['full_name'] ?? '') ?>
                        </p>
                    </div>

                    <div class="field">
                        <label for="identifier">Email or username</label>
                        <div class="input-wrap">
                            <input
                                type="text"
                                id="identifier"
                                name="identifier"
                                value="<?= h($identifier) ?>"
                                placeholder="Enter your email or username"
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
                                autocomplete="new-password"
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

                    <div class="field">
                        <label for="confirm_password">Confirm password</label>
                        <div class="input-wrap">
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Re-enter your password"
                                autocomplete="new-password"
                                class="<?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                            >
                            <button class="toggle-password" type="button" data-toggle-password="confirm_password" aria-label="Show password">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                        <p class="field-error <?= isset($errors['confirm_password']) ? 'is-visible' : '' ?>" data-error-for="confirm_password">
                            <?= h($errors['confirm_password'] ?? '') ?>
                        </p>
                    </div>

                    <button class="btn" type="submit">Register</button>
                </form>

                <p class="switch-text">Already have an account? <a href="login.php">Log in</a></p>
            </div>
<?php
require __DIR__ . '/includes/layout-end.php';
