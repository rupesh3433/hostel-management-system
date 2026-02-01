@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h1>Login</h1>

    <form method="POST"
          action="<?php echo htmlspecialchars($_SESSION['base_url'] . '/login', ENT_QUOTES, 'UTF-8'); ?>">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <div class="text-center">
        Don't have an account?
        <a href="<?php echo htmlspecialchars($_SESSION['base_url'] . '/register', ENT_QUOTES, 'UTF-8'); ?>">
            Register here
        </a>
    </div>
</div>
@endsection
