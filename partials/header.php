<header class="app-header">
    <div class="header-container">
        <h1 class="logo">⚓ MarineLog</h1>
        <nav class="nav-menu">
            <span class="user-badge">🤿 <?= htmlspecialchars($_SESSION['username']); ?> (<?= strtoupper($_SESSION['level']); ?>)</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>