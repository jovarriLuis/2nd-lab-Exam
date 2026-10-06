(function () {
    const MIN_PASSWORD_LENGTH = 8;

    function showError(input, message) {
        input.classList.add('is-invalid');
        const error = document.querySelector('[data-error-for="' + input.id + '"]');
        if (error) {
            error.textContent = message;
            error.classList.add('is-visible');
        }
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        const error = document.querySelector('[data-error-for="' + input.id + '"]');
        if (error) {
            error.textContent = '';
            error.classList.remove('is-visible');
        }
    }

    function looksLikeEmail(value) {
        return value.indexOf('@') !== -1;
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function validateIdentifier(input) {
        const value = input.value.trim();
        if (!value) {
            showError(input, 'Email or username is required.');
            return false;
        }
        if (looksLikeEmail(value) && !isValidEmail(value)) {
            showError(input, 'Please enter a valid email address.');
            return false;
        }
        if (!looksLikeEmail(value) && !/^[A-Za-z0-9._-]{3,30}$/.test(value)) {
            showError(input, 'Username must be 3–30 characters and may include letters, numbers, dots, hyphens, or underscores.');
            return false;
        }
        clearError(input);
        return true;
    }

    function validateFullName(input) {
        const value = input.value.trim();
        if (!value) {
            showError(input, 'Full name is required.');
            return false;
        }
        if (value.length < 2) {
            showError(input, 'Full name must be at least 2 characters.');
            return false;
        }
        if (!/^[\p{L} .'-]+$/u.test(value)) {
            showError(input, "Full name may only contain letters, spaces, hyphens, apostrophes, and periods.");
            return false;
        }
        clearError(input);
        return true;
    }

    function validatePassword(input) {
        const value = input.value;
        if (!value) {
            showError(input, 'Password is required.');
            return false;
        }
        if (value.length < MIN_PASSWORD_LENGTH) {
            showError(input, 'Password must be at least ' + MIN_PASSWORD_LENGTH + ' characters.');
            return false;
        }
        if (!/[A-Za-z]/.test(value) || !/\d/.test(value)) {
            showError(input, 'Password must include at least one letter and one number.');
            return false;
        }
        clearError(input);
        return true;
    }

    function validateConfirmPassword(passwordInput, confirmInput) {
        if (!confirmInput.value) {
            showError(confirmInput, 'Please confirm your password.');
            return false;
        }
        if (passwordInput.value !== confirmInput.value) {
            showError(confirmInput, 'Passwords do not match.');
            return false;
        }
        clearError(confirmInput);
        return true;
    }

    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.getAttribute('data-toggle-password'));
            if (!input) {
                return;
            }
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            button.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
            button.innerHTML = hidden ? eyeOffIcon() : eyeIcon();
        });
    });

    function eyeIcon() {
        return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>';
    }

    function eyeOffIcon() {
        return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10.6 10.6A3 3 0 0 0 12 15a3 3 0 0 0 2.4-1.2" stroke="currentColor" stroke-width="1.8"/><path d="M9.9 5.2A11.3 11.3 0 0 1 12 5c6.5 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.2M6.1 6.1C3.8 7.8 2 12 2 12s3.5 7 10 7c1.7 0 3.2-.4 4.5-1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            const identifier = document.getElementById('identifier');
            const password = document.getElementById('password');
            const identifierOk = validateIdentifier(identifier);
            const passwordOk = validatePassword(password);
            if (!identifierOk || !passwordOk) {
                event.preventDefault();
            }
        });
    }

    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            const fullName = document.getElementById('full_name');
            const identifier = document.getElementById('identifier');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const nameOk = validateFullName(fullName);
            const identifierOk = validateIdentifier(identifier);
            const passwordOk = validatePassword(password);
            const confirmOk = validateConfirmPassword(password, confirmPassword);
            if (!nameOk || !identifierOk || !passwordOk || !confirmOk) {
                event.preventDefault();
            }
        });
    }
})();
