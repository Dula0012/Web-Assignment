<?php
require_once 'config/database.php';
session_start();

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$brandFilter = isset($_GET['brand']) ? $_GET['brand'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : 999999;

$sql = "SELECT * FROM products WHERE price >= :minPrice AND price <= :maxPrice";
$params = [':minPrice' => $minPrice, ':maxPrice' => $maxPrice];

if ($categoryFilter) {
    $sql .= " AND category_id = :category";
    $params[':category'] = $categoryFilter;
}

if ($brandFilter) {
    $sql .= " AND brand = :brand";
    $params[':brand'] = $brandFilter;
}

if ($searchQuery) {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
    $params[':search'] = "%$searchQuery%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$brands = $pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<section class="products-page">
    <div class="container">
        <h1>Our Products</h1>
        
        <div class="products-container">
            <aside class="filters-sidebar">
                <h3>Filters</h3>
                
                <form method="GET" action="products.php" id="filterForm">
                    <div class="filter-group">
                        <h4>Search</h4>
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <h4>Category</h4>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Brand</h4>
                        <select name="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand']; ?>" <?php echo $brandFilter == $brand['brand'] ? 'selected' : ''; ?>>
                                    <?php echo $brand['brand']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <input type="number" name="min_price" placeholder="Min" value="<?php echo $minPrice; ?>">
                        <input type="number" name="max_price" placeholder="Max" value="<?php echo $maxPrice; ?>">
                    </div>
                    
                    <button type="submit" class="btn-primary">Apply Filters</button>
                    <a href="products.php" class="btn-secondary">Clear Filters</a>
                </form>
            </aside>
            
            <div class="products-main">
                <p class="results-count"><?php echo count($products); ?> Products Found</p>
                
                <div class="products-grid">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                </div>
                                <div class="product-info">
                                    <h3><?php echo $product['name']; ?></h3>
                                    <p class="brand"><?php echo $product['brand']; ?></p>
                                    <p class="price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                                    <p class="stock">Stock: <?php echo $product['stock']; ?></p>
                                    <button class="btn-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-details">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-products">No products found matching your criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
