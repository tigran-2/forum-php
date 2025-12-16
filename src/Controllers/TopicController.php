<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\Topic;
use App\Models\Comment;
use App\Helpers\Messages;
use App\Helpers\Validator;

use function App\Core\redirect;
use function App\Core\is_post;

/**
 * Topic controller with CRUD operations.
 */
class TopicController
{
    /**
     * Show a single topic with comments.
     */
    public function show(string $id): void
    {
        $topicId = Validator::getInt($id, 0, 1);

        if ($topicId <= 0) {
            $this->notFound();
            return;
        }

        $topic = Topic::find($topicId);

        if (!$topic) {
            $this->notFound();
            return;
        }

        $comments = Comment::forTopic($topicId);
        $user = Session::user();

        View::display('topics/show', [
            'title' => $topic['title'],
            'topic' => $topic,
            'comments' => $comments,
            'comment_errors' => [],
            'old_comment' => '',
            'is_owner' => $user && (int)$user['id'] === (int)$topic['user_id'],
        ]);
    }

    /**
     * Add a comment to a topic.
     */
    public function addComment(string $id): void
    {
        $topicId = Validator::getInt($id, 0, 1);

        if ($topicId <= 0) {
            $this->notFound();
            return;
        }

        $topic = Topic::find($topicId);

        if (!$topic) {
            $this->notFound();
            return;
        }

        if (!is_post()) {
            redirect("/topics/{$topicId}");
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $user = Session::user();
        if (!$user) {
            Session::flash('error', Messages::AUTH_REQUIRED);
            redirect('/login');
            return;
        }

        $body = Validator::getString($_POST['body'] ?? '');
        $result = Comment::create((int)$user['id'], $topicId, $body);

        if (!$result['ok']) {
            $comments = Comment::forTopic($topicId);
            View::display('topics/show', [
                'title' => $topic['title'],
                'topic' => $topic,
                'comments' => $comments,
                'comment_errors' => $result['errors'] ?? [],
                'old_comment' => $body,
                'is_owner' => (int)$user['id'] === (int)$topic['user_id'],
            ]);
            return;
        }

        Session::csrfRotate();
        Session::flash('ok', Messages::COMMENT_ADDED);
        redirect("/topics/{$topicId}");
    }

    /**
     * Show create topic form.
     */
    public function create(): void
    {
        View::display('topics/create', [
            'title' => 'Создать тему',
            'errors' => [],
            'old' => ['title' => '', 'body' => ''],
        ]);
    }

    /**
     * Store a new topic.
     */
    public function store(): void
    {
        if (!is_post()) {
            $this->create();
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $user = Session::user();
        if (!$user) {
            Session::flash('error', Messages::AUTH_REQUIRED);
            redirect('/login');
            return;
        }

        $title = Validator::getString($_POST['title'] ?? '');
        $body = Validator::getString($_POST['body'] ?? '');

        $result = Topic::create((int)$user['id'], $title, $body);

        if (!$result['ok']) {
            View::display('topics/create', [
                'title' => 'Создать тему',
                'errors' => $result['errors'] ?? [],
                'old' => ['title' => $title, 'body' => $body],
            ]);
            return;
        }

        Session::csrfRotate();
        Session::flash('ok', Messages::TOPIC_CREATED);
        redirect("/topics/{$result['id']}");
    }

    /**
     * Show edit topic form.
     */
    public function edit(string $id): void
    {
        $topicId = Validator::getInt($id, 0, 1);
        $topic = Topic::find($topicId);

        if (!$topic) {
            $this->notFound();
            return;
        }

        $user = Session::user();
        if (!$user || (int)$user['id'] !== (int)$topic['user_id']) {
            Session::flash('error', Messages::TOPIC_FORBIDDEN);
            redirect("/topics/{$topicId}");
            return;
        }

        View::display('topics/edit', [
            'title' => 'Редактировать тему',
            'topic' => $topic,
            'errors' => [],
            'old' => ['title' => $topic['title'], 'body' => $topic['body']],
        ]);
    }

    /**
     * Update a topic.
     */
    public function update(string $id): void
    {
        $topicId = Validator::getInt($id, 0, 1);
        $topic = Topic::find($topicId);

        if (!$topic) {
            $this->notFound();
            return;
        }

        $user = Session::user();
        if (!$user || (int)$user['id'] !== (int)$topic['user_id']) {
            Session::flash('error', Messages::TOPIC_FORBIDDEN);
            redirect("/topics/{$topicId}");
            return;
        }

        if (!is_post()) {
            redirect("/topics/{$topicId}/edit");
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $title = Validator::getString($_POST['title'] ?? '');
        $body = Validator::getString($_POST['body'] ?? '');

        $result = Topic::update($topicId, $title, $body);

        if (!$result['ok']) {
            View::display('topics/edit', [
                'title' => 'Редактировать тему',
                'topic' => $topic,
                'errors' => $result['errors'] ?? [],
                'old' => ['title' => $title, 'body' => $body],
            ]);
            return;
        }

        Session::csrfRotate();
        Session::flash('ok', Messages::TOPIC_UPDATED);
        redirect("/topics/{$topicId}");
    }

    /**
     * Delete a topic.
     */
    public function delete(string $id): void
    {
        $topicId = Validator::getInt($id, 0, 1);
        $topic = Topic::find($topicId);

        if (!$topic) {
            $this->notFound();
            return;
        }

        $user = Session::user();
        if (!$user || (int)$user['id'] !== (int)$topic['user_id']) {
            Session::flash('error', Messages::TOPIC_FORBIDDEN);
            redirect("/topics/{$topicId}");
            return;
        }

        if (!is_post()) {
            redirect("/topics/{$topicId}");
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        Topic::delete($topicId);

        Session::csrfRotate();
        Session::flash('ok', Messages::TOPIC_DELETED);
        redirect('/');
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::display('errors/404', ['title' => 'Не найдено']);
    }
}
