<?php

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

class AuthController extends Controller {
    
    public function loginForm() {
        if ($this->auth()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth.login');
    }
    
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // Redirect to dashboard WITH base path
            $this->redirect('/dashboard');
        }
        
        $_SESSION['error'] = 'Invalid email or password';
        $this->redirect('/login');
    }
    
    public function registerForm() {
        if ($this->auth()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth.register');
    }
    
    public function register() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';
        
        // Check if email exists
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'Email already registered';
            $this->redirect('/register');
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role]);
        
        $_SESSION['success'] = 'Registration successful! Please login.';
        $this->redirect('/login');
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}