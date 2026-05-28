<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3('../database.sqlite');
$contact = $db->querySingle("SELECT * FROM contact WHERE id = 1", true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $map_embed = $_POST['map_embed'];

    $stmt = $db->prepare("UPDATE contact SET 
        address = :address, phone = :phone, email = :email,
        website = :website, map_embed = :map_embed,
        updated_at = CURRENT_TIMESTAMP WHERE id = 1");
    
    $stmt->bindValue(':address', $address, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':website', $website, SQLITE3_TEXT);
    $stmt->bindValue(':map_embed', $map_embed, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'Contact settings updated successfully!';
        $contact = $db->querySingle("SELECT * FROM contact WHERE id = 1", true);
    } else {
        $error = 'Failed to update contact settings.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Settings - Admin Panel</title>
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
        .btn { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .btn:hover { background: #5a6fd6; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .hint { font-size: 12px; color: #999; margin-top: 5px; }
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
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php" class="active">Contact Settings</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
        <div class="logout"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Contact Settings</h1>
        </div>

        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($contact['address']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($contact['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" name="website" value="<?php echo htmlspecialchars($contact['website']); ?>">
                </div>
                <div class="form-group">
                    <label>Google Map Embed Code</label>
                    <textarea name="map_embed" placeholder="Paste your Google Maps embed code here"><?php echo htmlspecialchars($contact['map_embed']); ?></textarea>
                    <div class="hint">Get embed code from Google Maps: Share > Embed a map > Copy HTML</div>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
