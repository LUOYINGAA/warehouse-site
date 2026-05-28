<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Enterprise Website</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <?php
    $db = new SQLite3('database.sqlite');
    $about = $db->querySingle("SELECT * FROM about WHERE id = 1", true);
    ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?php echo htmlspecialchars($about['title']); ?></h1>
            <p>Learn more about our company and values</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="section">
        <div class="about-content">
            <div>
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600" alt="About Us" class="about-image">
            </div>
            <div class="about-text">
                <h3>Our Story</h3>
                <p><?php echo htmlspecialchars($about['content']); ?></p>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="section" style="background: #f8f9fa;">
        <div class="about-content">
            <div class="about-text">
                <h3>🎯 Our Mission</h3>
                <p><?php echo htmlspecialchars($about['mission']); ?></p>
            </div>
            <div class="about-text">
                <h3>🌟 Our Vision</h3>
                <p><?php echo htmlspecialchars($about['vision']); ?></p>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="section">
        <h2 class="section-title">Core Values</h2>
        <div class="services-grid">
            <?php
            $values = explode(',', $about['values']);
            foreach ($values as $value) {
                $value = trim($value);
                echo '<div class="service-card"><div class="service-icon">💎</div><h3>' . htmlspecialchars($value) . '</h3></div>';
            }
            ?>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section" style="background: #f8f9fa;">
        <h2 class="section-title"><?php echo htmlspecialchars($about['team_title']); ?></h2>
        <div class="services-grid">
            <div class="service-card">
                <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">👨‍💼</div>
                <h3>John Doe</h3>
                <p>CEO & Founder</p>
            </div>
            <div class="service-card">
                <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">👩‍💼</div>
                <h3>Jane Smith</h3>
                <p>COO</p>
            </div>
            <div class="service-card">
                <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">👨‍💻</div>
                <h3>Mike Johnson</h3>
                <p>CTO</p>
            </div>
            <div class="service-card">
                <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">👩‍💻</div>
                <h3>Sarah Williams</h3>
                <p>Lead Designer</p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
