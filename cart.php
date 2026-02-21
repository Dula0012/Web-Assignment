<?php
session_start();
?>
<?php include 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <h1>Shopping Cart</h1>
        
        <div class="cart-container">
            <div id="cart-items" class="cart-items">
            </div>
            
            <div class="cart-summary">
                <h2>Order Summary</h2>
                <div class="summary-item">
                    <span>Subtotal:</span>
                    <span id="subtotal">Rs. 0.00</span>
                </div>
                <div class="summary-item">
                    <span>Delivery:</span>
                    <span id="delivery">Rs. 500.00</span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span id="total">Rs. 500.00</span>
                </div>
                <button class="btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>
</section>

<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCheckoutModal()">&times;</span>
        <h2>Checkout</h2>
        
        <form id="checkoutForm" onsubmit="submitOrder(event)">
            <div class="form-group">
                <label for="customerName">Full Name *</label>
                <input type="text" id="customerName" name="customerName" required>
            </div>
            
            <div class="form-group">
                <label for="customerEmail">Email *</label>
                <input type="email" id="customerEmail" name="customerEmail" required>
            </div>
            
            <div class="form-group">
                <label for="customerPhone">Phone Number *</label>
                <input type="tel" id="customerPhone" name="customerPhone" required>
            </div>
            
            <div class="form-group">
                <label for="customerAddress">Delivery Address *</label>
                <textarea id="customerAddress" name="customerAddress" rows="4" required></textarea>
            </div>
            
            <div class="order-summary-checkout">
                <h3>Order Summary</h3>
                <p>Total Amount: <span id="checkoutTotal">Rs. 0.00</span></p>
            </div>
            
            <button type="submit" class="btn-primary">Place Order</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
