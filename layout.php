<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MarineLog'; ?></title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>

    <?php 
    if (isset($_SESSION['user_id'])) {
        include 'partials/header.php'; 
    }
    ?>

    <main class="main-content">
        <?= $content; ?>
    </main>

    <?php 
    if (isset($_SESSION['user_id'])) {
        include 'partials/footer.php'; 
    }
    ?>

</body>
</html>