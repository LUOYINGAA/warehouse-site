<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3('../database.sqlite');

// 删除留言
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->exec("DELETE FROM messages WHERE id = $id");
    header('Location: messages.php');
    exit();
}

// 标记为已读
if (isset($_GET['mark_read'])) {
    $id = $_GET['mark_read'];
    $db->exec("UPDATE messages SET status = 'read' WHERE id = $id");
    header('Location: messages.php');
    exit();
}

// 批量标记已读
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    $db->exec("UPDATE messages SET status = 'read'");
    header('Location: messages.php');
    exit();
}

$messages = $db->query("SELECT * FROM messages ORDER BY created_at DESC");
$unread_count = $db->querySingle("SELECT COUNT(*) FROM messages WHERE status = 'unread'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Panel</title>
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
        .message-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .message-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .message-title { font-weight: 600; color: #333; }
        .message-meta { display: flex; gap: 15px; color: #666; font-size: 14px; }
        .message-content { color: #555; line-height: 1.6; }
        .message-actions { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
        .btn { background: #667eea; color: white; padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-right: 10px; }
        .btn:hover { background: #5a6fd6; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
        .unread-badge { background: #e74c3c; color: white; padding: 3px 8px; border-radius: 10px; font-size: 12px; }
        .status-badge { padding: 3px 8px; border-radius: 10px; font-size: 12px; }
        .status-unread { background: #ffebee; color: #c62828; }
        .status-read { background: #e8f5e9; color: #2e7d32; }
        .no-messages { text-align: center; padding: 50px; color: #999; }
        .bulk-actions { margin-bottom: 20px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } .message-meta { flex-direction: column; gap: 5px; } }
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
            <li><a href="contact.php">Contact Settings</a></li>
            <li><a href="messages.php" class="active">Messages</a></li>
        </ul>
        <div class="logout"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Messages <span class="unread-badge"><?php echo $unread_count; ?> Unread</span></h1>
        </div>

        <div class="bulk-actions">
            <form method="POST">
                <button type="submit" name="mark_all_read" class="btn btn-secondary">Mark All as Read</button>
            </form>
        </div>

        <?php 
        $has_messages = false;
        while ($message = $messages->fetchArray(SQLITE3_ASSOC)): 
            $has_messages = true;
        ?>
            <div class="message-container">
                <div class="message-header">
                    <div class="message-title">
                        <?php echo htmlspecialchars($message['subject']); ?>
                        <span class="status-badge status-<?php echo $message['status']; ?>">
                            <?php echo ucfirst($message['status']); ?>
                        </span>
                    </div>
                    <div class="message-meta">
                        <span>👤 <?php echo htmlspecialchars($message['name']); ?></span>
                        <span>📧 <?php echo htmlspecialchars($message['email']); ?></span>
                        <span>📅 <?php echo $message['created_at']; ?></span>
                    </div>
                </div>
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
                <div class="message-actions">
                    <?php if ($message['status'] === 'unread'): ?>
                        <a href="messages.php?mark_read=<?php echo $message['id']; ?>" class="btn">Mark as Read</a>
                    <?php endif; ?>
                    <a href="messages.php?delete=<?php echo $message['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if (!$has_messages): ?>
            <div class="message-container no-messages">
                <div style="font-size: 48px; margin-bottom: 15px;">📭</div>
                <p>No messages yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
