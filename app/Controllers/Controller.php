<?php

namespace App\Controllers;

class Controller
{
    protected $pdo;
    protected $blade;

    public function __construct($pdo, $blade)
    {
        $this->pdo = $pdo;
        $this->blade = $blade;
    }

    /* ===============================
       VIEW RENDERING
       =============================== */
    protected function view(string $view, array $data = []): void
    {
        if (!isset($_SESSION['base_url'])) {
            $_SESSION['base_url'] = $this->getBaseUrl();
        }

        $data['base_url'] = $_SESSION['base_url'];

        echo $this->blade->render($view, $data);
    }

    /* ===============================
       REDIRECT (FIXED)
       =============================== */
    protected function redirect(string $path): void
    {
        $baseUrl = $_SESSION['base_url'] ?? $this->getBaseUrl();

        if ($path !== '' && $path[0] !== '/') {
            $path = '/' . $path;
        }

        header('Location: ' . rtrim($baseUrl, '/') . $path);
        exit;
    }

    /* ===============================
       HELPERS
       =============================== */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? ($this->getBaseUrl() . '/');
        header('Location: ' . $referer);
        exit;
    }

    protected function auth(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function user()
    {
        if (!$this->auth()) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    /* ===============================
       BASE URL (NO /public EVER)
       =============================== */
    protected function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            ? 'https'
            : 'http';

        $host = $_SERVER['HTTP_HOST'];

        // SCRIPT_NAME = /hostel-management-system/public/index.php
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // /hostel-management-system/public
        $basePath  = rtrim(str_replace('/public', '', $scriptDir), '');

        return $protocol . '://' . $host . $basePath;
    }
}
