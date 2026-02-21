let cart = JSON.parse(localStorage.getItem('cart')) || [];

cart = cart.map(item => ({
    ...item,
    id: parseInt(item.id),
    price: parseFloat(item.price),
    quantity: parseInt(item.quantity)
}));

function updateCartCount() {
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }
}

function addToCart(productId, quantity = 1) {
    quantity = parseInt(quantity);
    
    fetch(`api/get-product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            if (product.error) {
                alert('Product not found');
                return;
            }
            
            const existingItem = cart.find(item => parseInt(item.id) === parseInt(productId));
            
            if (existingItem) {
                if (existingItem.quantity + quantity <= product.stock) {
                    existingItem.quantity += quantity;
                    alert('Product quantity updated in cart!');
                } else {
                    alert('Not enough stock available');
                    return;
                }
            } else {
                if (quantity <= product.stock) {
                    cart.push({
                        id: parseInt(product.id),
                        name: product.name,
                        price: parseFloat(product.price),
                        image: product.image,
                        brand: product.brand,
                        quantity: quantity
                    });
                    alert('Product added to cart!');
                } else {
                    alert('Not enough stock available');
                    return;
                }
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add product to cart');
        });
}

function removeFromCart(productId) {
    productId = parseInt(productId);
    cart = cart.filter(item => parseInt(item.id) !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    displayCart();
}

function updateQuantity(productId, newQuantity) {
    productId = parseInt(productId);
    newQuantity = parseInt(newQuantity);
    
    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    const item = cart.find(item => parseInt(item.id) === productId);
    if (item) {
        item.quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCart();
    }
}

function displayCart() {
    const cartItemsContainer = document.getElementById('cart-items');
    
    if (!cartItemsContainer) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p style="text-align: center; padding: 3rem; color: #888;">Your cart is empty</p>';
        updateCartSummary(0);
        return;
    }
    
    let html = '';
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        html += `
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="assets/images/products/${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-info">
                    <h3>${item.name}</h3>
                    <p class="brand">${item.brand}</p>
                    <p class="price">Rs. ${item.price.toFixed(2)}</p>
                </div>
                <div class="cart-item-actions">
                    <div class="quantity-control">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                        <input type="number" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)" min="1">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                    </div>
                    <p class="price">Rs. ${itemTotal.toFixed(2)}</p>
                    <button class="remove-btn" onclick="removeFromCart(${item.id})">Remove</button>
                </div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = html;
    
    const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    updateCartSummary(subtotal);
}

function updateCartSummary(subtotal) {
    const deliveryFee = 500;
    const total = subtotal + deliveryFee;
    
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    
    if (subtotalElement) {
        subtotalElement.textContent = `Rs. ${subtotal.toFixed(2)}`;
    }
    
    if (totalElement) {
        totalElement.textContent = `Rs. ${total.toFixed(2)}`;
    }
}

function proceedToCheckout() {
    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }
    
    const modal = document.getElementById('checkoutModal');
    if (modal) {
        modal.style.display = 'block';
        
        const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        const deliveryFee = 500;
        const total = subtotal + deliveryFee;
        
        const checkoutTotalElement = document.getElementById('checkoutTotal');
        if (checkoutTotalElement) {
            checkoutTotalElement.textContent = `Rs. ${total.toFixed(2)}`;
        }
    }
}

function closeCheckoutModal() {
    const modal = document.getElementById('checkoutModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function submitOrder(event) {
    event.preventDefault();
    
    const customerName = document.getElementById('customerName').value;
    const customerEmail = document.getElementById('customerEmail').value;
    const customerPhone = document.getElementById('customerPhone').value;
    const customerAddress = document.getElementById('customerAddress').value;
    
    const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    const deliveryFee = 500;
    const totalAmount = subtotal + deliveryFee;
    
    const orderData = {
        customer: {
            name: customerName,
            email: customerEmail,
            phone: customerPhone,
            address: customerAddress
        },
        items: cart,
        totalAmount: totalAmount
    };
    
    fetch('api/place-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order placed successfully! Order ID: ' + data.orderId);
            cart = [];
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            closeCheckoutModal();
            window.location.href = 'index.php';
        } else {
            alert('Failed to place order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while placing your order');
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('checkoutModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    if (window.location.pathname.includes('cart.php')) {
        displayCart();
    }
});
