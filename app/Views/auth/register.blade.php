@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h1>Register</h1>

    <form method="POST"
          action="<?php echo htmlspecialchars($_SESSION['base_url'] . '/register', ENT_QUOTES, 'UTF-8'); ?>">
        @csrf

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>

    <div class="text-center">
        Already have an account?
        <a href="<?php echo htmlspecialchars($_SESSION['base_url'] . '/login', ENT_QUOTES, 'UTF-8'); ?>">
            Login here
        </a>
    </div>
</div>
@endsection
