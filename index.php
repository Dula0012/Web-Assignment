<?php
require_once 'config/database.php';
session_start();
?>
<?php include 'includes/header.php'; ?>

<section class="hero">
    <div class="hero-decorations">
        <div class="sparkle sparkle-1">✨</div>
        <div class="sparkle sparkle-2">💄</div>
        <div class="sparkle sparkle-3">💅</div>
        <div class="sparkle sparkle-4">✨</div>
        <div class="sparkle sparkle-5">💋</div>
    </div>
    <div class="hero-content">
        <h1 class="animated-title">Welcome to Girlly Beauty</h1>
        <p class="animated-subtitle">Discover Sri Lanka's finest collection of beauty and makeup products</p>
        <a href="products.php" class="btn-primary pulse-animation">Shop Now</a>
    </div>
</section>

<section class="promo-banner">
    <div class="container">
        <div class="promo-content">
            <span class="promo-icon">🎉</span>
            <p>Special Offer: Free Delivery on Orders Over Rs. 5,000!</p>
            <span class="promo-icon">🎁</span>
        </div>
    </div>
</section>

<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <p class="section-subtitle">Handpicked selections just for you</p>
        <div class="products-grid">
            <?php
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
            $index = 0;
            while ($product = $stmt->fetch()) {
                echo "<div class='product-card slide-up' style='animation-delay: " . ($index * 0.05) . "s'>";
                echo "<div class='product-badge'>New</div>";
                echo "<div class='product-image'>";
                echo "<img src='assets/images/products/{$product['image']}' alt='{$product['name']}'>";
                echo "<div class='product-overlay'>";
                echo "<a href='product-details.php?id={$product['id']}' class='quick-view'>Quick View</a>";
                echo "</div>";
                echo "</div>";
                echo "<div class='product-info'>";
                echo "<h3>{$product['name']}</h3>";
                echo "<p class='brand'>{$product['brand']}</p>";
                echo "<div class='rating'>⭐⭐⭐⭐⭐</div>";
                echo "<p class='price'>Rs. " . number_format($product['price'], 2) . "</p>";
                echo "<button class='btn-cart' onclick='addToCart({$product['id']})'>Add to Cart</button>";
                echo "</div>";
                echo "</div>";
                $index++;
            }
            ?>
        </div>
    </div>
</section>

<section class="why-choose">
    <div class="container">
        <h2 class="section-title">Why Choose Girlly Beauty?</h2>
        <div class="features-grid">
            <div class="feature zoom-in">
                <div class="feature-icon">✓</div>
                <h3>Authentic Products</h3>
                <p>100% genuine beauty and makeup items from trusted brands</p>
            </div>
            <div class="feature zoom-in" style="animation-delay: 0.1s">
                <div class="feature-icon">🚚</div>
                <h3>Island-wide Delivery</h3>
                <p>Fast and reliable delivery across Sri Lanka</p>
            </div>
            <div class="feature zoom-in" style="animation-delay: 0.2s">
                <div class="feature-icon">🔒</div>
                <h3>Secure Shopping</h3>
                <p>Safe and secure online shopping experience</p>
            </div>
            <div class="feature zoom-in" style="animation-delay: 0.3s">
                <div class="feature-icon">💬</div>
                <h3>Expert Support</h3>
                <p>Professional customer service and beauty advice</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <h2 class="section-title">What Our Customers Say</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"Amazing products! Fast delivery and excellent quality. Highly recommend!"</p>
                <p class="customer-name">- Amaya S.</p>
            </div>
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"Best beauty store in Sri Lanka. Authentic products at great prices!"</p>
                <p class="customer-name">- Nethmi R.</p>
            </div>
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"Love the variety! Customer service is outstanding. Will shop again!"</p>
                <p class="customer-name">- Dilani P.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
