<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Topic;
use App\Helpers\Validator;

/**
 * Home controller (topic list).
 */
class HomeController
{
    /**
     * Show home page with topic list.
     */
    /**
     * Show home page with topic list.
     * 
     * Handles pagination and search functionality.
     * GET params: page (int), q (string).
     */
    public function index(): void
    {
        $page = Validator::getInt($_GET['page'] ?? 1, 1, 1, 1000);
        $search = Validator::getString($_GET['q'] ?? null);
        $search = $search !== '' ? $search : null;

        $result = Topic::paginate($page, Topic::PER_PAGE, $search);

        View::display('home/index', [
            'title' => 'Темы',
            'topics' => $result['items'],
            'pagination' => [
                'page' => $result['page'],
                'total_pages' => $result['total_pages'],
                'total' => $result['total'],
            ],
            'search' => $search,
        ]);
    }
}
