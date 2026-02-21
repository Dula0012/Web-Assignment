<?php
require_once 'config/database.php';
session_start();

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $errorMessage = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);
            $successMessage = 'Thank you for contacting us! We will get back to you soon.';
        } catch (PDOException $e) {
            $errorMessage = 'An error occurred. Please try again later.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<section class="contact-page">
    <div class="container">
        <h1>Contact Us</h1>
        
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have questions about our products or need assistance? We're here to help!</p>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <h3>Location</h3>
                        <p>Colombo, Sri Lanka</p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Phone</h3>
                        <p>+94 77 123 4567</p>
                        <p>+94 11 234 5678</p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Email</h3>
                        <p>info@girllybeauty.lk</p>
                        <p>support@girllybeauty.lk</p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                        <p>Saturday: 9:00 AM - 4:00 PM</p>
                        <p>Sunday: Closed</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container">
                <h2>Send Us a Message</h2>
                
                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-error"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="contact.php" class="contact-form">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
