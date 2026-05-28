<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3('../database.sqlite');
$homepage = $db->querySingle("SELECT * FROM homepage WHERE id = 1", true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $hero_button_text = $_POST['hero_button_text'];
    $about_title = $_POST['about_title'];
    $about_description = $_POST['about_description'];
    $services_title = $_POST['services_title'];
    $services_subtitle = $_POST['services_subtitle'];
    $stats_title = $_POST['stats_title'];
    $stat1_label = $_POST['stat1_label'];
    $stat1_value = $_POST['stat1_value'];
    $stat2_label = $_POST['stat2_label'];
    $stat2_value = $_POST['stat2_value'];
    $stat3_label = $_POST['stat3_label'];
    $stat3_value = $_POST['stat3_value'];
    $stat4_label = $_POST['stat4_label'];
    $stat4_value = $_POST['stat4_value'];

    $hero_image = $homepage['hero_image'];
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
        $targetDir = '../uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['hero_image']['name']);
        move_uploaded_file($_FILES['hero_image']['tmp_name'], $targetDir . $fileName);
        $hero_image = $fileName;
    }

    $about_image = $homepage['about_image'];
    if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] == 0) {
        $targetDir = '../uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['about_image']['name']);
        move_uploaded_file($_FILES['about_image']['tmp_name'], $targetDir . $fileName);
        $about_image = $fileName;
    }

    $stmt = $db->prepare("UPDATE homepage SET 
        hero_title = :hero_title, hero_subtitle = :hero_subtitle, 
        hero_button_text = :hero_button_text, hero_image = :hero_image,
        about_title = :about_title, about_description = :about_description,
        about_image = :about_image, services_title = :services_title,
        services_subtitle = :services_subtitle, stats_title = :stats_title,
        stat1_label = :stat1_label, stat1_value = :stat1_value,
        stat2_label = :stat2_label, stat2_value = :stat2_value,
        stat3_label = :stat3_label, stat3_value = :stat3_value,
        stat4_label = :stat4_label, stat4_value = :stat4_value,
        updated_at = CURRENT_TIMESTAMP WHERE id = 1");
    
    $stmt->bindValue(':hero_title', $hero_title, SQLITE3_TEXT);
    $stmt->bindValue(':hero_subtitle', $hero_subtitle, SQLITE3_TEXT);
    $stmt->bindValue(':hero_button_text', $hero_button_text, SQLITE3_TEXT);
    $stmt->bindValue(':hero_image', $hero_image, SQLITE3_TEXT);
    $stmt->bindValue(':about_title', $about_title, SQLITE3_TEXT);
    $stmt->bindValue(':about_description', $about_description, SQLITE3_TEXT);
    $stmt->bindValue(':about_image', $about_image, SQLITE3_TEXT);
    $stmt->bindValue(':services_title', $services_title, SQLITE3_TEXT);
    $stmt->bindValue(':services_subtitle', $services_subtitle, SQLITE3_TEXT);
    $stmt->bindValue(':stats_title', $stats_title, SQLITE3_TEXT);
    $stmt->bindValue(':stat1_label', $stat1_label, SQLITE3_TEXT);
    $stmt->bindValue(':stat1_value', $stat1_value, SQLITE3_TEXT);
    $stmt->bindValue(':stat2_label', $stat2_label, SQLITE3_TEXT);
    $stmt->bindValue(':stat2_value', $stat2_value, SQLITE3_TEXT);
    $stmt->bindValue(':stat3_label', $stat3_label, SQLITE3_TEXT);
    $stmt->bindValue(':stat3_value', $stat3_value, SQLITE3_TEXT);
    $stmt->bindValue(':stat4_label', $stat4_label, SQLITE3_TEXT);
    $stmt->bindValue(':stat4_value', $stat4_value, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'Homepage updated successfully!';
        $homepage = $db->querySingle("SELECT * FROM homepage WHERE id = 1", true);
    } else {
        $error = 'Failed to update homepage.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Settings - Admin Panel</title>
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
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .btn:hover { background: #5a6fd6; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .image-preview { max-width: 200px; margin-top: 10px; border-radius: 8px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="homepage.php" class="active">Homepage Settings</a></li>
            <li><a href="services.php">Services Management</a></li>
            <li><a href="portfolio.php">Portfolio Management</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Settings</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
        <div class="logout"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Homepage Settings</h1>
        </div>

        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Hero Section</h3>
                
                <div class="form-group">
                    <label>Hero Title</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($homepage['hero_title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Hero Subtitle</label>
                    <textarea name="hero_subtitle" required><?php echo htmlspecialchars($homepage['hero_subtitle']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Hero Button Text</label>
                    <input type="text" name="hero_button_text" value="<?php echo htmlspecialchars($homepage['hero_button_text']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Hero Image</label>
                    <input type="file" name="hero_image" accept="image/*">
                    <?php if ($homepage['hero_image']): ?>
                        <img src="../uploads/<?php echo $homepage['hero_image']; ?>" class="image-preview" alt="Current Hero Image">
                    <?php endif; ?>
                </div>

                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">About Section</h3>
                
                <div class="form-group">
                    <label>About Title</label>
                    <input type="text" name="about_title" value="<?php echo htmlspecialchars($homepage['about_title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>About Description</label>
                    <textarea name="about_description" required><?php echo htmlspecialchars($homepage['about_description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>About Image</label>
                    <input type="file" name="about_image" accept="image/*">
                    <?php if ($homepage['about_image']): ?>
                        <img src="../uploads/<?php echo $homepage['about_image']; ?>" class="image-preview" alt="Current About Image">
                    <?php endif; ?>
                </div>

                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Services Section</h3>
                
                <div class="form-group">
                    <label>Services Title</label>
                    <input type="text" name="services_title" value="<?php echo htmlspecialchars($homepage['services_title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Services Subtitle</label>
                    <input type="text" name="services_subtitle" value="<?php echo htmlspecialchars($homepage['services_subtitle']); ?>" required>
                </div>

                <h3 style="color: #333; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Stats Section</h3>
                
                <div class="form-group">
                    <label>Stats Title</label>
                    <input type="text" name="stats_title" value="<?php echo htmlspecialchars($homepage['stats_title']); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stat 1 Label</label>
                        <input type="text" name="stat1_label" value="<?php echo htmlspecialchars($homepage['stat1_label']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stat 1 Value</label>
                        <input type="text" name="stat1_value" value="<?php echo htmlspecialchars($homepage['stat1_value']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stat 2 Label</label>
                        <input type="text" name="stat2_label" value="<?php echo htmlspecialchars($homepage['stat2_label']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stat 2 Value</label>
                        <input type="text" name="stat2_value" value="<?php echo htmlspecialchars($homepage['stat2_value']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stat 3 Label</label>
                        <input type="text" name="stat3_label" value="<?php echo htmlspecialchars($homepage['stat3_label']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stat 3 Value</label>
                        <input type="text" name="stat3_value" value="<?php echo htmlspecialchars($homepage['stat3_value']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stat 4 Label</label>
                        <input type="text" name="stat4_label" value="<?php echo htmlspecialchars($homepage['stat4_label']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stat 4 Value</label>
                        <input type="text" name="stat4_value" value="<?php echo htmlspecialchars($homepage['stat4_value']); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
