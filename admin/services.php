<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3('../database.sqlite');

// 添加服务
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];
    
    $stmt = $db->prepare("INSERT INTO services (title, description, icon) VALUES (:title, :description, :icon)");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':icon', $icon, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'Service added successfully!';
    } else {
        $error = 'Failed to add service.';
    }
}

// 删除服务
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->exec("DELETE FROM services WHERE id = $id");
    header('Location: services.php');
    exit();
}

// 编辑服务
$editService = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editService = $db->querySingle("SELECT * FROM services WHERE id = $id", true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];
    
    $stmt = $db->prepare("UPDATE services SET title = :title, description = :description, icon = :icon WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':icon', $icon, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'Service updated successfully!';
        $editService = null;
    } else {
        $error = 'Failed to update service.';
    }
}

$services = $db->query("SELECT * FROM services ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Admin Panel</title>
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
        .form-container, .list-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .btn { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; margin-right: 10px; }
        .btn:hover { background: #5a6fd6; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        .icon-cell { font-size: 24px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } table { display: block; overflow-x: auto; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="homepage.php">Homepage Settings</a></li>
            <li><a href="services.php" class="active">Services Management</a></li>
            <li><a href="portfolio.php">Portfolio Management</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Settings</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
        <div class="logout"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Services Management</h1>
        </div>

        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <h3 style="color: #333; margin-bottom: 20px;"><?php echo $editService ? 'Edit Service' : 'Add New Service'; ?></h3>
            
            <form method="POST">
                <?php if ($editService): ?>
                    <input type="hidden" name="id" value="<?php echo $editService['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Icon (Emoji)</label>
                    <input type="text" name="icon" value="<?php echo $editService ? htmlspecialchars($editService['icon']) : ''; ?>" placeholder="e.g., 💻" required>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo $editService ? htmlspecialchars($editService['title']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required><?php echo $editService ? htmlspecialchars($editService['description']) : ''; ?></textarea>
                </div>
                
                <?php if ($editService): ?>
                    <button type="submit" name="update_service" class="btn">Update Service</button>
                    <a href="services.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_service" class="btn">Add Service</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="list-container">
            <h3 style="color: #333; margin-bottom: 20px;">Services List</h3>
            <table>
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($service = $services->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td class="icon-cell"><?php echo $service['icon']; ?></td>
                            <td><?php echo htmlspecialchars($service['title']); ?></td>
                            <td><?php echo substr(htmlspecialchars($service['description']), 0, 50) . '...'; ?></td>
                            <td>
                                <a href="services.php?edit=<?php echo $service['id']; ?>" class="btn" style="padding: 8px 15px; font-size: 14px;">Edit</a>
                                <a href="services.php?delete=<?php echo $service['id']; ?>" class="btn btn-danger" style="padding: 8px 15px; font-size: 14px;" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
