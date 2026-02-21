<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

$orderId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :orderId");
$itemsStmt->execute([':orderId' => $orderId]);
$orderItems = $itemsStmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<div class="admin-content">
    <div class="content-header">
        <h2>Order Details - #<?php echo $order['id']; ?></h2>
        <a href="orders.php" class="btn-secondary">Back to Orders</a>
    </div>
    
    <div class="order-details-grid">
        <div class="order-info-card">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
            <p><strong>Address:</strong> <?php echo $order['customer_address']; ?></p>
        </div>
        
        <div class="order-info-card">
            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></p>
            <p><strong>Order Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($order['created_at'])); ?></p>
            <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
        </div>
    </div>
    
    <div class="content-header">
        <h3>Order Items</h3>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($orderItems as $item): 
                    $itemTotal = $item['price'] * $item['quantity'];
                    $subtotal += $itemTotal;
                ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($itemTotal, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td><strong>Rs. <?php echo number_format($subtotal, 2); ?></strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Delivery Fee:</strong></td>
                    <td><strong>Rs. 500.00</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>Rs. <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
