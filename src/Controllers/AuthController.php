<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Helpers\Messages;

use function App\Core\redirect;
use function App\Core\is_post;

/**
 * Authentication controller.
 */
class AuthController
{
    /**
     * Show login form.
     */
    public function showLogin(): void
    {
        View::display('auth/login', [
            'title' => 'Вход',
            'error' => null,
            'old_email' => '',
        ]);
    }

    /**
     * Process login.
     */
    /**
     * Process login.
     * 
     * Validates input, checks rate limits, verifies credentials,
     * and sets up the session if successful.
     */
    public function login(): void
    {
        if (!is_post()) {
            $this->showLogin();
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Check rate limit
        if (!Session::checkRateLimit('login', 5, 300)) {
            $remaining = Session::getRateLimitReset('login', 300);
            View::display('auth/login', [
                'title' => 'Вход',
                'error' => "Слишком много попыток. Попробуйте через {$remaining} секунд.",
                'old_email' => $email,
            ]);
            return;
        }

        if ($email === '' || $password === '') {
            View::display('auth/login', [
                'title' => 'Вход',
                'error' => Messages::EMAIL_PASSWORD_REQUIRED,
                'old_email' => $email,
            ]);
            return;
        }

        $user = User::findByEmail($email);

        if (!$user || !User::verifyPassword($user, $password)) {
            View::display('auth/login', [
                'title' => 'Вход',
                'error' => Messages::INVALID_CREDENTIALS,
                'old_email' => $email,
            ]);
            return;
        }

        // Rehash password if needed
        User::rehashIfNeeded((int)$user['id'], $password, $user['password_hash']);

        // Clear rate limit on successful login
        Session::clearRateLimit('login');

        // Rotate CSRF token
        Session::csrfRotate();

        // Set user in session
        unset($user['password_hash']);
        Session::setUser($user);

        Session::flash('ok', Messages::LOGIN_SUCCESS);
        redirect('/');
    }

    /**
     * Show registration form.
     */
    public function showRegister(): void
    {
        View::display('auth/register', [
            'title' => 'Регистрация',
            'errors' => [],
            'old' => [
                'email' => '',
                'first_name' => '',
                'last_name' => '',
                'dob' => '',
                'phone' => '',
            ],
        ]);
    }

    /**
     * Process registration.
     */
    /**
     * Process registration.
     * 
     * Validates input, creates a new user, and redirects to login.
     */
    public function register(): void
    {
        if (!is_post()) {
            $this->showRegister();
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $old = [
            'email' => trim($_POST['email'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'dob' => trim($_POST['dob'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
        ];

        $result = User::create($_POST);

        if (!$result['ok']) {
            View::display('auth/register', [
                'title' => 'Регистрация',
                'errors' => $result['errors'] ?? [],
                'old' => $old,
            ]);
            return;
        }

        // Rotate CSRF token
        Session::csrfRotate();

        Session::flash('ok', Messages::REGISTER_SUCCESS);
        redirect('/login');
    }

    /**
     * Process logout.
     */
    /**
     * Process logout.
     * 
     * Clears user session and rotates CSRF token.
     * Requires POST request.
     */
    public function logout(): void
    {
        if (!is_post()) {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        Session::clearUser();
        Session::csrfRotate();
        Session::flash('ok', Messages::LOGOUT_SUCCESS);
        redirect('/');
    }
}
