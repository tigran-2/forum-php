<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Simple template renderer with layout support.
 */
class View
{
    private static string $templatesPath = '';
    private static string $layoutsPath = '';
    private static ?string $currentLayout = 'main';

    public static function init(string $templatesPath): void
    {
        self::$templatesPath = rtrim($templatesPath, '/');
        self::$layoutsPath = self::$templatesPath . '/layouts';
    }

    /**
     * Set the layout to use (null for no layout).
     */
    public static function setLayout(?string $layout): void
    {
        self::$currentLayout = $layout;
    }

    /**
     * Render a template with optional data.
     */
    public static function render(string $template, array $data = []): string
    {
        $templatePath = self::$templatesPath . '/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        // Render template content
        $content = self::renderFile($templatePath, $data);

        // Wrap in layout if set
        if (self::$currentLayout !== null) {
            $layoutPath = self::$layoutsPath . '/' . self::$currentLayout . '.php';
            if (file_exists($layoutPath)) {
                $data['content'] = $content;
                $content = self::renderFile($layoutPath, $data);
            }
        }

        return $content;
    }

    /**
     * Render and output a template.
     */
    public static function display(string $template, array $data = []): void
    {
        echo self::render($template, $data);
    }

    /**
     * Render a partial template (no layout).
     */
    public static function partial(string $template, array $data = []): string
    {
        $templatePath = self::$templatesPath . '/partials/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Partial not found: {$template}");
        }

        return self::renderFile($templatePath, $data);
    }

    private static function renderFile(string $path, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        return ob_get_clean() ?: '';
    }

    /**
     * Escape HTML entities.
     */
    public static function e(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Generate URL.
     */
    public static function url(string $path, array $params = []): string
    {
        $url = '/' . ltrim($path, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }
}

/**
 * Global helper functions for templates.
 */
function e(string $s): string
{
    return View::e($s);
}

function url(string $path, array $params = []): string
{
    return View::url($path, $params);
}

function partial(string $template, array $data = []): string
{
    return View::partial($template, $data);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(Session::csrfToken()) . '">';
}

function current_user(): ?array
{
    return Session::user();
}

function flash(): ?array
{
    return Session::getFlash();
}
