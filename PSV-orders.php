<?php
session_start();
include 'connect.php'; // Ensure this file contains the database connection details

if (!isset($_SESSION['email'])) {
    header("Location: PSV-login.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user details from session
    $user_email = $_SESSION['email'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session during login

    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $city = $_POST['city'];
    $payment_method = $_POST['payment'];

    // Retrieve cart items from the hidden input field
    $cart = json_decode($_POST['cart'], true);

    // Calculate total amount
    $total_amount = 0;
    foreach ($cart as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Insert into `orders` table
    $order_query = "INSERT INTO orders (id, first_name, last_name, email, phone, address, pincode, city, payment_method, total_amount) 
                    VALUES ('id', '$firstName', '$lastName', '$email', '$phone', '$address', '$pincode', '$city', '$payment_method', '$total_amount')";

    if (mysqli_query($conn, $order_query)) {
        $order_id = mysqli_insert_id($conn); // Get the last inserted order ID

        // Insert into `orders1` table
        foreach ($cart as $item) {
            $product_name = $item['name'];
            $quantity = $item['quantity'];
            $total_price = $item['price'] * $quantity;

            $order1_query = "INSERT INTO orders1 (user_email, product_name, quantity, total_price) 
                             VALUES ('$user_email', '$product_name', '$quantity', '$total_price')";
            mysqli_query($conn, $order1_query);
        }

        // Insert into `order_items` table
        foreach ($cart as $item) {
            $product_name = $item['name'];
            $product_image = $item['image'];
            $price = $item['price'];
            $quantity = $item['quantity'];

            $order_items_query = "INSERT INTO order_items (order_id, product_name, product_image, price, quantity) 
                                  VALUES ('$order_id', '$product_name', '$product_image', '$price', '$quantity')";
            mysqli_query($conn, $order_items_query);
        }

        // Clear the cart after successful order placement
        echo "<script>localStorage.removeItem('cart');</script>";

        // Redirect to the feedback page
        echo "<script>alert('Order placed successfully!'); window.location.href='psv-feedback.php';</script>";
        exit(); // Ensure no further code is executed after redirection
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSV - Order Checkout</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Cinzel:wght@400;700&display=swap">
    <style>
        /* Add your CSS styles here */
        body {
            font-family: 'Playfair Display', serif;
            color: #6b5a45;
            margin: 0;
            padding: 0;
            transition: background-color 1s ease;
        }

        .header {
            display: flex;
            position: sticky;
            height: 40px;
            top: 0;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            padding: 5px 20px;
            background-color: black !important;
            border-radius: 2px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .navbar {
            margin-left: auto;
        }

        .navbar ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            gap: 20px;
        }

        .navbar a {
            text-decoration: none;
            color: #fdfdfc;
            font-weight: bold;
        }

        .orders-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .order-summary {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-item, .order-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .cart-item-image, .order-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .cart-item-details, .order-item-details {
            flex: 1;
        }

        .total-amount, .item-total {
            text-align: right;
            padding: 15px;
            border-top: 2px solid #8b7355;
            margin-top: 15px;
            font-weight: bold;
            color: #8b7355;
        }

        .order-total {
            text-align: right;
            padding: 15px;
            border-top: 2px solid #8b7355;
            margin-top: 15px;
        }

        .form-section {
            margin-bottom: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #8b7355;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .payment-options {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-option svg {
            width: 40px;
            height: 40px;
        }

        .place-order-btn {
            background: #8b7355;
            color: #000000;
            padding: 15px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
        }

        .place-order-btn:hover {
            background: #8a5616;
        }

        .orders-container h2 {
            text-align: center;
            color: #8b7355;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .upi-section {
            margin: 20px 0;
        }

        .upi-section label {
            display: block;
            margin-bottom: 5px;
            color: #8b7355;
        }

        .upi-section input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .card-details {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .card-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .card-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .card-input:focus {
            outline: none;
            border-color: #8b7355;
        }

        #cardDetails {
            display: none;
        }

        .animated-popup {
            border: 2px solid #8b7355 !important;
            border-radius: 15px !important;
        }

        .custom-title {
            color: #8b7355 !important;
            font-family: 'Cinzel', serif !important;
            font-size: 1.5em !important;
        }

        .logo img {
            width: 120px;
            height: 50px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(107, 90, 69, 0.2);
        }

        @media (max-width: 768px) {
            .orders-container {
                padding: 10px;
            }

            .form-row {
                flex-direction: column;
                gap: 10px;
            }

            .payment-options {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .header-content {
                flex-direction: column;
                height: auto;
                padding: 10px;
                width: 100%;
            }

            .navbar ul {
                flex-direction: column;
                gap: 10px;
            }

            .card-input-group {
                flex-direction: column;
            }

            .logo img {
                width: 100px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <a href="PSV-home.php">
                    <img src="psvlogo1.png" alt="PSV QuickBuy Logo">
                </a>
            </div>
            <nav class="navbar">
                <ul>
                    <li><a href="PSV-home.php"><b>Home</b></a></li>
                    <li><a href="PSV-exploremore.html"><b>Collections</b></a></li>
                    <li><a href="PSV-cart.html"><b>Cart</b></a></li>
                    <li><a href="PSV-orders.php"><b>Orders</b></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="orders-container">
        <h2>Complete Your Order</h2>
        <div class="order-summary">
            <h3>Order Summary</h3>
            <div id="selectedItems"></div>
            <div class="total-amount">
                <span>Total Amount:</span>
                <span id="orderTotal">₹0.00</span>
            </div>
        </div>

        <form id="orderForm" method="POST" action="">
            <div class="form-section">
                <h3>Personal Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Shipping Address</h3>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="pincode">PIN Code</label>
                        <input type="text" id="pincode" name="pincode" pattern="[0-9]{6}" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Payment Method</h3>
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" id="phonepe" name="payment" value="phonepe" required>
                        <label for="phonepe">PhonePe</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="googlepay" name="payment" value="googlepay">
                        <label for="googlepay">Google Pay</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="creditcard" name="payment" value="creditcard">
                        <label for="creditcard">Credit Card</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="debitcard" name="payment" value="debitcard">
                        <label for="debitcard">Debit Card</label>
                    </div>
                </div>
            </div>

            <!-- Hidden input for cart data -->
            <input type="hidden" name="cart" id="cartData">

            <button type="submit" class="place-order-btn"><b>Place Order</b></button>
        </form>
    </section>

    <script>
        // Display cart items
        function displayCartItems() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const selectedItems = document.getElementById('selectedItems');
            const orderTotal = document.getElementById('orderTotal');
            let totalAmount = 0;

            const itemsHTML = cart.map(item => {
                const itemTotal = item.price * item.quantity;
                totalAmount += itemTotal;
                return `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <h3>${item.name}</h3>
                            <p>Price: ₹${item.price}</p>
                            <p>Quantity: ${item.quantity}</p>
                            <p>Subtotal: ₹${itemTotal}</p>
                        </div>
                    </div>
                `;
            }).join('');

            selectedItems.innerHTML = itemsHTML;
            orderTotal.textContent = `₹${totalAmount}`;
        }

        // Set cart data in hidden input before form submission
        document.getElementById('orderForm').addEventListener('submit', function () {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            document.getElementById('cartData').value = JSON.stringify(cart);
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', displayCartItems);
    </script>
</body>
</html>