<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>About Us</h3>
            <p>We are a professional team dedicated to delivering high-quality solutions for your business needs.</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="portfolio.php">Portfolio</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <?php
            $db = new SQLite3('database.sqlite');
            $contact = $db->querySingle("SELECT * FROM contact WHERE id = 1", true);
            ?>
            <h3>Contact Info</h3>
            <p>📍 <?php echo htmlspecialchars($contact['address']); ?></p>
            <p>📞 <?php echo htmlspecialchars($contact['phone']); ?></p>
            <p>📧 <?php echo htmlspecialchars($contact['email']); ?></p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 Enterprise. All rights reserved.</p>
    </div>
</footer>
