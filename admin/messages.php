<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if (isset($_GET['mark_read'])) {
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'Read' WHERE id = :id");
    $stmt->execute([':id' => $_GET['mark_read']]);
    $message = 'Message marked as read!';
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $message = 'Message deleted successfully!';
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="admin-content">
    <div class="content-header">
        <h2>Contact Messages</h2>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr class="<?php echo $msg['status'] === 'Unread' ? 'unread-message' : ''; ?>">
                        <td><?php echo $msg['id']; ?></td>
                        <td><?php echo $msg['name']; ?></td>
                        <td><?php echo $msg['email']; ?></td>
                        <td><?php echo $msg['subject'] ?: 'No subject'; ?></td>
                        <td><?php echo substr($msg['message'], 0, 50) . '...'; ?></td>
                        <td><span class="status-badge status-<?php echo strtolower($msg['status']); ?>"><?php echo $msg['status']; ?></span></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></td>
                        <td>
                            <?php if ($msg['status'] === 'Unread'): ?>
                                <a href="messages.php?mark_read=<?php echo $msg['id']; ?>" class="btn-small">Mark Read</a>
                            <?php endif; ?>
                            <a href="messages.php?delete=<?php echo $msg['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
