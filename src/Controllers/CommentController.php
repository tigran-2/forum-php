<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Models\Comment;
use App\Helpers\Messages;
use App\Helpers\Validator;

use function App\Core\redirect;
use function App\Core\is_post;

/**
 * Comment controller for edit/delete operations.
 */
class CommentController
{
    /**
     * Edit a comment.
     */
    public function edit(string $id): void
    {
        $commentId = Validator::getInt($id, 0, 1);
        $comment = Comment::find($commentId);

        if (!$comment) {
            $this->notFound();
            return;
        }

        $user = Session::user();
        if (!$user || (int)$user['id'] !== (int)$comment['user_id']) {
            Session::flash('error', Messages::COMMENT_FORBIDDEN);
            redirect("/topics/{$comment['topic_id']}");
            return;
        }

        View::display('comments/edit', [
            'title' => 'Редактировать комментарий',
            'comment' => $comment,
            'errors' => [],
            'old' => ['body' => $comment['body']],
        ]);
    }

    /**
     * Update a comment.
     */
    public function update(string $id): void
    {
        $commentId = Validator::getInt($id, 0, 1);
        $comment = Comment::find($commentId);

        if (!$comment) {
            $this->notFound();
            return;
        }

        $user = Session::user();
        if (!$user || (int)$user['id'] !== (int)$comment['user_id']) {
            Session::flash('error', Messages::COMMENT_FORBIDDEN);
            redirect("/topics/{$comment['topic_id']}");
            return;
        }

        if (!is_post()) {
            redirect("/comments/{$commentId}/edit");
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $body = Validator::getString($_POST['body'] ?? '');
        $result = Comment::update($commentId, $body);

        if (!$result['ok']) {
            View::display('comments/edit', [
                'title' => 'Редактировать комментарий',
                'comment' => $comment,
                'errors' => $result['errors'] ?? [],
                'old' => ['body' => $body],
            ]);
            return;
        }

        Session::csrfRotate();
        Session::flash('ok', Messages::COMMENT_UPDATED);
        redirect("/topics/{$comment['topic_id']}");
    }

    /**
     * Delete a comment.
     */
    public function delete(string $id): void
    {
        $commentId = Validator::getInt($id, 0, 1);
        $comment = Comment::find($commentId);

        if (!$comment) {
            $this->notFound();
            return;
        }

        $user = Session::user();
        if (!$user || (int)$user['id'] !== (int)$comment['user_id']) {
            Session::flash('error', Messages::COMMENT_FORBIDDEN);
            redirect("/topics/{$comment['topic_id']}");
            return;
        }

        if (!is_post()) {
            redirect("/topics/{$comment['topic_id']}");
            return;
        }

        // Check CSRF
        if (!Session::csrfCheck()) {
            http_response_code(400);
            echo 'Bad Request (CSRF)';
            return;
        }

        $topicId = $comment['topic_id'];
        Comment::delete($commentId);

        Session::csrfRotate();
        Session::flash('ok', Messages::COMMENT_DELETED);
        redirect("/topics/{$topicId}");
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::display('errors/404', ['title' => 'Не найдено']);
    }
}
