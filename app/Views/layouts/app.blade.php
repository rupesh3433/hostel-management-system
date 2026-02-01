<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System</title>

    <!-- Global CSS -->
    <link rel="stylesheet" href="<?= $_SESSION['base_url'] ?>/css/app.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar">
    <a href="<?= $_SESSION['base_url'] ?>/dashboard" class="navbar-brand">
        🏠 Hostel Management
    </a>

    <div class="navbar-menu">
        <a href="<?= $_SESSION['base_url'] ?>/dashboard">Dashboard</a>
        <a href="<?= $_SESSION['base_url'] ?>/rooms">Rooms</a>

        <form method="POST" action="<?= $_SESSION['base_url'] ?>/logout" style="display:inline;">
            <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <button type="submit">Logout</button>
        </form>
    </div>
</nav>
<?php endif; ?>

<div class="container">

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

    @yield('content')
</div>

<!-- Global JS -->
<script src="<?= $_SESSION['base_url'] ?>/js/main.js"></script>

</body>
</html>
