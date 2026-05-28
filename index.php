<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Website - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <?php
    $db = new SQLite3('database.sqlite');
    $homepage = $db->querySingle("SELECT * FROM homepage WHERE id = 1", true);
    $services = $db->query("SELECT * FROM services");
    $portfolio = $db->query("SELECT * FROM portfolio ORDER BY id DESC LIMIT 6");
    ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?php echo htmlspecialchars($homepage['hero_title']); ?></h1>
            <p><?php echo htmlspecialchars($homepage['hero_subtitle']); ?></p>
            <a href="#services" class="hero-btn"><?php echo htmlspecialchars($homepage['hero_button_text']); ?></a>
        </div>
    </section>

    <!-- About Section -->
    <section class="section" id="about">
        <h2 class="section-title"><?php echo htmlspecialchars($homepage['about_title']); ?></h2>
        <p class="section-subtitle">Learn more about our company</p>
        
        <div class="about-content">
            <div class="about-text">
                <p><?php echo htmlspecialchars($homepage['about_description']); ?></p>
                <a href="about.php" class="hero-btn" style="background: #667eea; color: white;">Read More</a>
            </div>
            <div>
                <?php if ($homepage['about_image']): ?>
                    <img src="uploads/<?php echo $homepage['about_image']; ?>" alt="About Us" class="about-image">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400" alt="About Us" class="about-image">
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section" id="services">
        <h2 class="section-title"><?php echo htmlspecialchars($homepage['services_title']); ?></h2>
        <p class="section-subtitle"><?php echo htmlspecialchars($homepage['services_subtitle']); ?></p>
        
        <div class="services-grid">
            <?php while ($service = $services->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="service-card">
                    <div class="service-icon"><?php echo $service['icon']; ?></div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section stats">
        <h2 class="section-title" style="color: white;"><?php echo htmlspecialchars($homepage['stats_title']); ?></h2>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo htmlspecialchars($homepage['stat1_value']); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($homepage['stat1_label']); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo htmlspecialchars($homepage['stat2_value']); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($homepage['stat2_label']); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo htmlspecialchars($homepage['stat3_value']); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($homepage['stat3_label']); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo htmlspecialchars($homepage['stat4_value']); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($homepage['stat4_label']); ?></div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="section" id="portfolio">
        <h2 class="section-title">Our Work</h2>
        <p class="section-subtitle">Check out our latest projects</p>
        
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
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="portfolio.php" class="hero-btn" style="background: #667eea; color: white;">View All Projects</a>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="hero" style="padding: 80px 20px;">
        <div class="container">
            <h2 style="font-size: 36px;">Ready to Start Your Project?</h2>
            <p style="font-size: 18px;">Contact us today and let's create something amazing together</p>
            <a href="contact.php" class="hero-btn">Get in Touch</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
