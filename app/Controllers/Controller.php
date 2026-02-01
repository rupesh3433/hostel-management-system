<?php

namespace App\Controllers;

class Controller {
    protected $pdo;
    protected $blade;
    
    public function __construct($pdo, $blade) {
        $this->pdo = $pdo;
        $this->blade = $blade;
    }
    
    protected function view($view, $data = []) {
        // Add base_url to all views automatically
        $data['base_url'] = $_SESSION['base_url'] ?? $this->getBaseUrl();
        
        echo $this->blade->render($view, $data);
    }
    
    protected function redirect($path) {
        // Get base path from session or calculate it
        $basePath = $_SESSION['base_path'] ?? $this->getBasePath();
        
        // Ensure path starts with /
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        
        // Combine base path with requested path
        $url = $basePath . $path;
        
        header("Location: {$url}");
        exit;
    }
    
    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }
    
    protected function auth() {
        return isset($_SESSION['user_id']);
    }
    
    protected function user() {
        if (!$this->auth()) return null;
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    
    protected function getBasePath() {
        // Calculate base path similar to App.php
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = dirname($scriptName);
        
        if ($basePath !== '/') {
            $basePath = rtrim($basePath, '/');
        }
        
        return $basePath;
    }
    
    protected function getBaseUrl() {
        if (isset($_SESSION['base_url'])) {
            return $_SESSION['base_url'];
        }
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $basePath = $this->getBasePath();
        
        return $protocol . '://' . $host . $basePath;
    }
    
    // Helper method to generate URLs with base path
    protected function url($path = '') {
        $basePath = $_SESSION['base_path'] ?? $this->getBasePath();
        
        if ($path && strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        
        return $basePath . $path;
    }
}