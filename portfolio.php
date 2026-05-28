<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Enterprise Website</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Our Portfolio</h1>
            <p>Explore our latest projects and achievements</p>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="section">
        <?php
        $db = new SQLite3('database.sqlite');
        $portfolio = $db->query("SELECT * FROM portfolio ORDER BY id DESC");
        ?>
        
        <div class="portfolio-grid">
            <?php while ($item = $portfolio->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="portfolio-item">
                    <?php if ($item['image']): ?>
                        <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="portfolio-image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x250" alt="<?php echo htmlspecialchars($item['title']); ?>" class="portfolio-image">
                    <?php endif; ?>
                    <div class="portfolio-info">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <span class="category"><?php echo htmlspecialchars($item['category']); ?></span>
                        <p style="color: #666; margin-top: 10px; font-size: 14px;"><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
