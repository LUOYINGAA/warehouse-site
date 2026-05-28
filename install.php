<?php
// 数据库初始化文件
$db = new SQLite3('database.sqlite');

// 创建首页内容表
$db->exec("CREATE TABLE IF NOT EXISTS homepage (
    id INTEGER PRIMARY KEY,
    hero_title TEXT,
    hero_subtitle TEXT,
    hero_button_text TEXT,
    hero_image TEXT,
    about_title TEXT,
    about_description TEXT,
    about_image TEXT,
    services_title TEXT,
    services_subtitle TEXT,
    stats_title TEXT,
    stat1_label TEXT,
    stat1_value TEXT,
    stat2_label TEXT,
    stat2_value TEXT,
    stat3_label TEXT,
    stat3_value TEXT,
    stat4_label TEXT,
    stat4_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 创建服务项目表
$db->exec("CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    description TEXT,
    icon TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 创建案例作品表
$db->exec("CREATE TABLE IF NOT EXISTS portfolio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    description TEXT,
    category TEXT,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 创建关于我们表
$db->exec("CREATE TABLE IF NOT EXISTS about (
    id INTEGER PRIMARY KEY,
    title TEXT,
    content TEXT,
    mission TEXT,
    vision TEXT,
    values TEXT,
    team_title TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 创建联系信息表
$db->exec("CREATE TABLE IF NOT EXISTS contact (
    id INTEGER PRIMARY KEY,
    address TEXT,
    phone TEXT,
    email TEXT,
    website TEXT,
    map_embed TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 创建留言表
$db->exec("CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT,
    phone TEXT,
    subject TEXT,
    message TEXT,
    status TEXT DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 创建管理员表
$db->exec("CREATE TABLE IF NOT EXISTS admin (
    id INTEGER PRIMARY KEY,
    username TEXT,
    password TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 检查是否已有管理员
$admin_check = $db->querySingle("SELECT COUNT(*) FROM admin");
if ($admin_check == 0) {
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO admin (username, password) VALUES ('admin', '$hashed_password')");
}

// 检查是否已有首页内容
$homepage_check = $db->querySingle("SELECT COUNT(*) FROM homepage");
if ($homepage_check == 0) {
    $db->exec("INSERT INTO homepage (
        hero_title, hero_subtitle, hero_button_text, hero_image,
        about_title, about_description, about_image,
        services_title, services_subtitle,
        stats_title, stat1_label, stat1_value, stat2_label, stat2_value,
        stat3_label, stat3_value, stat4_label, stat4_value
    ) VALUES (
        'Welcome to Our Company',
        'We provide exceptional services to help your business grow',
        'Get Started',
        'hero.jpg',
        'About Us',
        'We are a professional team dedicated to delivering high-quality solutions for your business needs.',
        'about.jpg',
        'Our Services',
        'Explore our comprehensive range of services',
        'Our Achievements',
        'Projects Completed', '500+',
        'Clients Served', '200+',
        'Years Experience', '10+',
        'Awards Won', '50+'
    )");
}

// 检查是否已有联系信息
$contact_check = $db->querySingle("SELECT COUNT(*) FROM contact");
if ($contact_check == 0) {
    $db->exec("INSERT INTO contact (address, phone, email, website) VALUES (
        '123 Business Avenue, New York, NY 10001',
        '+1 (555) 123-4567',
        'contact@company.com',
        'www.company.com'
    )");
}

// 检查是否已有关于我们内容
$about_check = $db->querySingle("SELECT COUNT(*) FROM about");
if ($about_check == 0) {
    $db->exec("INSERT INTO about (title, content, mission, vision, values, team_title) VALUES (
        'About Our Company',
        'Founded in 2014, we have been providing innovative solutions to businesses worldwide. Our team of experts is committed to delivering excellence in every project we undertake.',
        'To empower businesses with cutting-edge solutions that drive growth and success.',
        'To be the leading provider of innovative business solutions globally.',
        'Quality, Innovation, Integrity, Customer Focus',
        'Meet Our Team'
    )");
}

// 检查是否已有服务项目
$services_check = $db->querySingle("SELECT COUNT(*) FROM services");
if ($services_check == 0) {
    $db->exec("INSERT INTO services (title, description, icon) VALUES 
        ('Web Development', 'Professional website development services with modern design and functionality.', '💻'),
        ('Mobile App Development', 'Cross-platform mobile applications for iOS and Android.', '📱'),
        ('Digital Marketing', 'Comprehensive digital marketing strategies to boost your online presence.', '📈'),
        ('Graphic Design', 'Creative graphic design services for branding and marketing materials.', '🎨')
    ");
}

// 检查是否已有案例作品
$portfolio_check = $db->querySingle("SELECT COUNT(*) FROM portfolio");
if ($portfolio_check == 0) {
    $db->exec("INSERT INTO portfolio (title, description, category, image) VALUES 
        ('Corporate Website', 'A modern corporate website design for a leading tech company.', 'Web Design', 'portfolio1.jpg'),
        ('Mobile App UI', 'User interface design for a popular e-commerce mobile app.', 'Mobile', 'portfolio2.jpg'),
        ('Brand Identity', 'Complete brand identity package for a startup company.', 'Branding', 'portfolio3.jpg'),
        ('E-commerce Platform', 'Full-featured e-commerce website with payment integration.', 'Web Development', 'portfolio4.jpg')
    ");
}

echo "Database initialized successfully!\n";
echo "Default admin credentials: admin / admin123\n";
$db->close();
?>
