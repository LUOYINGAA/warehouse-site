<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Enterprise Website</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Our Services</h1>
            <p>Comprehensive solutions for your business needs</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section">
        <?php
        $db = new SQLite3('database.sqlite');
        $services = $db->query("SELECT * FROM services");
        ?>
        
        <div class="services-grid">
            <?php while ($service = $services->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="service-card">
                    <div class="service-icon"><?php echo $service['icon']; ?></div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <a href="contact.php" class="hero-btn" style="display: inline-block; margin-top: 20px; background: #667eea; color: white; padding: 12px 30px; font-size: 14px;">Get Started</a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" style="background: #f8f9fa;">
        <h2 class="section-title">Why Choose Us?</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">⭐</div>
                <h3>Quality Service</h3>
                <p>We deliver top-notch solutions tailored to your needs</p>
            </div>
            <div class="service-card">
                <div class="service-icon">⏱️</div>
                <h3>Timely Delivery</h3>
                <p>We meet deadlines without compromising quality</p>
            </div>
            <div class="service-card">
                <div class="service-icon">💰</div>
                <h3>Affordable Pricing</h3>
                <p>Competitive rates with no hidden fees</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🎯</div>
                <h3>Expert Team</h3>
                <p>Highly skilled professionals dedicated to your success</p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
