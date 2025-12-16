<?php
declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;

/**
 * Main application class.
 */
class App
{
    private static ?App $instance = null;
    private Router $router;
    private bool $debug = false;

    private function __construct()
    {
        $this->router = new Router();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Bootstrap the application.
     */
    public function boot(): void
    {
        // Load environment variables
        $this->loadEnv();

        // Set error handling
        $this->setupErrorHandling();

        // Start session
        Session::start();

        // Initialize view
        View::init(dirname(__DIR__, 2) . '/templates');

        // Register middleware
        $this->registerMiddleware();
    }

    private function loadEnv(): void
    {
        $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
            if (class_exists(Dotenv::class)) {
                $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->safeLoad();
            }
        }
    }

    private function setupErrorHandling(): void
    {
        set_exception_handler(function (\Throwable $e) {
            $this->handleException($e);
        });

        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    private function handleException(\Throwable $e): void
    {
        http_response_code(500);

        if ($this->debug) {
            echo '<h1>Error</h1>';
            echo '<p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } else {
            echo '<h1>500 Internal Server Error</h1>';
            echo '<p>Something went wrong. Please try again later.</p>';
        }

        // Log error
        error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    }

    private function registerMiddleware(): void
    {
        // Auth middleware
        $this->router->addMiddleware('auth', function () {
            if (Session::user() === null) {
                Session::flash('error', 'Войдите, чтобы продолжить.');
                $this->redirect('/login');
                return false;
            }
            return true;
        });

        // Guest middleware (for login/register pages)
        $this->router->addMiddleware('guest', function () {
            if (Session::user() !== null) {
                $this->redirect('/');
                return false;
            }
            return true;
        });

        // CSRF middleware
        $this->router->addMiddleware('csrf', function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!Session::csrfCheck()) {
                    http_response_code(400);
                    echo 'Bad Request (CSRF)';
                    return false;
                }
            }
            return true;
        });

        // Rate limit for login
        $this->router->addMiddleware('rate_limit_login', function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!Session::checkRateLimit('login', 5, 300)) {
                    $remaining = Session::getRateLimitReset('login', 300);
                    Session::flash('error', "Слишком много попыток. Попробуйте через {$remaining} секунд.");
                    return true; // Allow page render to show error
                }
            }
            return true;
        });
    }

    /**
     * Run the application.
     */
    public function run(): void
    {
        $this->router->dispatch();
    }

    /**
     * Redirect to a URL.
     */
    public function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }
}

/**
 * Global redirect helper.
 */
function redirect(string $path): void
{
    App::getInstance()->redirect($path);
}

/**
 * Check if current request is POST.
 */
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}
