<?php
// public/bootstrap.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/db.php';

// Initialize CSRF token on first visit.
csrf_token();
