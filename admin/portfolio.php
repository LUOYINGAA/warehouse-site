<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3('../database.sqlite');

// 添加案例
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_portfolio'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = '../uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);
        $image = $fileName;
    }
    
    $stmt = $db->prepare("INSERT INTO portfolio (title, description, category, image) VALUES (:title, :description, :category, :image)");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $stmt->bindValue(':image', $image, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'Portfolio item added successfully!';
    } else {
        $error = 'Failed to add portfolio item.';
    }
}

// 删除案例
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $item = $db->querySingle("SELECT image FROM portfolio WHERE id = $id", true);
    if ($item['image']) {
        unlink('../uploads/' . $item['image']);
    }
    $db->exec("DELETE FROM portfolio WHERE id = $id");
    header('Location: portfolio.php');
    exit();
}

// 编辑案例
$editItem = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editItem = $db->querySingle("SELECT * FROM portfolio WHERE id = $id", true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_portfolio'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    
    $image = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if ($image) {
            unlink('../uploads/' . $image);
        }
        $targetDir = '../uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);
        $image = $fileName;
    }
    
    $stmt = $db->prepare("UPDATE portfolio SET title = :title, description = :description, category = :category, image = :image WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $stmt->bindValue(':image', $image, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $success = 'Portfolio item updated successfully!';
        $editItem = null;
    } else {
        $error = 'Failed to update portfolio item.';
    }
}

$portfolio = $db->query("SELECT * FROM portfolio ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Management - Admin Panel</title>
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
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
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
        .image-preview { max-width: 100px; border-radius: 8px; }
        .category-badge { background: #667eea; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } table { display: block; overflow-x: auto; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="homepage.php">Homepage Settings</a></li>
            <li><a href="services.php">Services Management</a></li>
            <li><a href="portfolio.php" class="active">Portfolio Management</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Settings</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
        <div class="logout"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Portfolio Management</h1>
        </div>

        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <h3 style="color: #333; margin-bottom: 20px;"><?php echo $editItem ? 'Edit Portfolio Item' : 'Add New Portfolio Item'; ?></h3>
            
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editItem): ?>
                    <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo $editItem['image']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="<?php echo $editItem ? htmlspecialchars($editItem['category']) : ''; ?>" placeholder="e.g., Web Design">
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($editItem && $editItem['image']): ?>
                        <img src="../uploads/<?php echo $editItem['image']; ?>" class="image-preview" alt="Current Image">
                    <?php endif; ?>
                </div>
                
                <?php if ($editItem): ?>
                    <button type="submit" name="update_portfolio" class="btn">Update Item</button>
                    <a href="portfolio.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_portfolio" class="btn">Add Item</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="list-container">
            <h3 style="color: #333; margin-bottom: 20px;">Portfolio List</h3>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $portfolio->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><img src="../uploads/<?php echo $item['image']; ?>" class="image-preview" alt="<?php echo htmlspecialchars($item['title']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span></td>
                            <td>
                                <a href="portfolio.php?edit=<?php echo $item['id']; ?>" class="btn" style="padding: 8px 15px; font-size: 14px;">Edit</a>
                                <a href="portfolio.php?delete=<?php echo $item['id']; ?>" class="btn btn-danger" style="padding: 8px 15px; font-size: 14px;" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
