<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3('../database.sqlite');
$about = $db->querySingle("SELECT * FROM about WHERE id = 1", true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $mission = $_POST['mission'];
    $vision = $_POST['vision'];
    $values = $_POST['values'];
    $team_title = $_POST['team_title'];

    $stmt = $db->prepare("UPDATE about SET 
        title = :title, content = :content, mission = :mission,
        vision = :vision, values = :values, team_title = :team_title,
        updated_at = CURRENT_TIMESTAMP WHERE id = 1");
    
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':content', $content, SQLITE3_TEXT);
    $stmt->bindValue(':mission', $mission, SQLITE3_TEXT);
    $stmt->bindValue(':vision', $vision, SQLITE3_TEXT);
    $stmt->bindValue(':values', $values, SQLITE3_TEXT);
    $stmt->bindValue(':team_title', $team_title, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'About Us updated successfully!';
        $about = $db->querySingle("SELECT * FROM about WHERE id = 1", true);
    } else {
        $error = 'Failed to update About Us.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
        .sidebar { background: #2c3e50; color: white; width: 250px; height: 100vh; position: fixed; left: 0; top: 0; padding: 20px; }
        .sidebar h2 { font-size: 20px; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin-bottom: 10px; }
        .sidebar ul li a { display: block; padding: 12px 15px; color: white; text-decoration: none; border-radius: 6px; transition: background 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #34495e; }
        .sidebar .logout { position: absolute; bottom: 20px; width: calc(100% - 40px); }
        .sidebar .logout a { background: #e74c3c; }
        .main-content { margin-left: 250px; padding: 30px; }
        .header { margin-bottom: 30px; }
        .header h1 { color: #333; font-size: 28px; }
        .form-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .form-group textarea { resize: vertical; min-height: 150px; }
        .btn { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .btn:hover { background: #5a6fd6; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="homepage.php">Homepage Settings</a></li>
            <li><a href="services.php">Services Management</a></li>
            <li><a href="portfolio.php">Portfolio Management</a></li>
            <li><a href="about.php" class="active">About Us</a></li>
            <li><a href="contact.php">Contact Settings</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
        <div class="logout"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>About Us Settings</h1>
        </div>

        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Main Content</h3>
                
                <div class="form-group">
                    <label>Page Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>About Content</label>
                    <textarea name="content" required><?php echo htmlspecialchars($about['content']); ?></textarea>
                </div>

                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Mission & Vision</h3>
                
                <div class="form-group">
                    <label>Mission Statement</label>
                    <textarea name="mission" required><?php echo htmlspecialchars($about['mission']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Vision Statement</label>
                    <textarea name="vision" required><?php echo htmlspecialchars($about['vision']); ?></textarea>
                </div>

                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Company Values</h3>
                
                <div class="form-group">
                    <label>Core Values (comma separated)</label>
                    <input type="text" name="values" value="<?php echo htmlspecialchars($about['values']); ?>" required placeholder="Quality, Innovation, Integrity">
                </div>
                <div class="form-group">
                    <label>Team Section Title</label>
                    <input type="text" name="team_title" value="<?php echo htmlspecialchars($about['team_title']); ?>" required>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
