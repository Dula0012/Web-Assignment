<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$productsCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoriesCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$ordersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$messagesCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'Unread'")->fetchColumn();

$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
$lowStock = $pdo->query("SELECT * FROM products WHERE stock < 20 ORDER BY stock ASC LIMIT 5")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Products</h3>
        <p class="stat-number"><?php echo $productsCount; ?></p>
    </div>
    <div class="stat-card">
        <h3>Categories</h3>
        <p class="stat-number"><?php echo $categoriesCount; ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Orders</h3>
        <p class="stat-number"><?php echo $ordersCount; ?></p>
    </div>
    <div class="stat-card">
        <h3>Unread Messages</h3>
        <p class="stat-number"><?php echo $messagesCount; ?></p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <h2>Recent Orders</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recentOrders) > 0): ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['customer_name']; ?></td>
                            <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center;">No orders yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="dashboard-section">
        <h2>Low Stock Products</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($lowStock) > 0): ?>
                    <?php foreach ($lowStock as $product): ?>
                        <tr>
                            <td><?php echo $product['name']; ?></td>
                            <td><span class="stock-warning"><?php echo $product['stock']; ?></span></td>
                            <td><a href="products.php?edit=<?php echo $product['id']; ?>" class="btn-small">Update</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center;">All products have sufficient stock</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
