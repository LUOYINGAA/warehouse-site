<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Enterprise Website</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Get in touch with us for your project needs</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <?php
        $db = new SQLite3('database.sqlite');
        $contact = $db->querySingle("SELECT * FROM contact WHERE id = 1", true);

        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $subject = $_POST['subject'];
            $message = $_POST['message'];

            $stmt = $db->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)");
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
            $stmt->bindValue(':subject', $subject, SQLITE3_TEXT);
            $stmt->bindValue(':message', $message, SQLITE3_TEXT);

            if ($stmt->execute()) {
                $success = 'Your message has been sent successfully! We will contact you soon.';
            }
        }
        ?>
        
        <div class="contact-content">
            <div class="contact-info">
                <h3 style="font-size: 24px; margin-bottom: 30px; color: #333;">Get In Touch</h3>
                
                <div class="contact-item">
                    <i>📍</i>
                    <div>
                        <h4>Address</h4>
                        <p><?php echo htmlspecialchars($contact['address']); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i>📞</i>
                    <div>
                        <h4>Phone</h4>
                        <p><?php echo htmlspecialchars($contact['phone']); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i>📧</i>
                    <div>
                        <h4>Email</h4>
                        <p><?php echo htmlspecialchars($contact['email']); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i>🌐</i>
                    <div>
                        <h4>Website</h4>
                        <p><?php echo htmlspecialchars($contact['website']); ?></p>
                    </div>
                </div>

                <?php if ($contact['map_embed']): ?>
                    <div style="margin-top: 30px;">
                        <h4>Find Us</h4>
                        <?php echo $contact['map_embed']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="contact-form">
                <h3 style="font-size: 24px; margin-bottom: 20px; color: #333;">Send Message</h3>
                
                <?php if ($success): ?>
                    <div class="success-message">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
