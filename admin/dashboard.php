<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Enterprise Website</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }

        .sidebar {
            background: #2c3e50;
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #34495e;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
        }

        .sidebar .logout a {
            background: #e74c3c;
        }

        .sidebar .logout a:hover {
            background: #c0392b;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .user-info span {
            color: #666;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stat-card .icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }

        .stat-card .label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .action-btn {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: #333;
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .action-btn .icon {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .action-btn .text {
            font-weight: 600;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar .logout {
                position: relative;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="homepage.php">Homepage Settings</a></li>
            <li><a href="services.php">Services Management</a></li>
            <li><a href="portfolio.php">Portfolio Management</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Settings</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
        <div class="logout">
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Welcome, Admin</h1>
            <div class="user-info">
                <span>👤 <?php echo $_SESSION['admin_username']; ?></span>
            </div>
        </div>

        <div class="stats-cards">
            <?php
            $db = new SQLite3('../database.sqlite');
            $services_count = $db->querySingle("SELECT COUNT(*) FROM services");
            $portfolio_count = $db->querySingle("SELECT COUNT(*) FROM portfolio");
            $messages_count = $db->querySingle("SELECT COUNT(*) FROM messages WHERE status = 'unread'");
            ?>
            
            <div class="stat-card">
                <div class="icon">💼</div>
                <div class="value"><?php echo $services_count; ?></div>
                <div class="label">Services</div>
            </div>
            <div class="stat-card">
                <div class="icon">📁</div>
                <div class="value"><?php echo $portfolio_count; ?></div>
                <div class="label">Portfolio Items</div>
            </div>
            <div class="stat-card">
                <div class="icon">📩</div>
                <div class="value"><?php echo $messages_count; ?></div>
                <div class="label">Unread Messages</div>
            </div>
        </div>

        <h2 style="color: #333; margin: 30px 0 15px;">Quick Actions</h2>
        <div class="quick-actions">
            <a href="homepage.php" class="action-btn">
                <div class="icon">🏠</div>
                <div class="text">Edit Homepage</div>
            </a>
            <a href="services.php" class="action-btn">
                <div class="icon">➕</div>
                <div class="text">Add Service</div>
            </a>
            <a href="portfolio.php" class="action-btn">
                <div class="icon">📷</div>
                <div class="text">Add Portfolio</div>
            </a>
            <a href="messages.php" class="action-btn">
                <div class="icon">📬</div>
                <div class="text">View Messages</div>
            </a>
        </div>
    </div>
</body>
</html>
