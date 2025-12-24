<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Helpers\Messages;
use App\Helpers\Validator;

use function App\Core\redirect;
use function App\Core\is_post;

/**
 * Profile controller.
 */
class ProfileController
{
    /**
     * Show user profile.
     */
    /**
     * Show user profile.
     * 
     * Displays user info and stats (topic count, comment count).
     * @param string $id User ID
     */
    public function show(string $id): void
    {
        $userId = Validator::getInt($id, 0, 1);
        $user = User::find($userId);

        if (!$user) {
            $this->notFound();
            return;
        }

        $currentUser = Session::user();
        $isOwner = $currentUser && (int)$currentUser['id'] === $userId;

        View::display('profile/show', [
            'title' => $user['first_name'] . ' ' . $user['last_name'],
            'profile' => $user,
            'is_owner' => $isOwner,
            'topic_count' => User::getTopicCount($userId),
            'comment_count' => User::getCommentCount($userId),
        ]);
    }

    /**
     * Show edit profile form.
     */
    /**
     * Show edit profile form.
     * 
     * Loads current user data into the form.
     */
    public function edit(): void
    {
        $user = Session::user();
        if (!$user) {
            Session::flash('error', Messages::AUTH_REQUIRED);
            redirect('/login');
            return;
        }

        $fullUser = User::find((int)$user['id']);

        View::display('profile/edit', [
            'title' => 'Редактировать профиль',
            'profile' => $fullUser,
            'errors' => [],
            'old' => [
                'first_name' => $fullUser['first_name'],
                'last_name' => $fullUser['last_name'],
                'phone' => $fullUser['phone'],
            ],
        ]);
    }

    /**
     * Update profile.
     */
    /**
     * Update profile.
     * 
     * Validates input and updates user record.
     * Also updates the session user data.
     */
    public function update(): void
    {
        $user = Session::user();
        if (!$user) {
            Session::flash('error', Messages::AUTH_REQUIRED);
            redirect('/login');
            return;
        }

        if (!is_post()) {
            redirect('/profile/edit');
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $fullUser = User::find((int)$user['id']);
        $old = [
            'first_name' => Validator::getString($_POST['first_name'] ?? ''),
            'last_name' => Validator::getString($_POST['last_name'] ?? ''),
            'phone' => Validator::getString($_POST['phone'] ?? ''),
        ];

        $result = User::update((int)$user['id'], $old);

        if (!$result['ok']) {
            View::display('profile/edit', [
                'title' => 'Редактировать профиль',
                'profile' => $fullUser,
                'errors' => $result['errors'] ?? [],
                'old' => $old,
            ]);
            return;
        }

        // Update session user data
        $updatedUser = User::find((int)$user['id']);
        Session::setUser([
            'id' => $updatedUser['id'],
            'email' => $updatedUser['email'],
            'first_name' => $updatedUser['first_name'],
            'last_name' => $updatedUser['last_name'],
        ]);

        Session::csrfRotate();
        Session::flash('ok', Messages::PROFILE_UPDATED);
        redirect("/profile/{$user['id']}");
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::display('errors/404', ['title' => 'Не найдено']);
    }
}
