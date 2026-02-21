<?php
require_once 'config/database.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$productId = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$relatedStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :catId AND id != :id LIMIT 4");
$relatedStmt->execute([':catId' => $product['category_id'], ':id' => $productId]);
$relatedProducts = $relatedStmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<section class="product-details">
    <div class="container">
        <div class="product-detail-grid">
            <div class="product-image-large">
                <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            
            <div class="product-detail-info">
                <h1><?php echo $product['name']; ?></h1>
                <p class="brand">Brand: <?php echo $product['brand']; ?></p>
                <p class="category">Category: <?php echo $product['category_name']; ?></p>
                <p class="price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                <p class="stock">Availability: <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock'; ?></p>
                
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo $product['description']; ?></p>
                </div>
                
                <div class="product-actions">
                    <div class="quantity-selector">
                        <label>Quantity:</label>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button class="btn-cart" onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value)">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
        
        <?php if (count($relatedProducts) > 0): ?>
        <div class="related-products">
            <h2>Related Products</h2>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/products/<?php echo $related['image']; ?>" alt="<?php echo $related['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $related['name']; ?></h3>
                            <p class="brand"><?php echo $related['brand']; ?></p>
                            <p class="price">Rs. <?php echo number_format($related['price'], 2); ?></p>
                            <a href="product-details.php?id=<?php echo $related['id']; ?>" class="btn-details">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
