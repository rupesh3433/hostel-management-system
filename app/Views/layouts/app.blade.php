<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System</title>

    <!-- ===============================
         GLOBAL CSS (MUST USE /public)
         =============================== -->
    <link rel="stylesheet"
          href="<?php echo htmlspecialchars($_SESSION['base_url'] . '/public/css/app.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar">
        <a href="<?php echo htmlspecialchars($_SESSION['base_url'] . '/dashboard', ENT_QUOTES, 'UTF-8'); ?>"
           class="navbar-brand">
            🏠 Hostel Management
        </a>

        <div class="navbar-menu">
            <a href="<?php echo htmlspecialchars($_SESSION['base_url'] . '/dashboard', ENT_QUOTES, 'UTF-8'); ?>">
                Dashboard
            </a>

            <a href="<?php echo htmlspecialchars($_SESSION['base_url'] . '/rooms', ENT_QUOTES, 'UTF-8'); ?>">
                Rooms
            </a>

            <form method="POST"
                  action="<?php echo htmlspecialchars($_SESSION['base_url'] . '/logout', ENT_QUOTES, 'UTF-8'); ?>"
                  style="display:inline;">
                <input type="hidden" name="_token"
                       value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">Logout</button>
            </form>
        </div>
    </nav>
<?php endif; ?>

<div class="container">

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- ===============================
         PAGE CONTENT
         =============================== -->
    @yield('content')

</div>

<!-- ===============================
     GLOBAL JS (MUST USE /public)
     =============================== -->
<script src="<?php echo htmlspecialchars($_SESSION['base_url'] . '/public/js/main.js', ENT_QUOTES, 'UTF-8'); ?>"></script>

</body>
</html>
