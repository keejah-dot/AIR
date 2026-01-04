<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }
        
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
        }
        
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .product-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .product-price {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin: 20px 0;
        }
        
        .product-description {
            line-height: 1.6;
            color: #666;
            margin-bottom: 30px;
        }
        
        .order-btn {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            width: 100%;
        }
        
        .order-btn:hover {
            background: #1a252f;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .modal-buttons button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .submit-btn {
            background: #27ae60;
            color: white;
        }
        
        .submit-btn:hover {
            background: #219653;
        }
        
        .cancel-btn {
            background: #95a5a6;
            color: white;
        }
        
        .cancel-btn:hover {
            background: #7f8c8d;
        }
        
        .success-message {
            display: none;
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-grid">
            <div>
                <img id="productImage" class="product-image" alt="Product Image">
            </div>
            
            <div class="product-info">
                <h1 id="productName">Loading...</h1>
                <div class="product-price" id="productPrice">ZMW 0.00</div>
                <p class="product-description" id="productDescription"></p>
                
                <div class="error-message" id="errorMessage"></div>
                
                <button class="order-btn" onclick="openOrderModal()">Order Now</button>
            </div>
        </div>
    </div>
    
    <!-- Order Modal -->
    <div class="modal" id="orderModal">
        <div class="modal-content">
            <h2>Place Your Order</h2>
            
            <div class="error-message" id="orderError"></div>
            
            <form id="orderForm">
                <div class="form-group">
                    <label for="customerName">Full Name *</label>
                    <input type="text" id="customerName" name="customerName" required>
                </div>
                
                <div class="form-group">
                    <label for="customerEmail">Email Address *</label>
                    <input type="email" id="customerEmail" name="customerEmail" required>
                </div>
                
                <div class="form-group">
                    <label for="customerPhone">Phone Number *</label>
                    <input type="tel" id="customerPhone" name="customerPhone" required>
                </div>
                
                <div class="form-group">
                    <label for="customerAddress">Delivery Address *</label>
                    <textarea id="customerAddress" name="customerAddress" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="orderNotes">Additional Notes (Optional)</label>
                    <textarea id="orderNotes" name="orderNotes" placeholder="Any special instructions..."></textarea>
                </div>
                
                <div class="modal-buttons">
                    <button type="submit" class="submit-btn">Place Order</button>
                    <button type="button" class="cancel-btn" onclick="closeOrderModal()">Cancel</button>
                </div>
            </form>
            
            <div class="success-message" id="successMessage">
                <h3>✓ Order Successful!</h3>
                <p id="orderSuccessMessage"></p>
            </div>
            
            <div class="loading" id="orderLoading">
                Processing your order...
            </div>
        </div>
    </div>

    <script>
        // Get product ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        
        // Elements
        const productImage = document.getElementById('productImage');
        const productName = document.getElementById('productName');
        const productPrice = document.getElementById('productPrice');
        const productDescription = document.getElementById('productDescription');
        const errorMessage = document.getElementById('errorMessage');
        const orderModal = document.getElementById('orderModal');
        const orderForm = document.getElementById('orderForm');
        const successMessage = document.getElementById('successMessage');
        const orderSuccessMessage = document.getElementById('orderSuccessMessage');
        const orderError = document.getElementById('orderError');
        const orderLoading = document.getElementById('orderLoading');
        
        // Product data
        let currentProduct = null;
        
        // Load product details
        async function loadProduct() {
            if (!productId) {
                showError('Product ID is missing from the URL');
                return;
            }
            
            try {
                const response = await fetch(`api/products/single.php?id=${productId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const product = await response.json();
                
                // Check if product has error
                if (product.error) {
                    throw new Error(product.error);
                }
                
                currentProduct = product;
                
                // Update page
                productImage.src = product.image ? `Admin/${product.image}` : 'https://via.placeholder.com/400x400?text=No+Image';
                productImage.alt = product.name;
                productName.textContent = product.name;
                productPrice.textContent = `ZMW ${parseFloat(product.price).toFixed(2)}`;
                productDescription.textContent = product.description || 'No description available.';
                
            } catch (error) {
                console.error('Error loading product:', error);
                showError(`Failed to load product: ${error.message}`);
            }
        }
        
        // Show error message
        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }
        
        // Open order modal
        function openOrderModal() {
            if (!currentProduct) {
                alert('Please wait for product details to load.');
                return;
            }
            
            // Reset form
            orderForm.reset();
            successMessage.style.display = 'none';
            orderError.style.display = 'none';
            orderLoading.style.display = 'none';
            
            // Show modal
            orderModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        // Close order modal
        function closeOrderModal() {
            orderModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Handle form submission
        orderForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate form
            const name = document.getElementById('customerName').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const address = document.getElementById('customerAddress').value.trim();
            
            if (!name || !email || !phone || !address) {
                showOrderError('Please fill in all required fields.');
                return;
            }
            
            if (!validateEmail(email)) {
                showOrderError('Please enter a valid email address.');
                return;
            }
            
            // Prepare order data
            const orderData = {
                customer: {
                    fullName: name,
                    email: email,
                    phone: phone,
                    address: address,
                    notes: document.getElementById('orderNotes').value.trim()
                },
                product: {
                    id: currentProduct.id,
                    name: currentProduct.name,
                    price: parseFloat(currentProduct.price),
                    quantity: 1
                }
            };
            
            // Show loading
            orderForm.style.display = 'none';
            orderLoading.style.display = 'block';
            orderError.style.display = 'none';
            
            try {
                // Send order
                const response = await fetch('api/orders/create.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                // Hide loading
                orderLoading.style.display = 'none';
                
                if (result.success) {
                    // Show success message
                    orderSuccessMessage.innerHTML = `
                        Your order has been placed successfully!<br>
                        <strong>Order ID:</strong> ${result.order_id}<br><br>
                        We will contact you shortly for confirmation.
                    `;
                    successMessage.style.display = 'block';
                    
                    // Reset form after 5 seconds and close modal
                    setTimeout(() => {
                        closeOrderModal();
                        orderForm.style.display = 'block';
                        orderForm.reset();
                    }, 5000);
                    
                } else {
                    // Show error
                    showOrderError(result.message || result.error || 'Failed to place order. Please try again.');
                    orderForm.style.display = 'block';
                }
                
            } catch (error) {
                console.error('Order error:', error);
                
                // Hide loading, show form
                orderLoading.style.display = 'none';
                orderForm.style.display = 'block';
                
                showOrderError('Network error. Please check your connection and try again.');
            }
        });
        
        // Show order error
        function showOrderError(message) {
            orderError.textContent = message;
            orderError.style.display = 'block';
        }
        
        // Validate email
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Close modal when clicking outside
        orderModal.addEventListener('click', function(e) {
            if (e.target === orderModal) {
                closeOrderModal();
            }
        });
        
        // Load product on page load
        document.addEventListener('DOMContentLoaded', loadProduct);
    </script>
</body>
</html>