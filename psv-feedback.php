<?php
session_start();
include 'connect.php'; // Ensure this file contains the database connection details

if (!isset($_SESSION['email'])) {
    header("Location: PSV-login.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    $feedback_date = date('Y-m-d H:i:s'); // Current date and time

    // Insert data into the feedback table
    $query = "INSERT INTO feedback (name, email, rating, comments, feedback_date) 
              VALUES ('$name', '$email', '$rating', '$comments', '$feedback_date')";

    if (mysqli_query($conn, $query)) {
        // Feedback submitted successfully
        echo "<script>
                document.getElementById('feedbackForm').style.display = 'none';
                document.getElementById('successMessage').style.display = 'block';
                setTimeout(function() {
                    window.location.href = 'PSV-home.php';
                }, 3000); // Redirect after 3 seconds
              </script>";
    } else {
        // Error handling
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSV - Feedback</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Your CSS styles for the feedback form */
        body {
            font-family: "Playfair Display", serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-image: url('https://anntamandiri-sejahtera.co.id/storage/2023/09/Pelatihan-Customer-Satisfaction-Survey.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start; /* Align items to the left */
            padding-left: 1in; /* 1-inch gap from the left */
        }

        .header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 40px;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background-color: black !important;
            border-radius: 2px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo h1 {
            font-size: 24px;
            color: #c27517;
            cursor: pointer;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .navbar ul li a {
            font-size: 16px;
            color: rgb(249, 238, 238);
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #f4f1ec;
        }

        .feedback-section {
            background-color: rgba(3, 2, 2, 0.9);
            padding: 20px; /* Reduced padding */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 500px; /* Reduced max-width */
            width: 90%;
            text-align: center;
        }

        .feedback-section h2 {
            font-size: 42px; /* Further increased font size */
            margin-bottom: 15px; /* Reduced margin */
            color: white; /* White color for the heading */
            font-family: "Cinzel", serif; /* Retain the style */
            font-weight: 700; /* Bold font weight */
        }

        .feedback-section form {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Reduced gap */
        }

        .feedback-section label {
            color: white; /* White color for labels */
            font-weight: bold; /* Bold font weight */
            font-size: 16px; /* Increased font size for labels */
        }

        .feedback-section input, .feedback-section textarea, .feedback-section select {
            padding: 8px; /* Reduced padding */
            font-size: 14px; /* Reduced font size */
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .feedback-section button {
            padding: 8px 16px; /* Reduced padding */
            font-size: 14px; /* Reduced font size */
            background-color: #27961b;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .feedback-section button:hover {
            background-color: #6b5a45;
        }

        .success-message {
            display: none;
            margin-top: 15px; /* Reduced margin */
            color: #28a745;
            font-weight: bold;
            font-size: 14px; /* Reduced font size */
        }

        .success-message i {
            color: #28a745;
            margin-right: 5px;
        }

        .footer {
            text-align: center;
            padding: -12px;
            background-color: rgba(175, 145, 145, 0.8);
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer p {
            font-size: 15px;
            color: #333;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 20px;
            }

            .navbar ul {
                flex-direction: column;
                gap: 10px;
            }

            .feedback-section {
                padding: 15px; /* Reduced padding */
            }
        }

        @media (max-width: 480px) {
            .feedback-section h2 {
                font-size: 32px; /* Adjusted font size for smaller screens */
            }

            .feedback-section label {
                font-size: 14px; /* Adjusted font size for smaller screens */
            }

            .feedback-section input, .feedback-section textarea, .feedback-section select {
                font-size: 12px; /* Adjusted font size */
            }
        }

        .logo img {
            width: 150px; /* Adjusted width for rectangular shape */
            height: 75px; /* Adjusted height for rectangular shape */
            border-radius: 8px; /* Optional: Slightly rounded corners */
            box-shadow: 0 4px 6px rgba(107, 90, 69, 0.2);
        }
    </style>
    <script>
        function validateEmail() {
            var emailInput = document.getElementById("email");
            var emailValue = emailInput.value;
            var emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
            
            if (!emailPattern.test(emailValue)) {
                alert("Please enter a valid email address ending with @gmail.com");
                return false;
            }
            return true;
        }

        function submitFeedback(event) {
            event.preventDefault(); // Prevent actual form submission
            if (validateEmail()) {
                document.getElementById("feedbackForm").submit();
            }
        }
    </script>
</head>
<body>
    <header class="header">
        <!-- Logo -->
        <div class="logo">
            <a href="PSV-home.php">
                <img src="psvlogo1.png" alt="PSV QuickBuy Logo">
            </a>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="PSV-login.php"><b>Login</b></a></li>
                <li><a href="PSV-home.php"><b>Home</b></a></li>
                <li><a href="PSV-exploremore.html"><b>Collections</b></a></li>
                <li><a href="#feedback"><b>Feedback</b></a></li>
            </ul>
        </nav>
    </header>

    <!-- Feedback Section -->
    <section id="feedback" class="feedback-section">
        <h2>Feedback</h2>
        <form id="feedbackForm" method="POST" action="" onsubmit="submitFeedback(event)">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="rating">How do you feel about our products and website?</label>
            <select id="rating" name="rating" required>
                <option value="5">Excellent😍</option>
                <option value="4">Very Good😊</option>
                <option value="3">Good😐</option>
                <option value="2">Fair😕</option>
                <option value="1">Poor😠</option>
            </select>
            
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" rows="4" required></textarea>
            
            <button type="submit" class="btn">Submit Feedback</button>
        </form>
        
        <div id="successMessage" class="success-message">
            ✅ Your feedback has been successfully submitted!
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 PSV. All rights reserved.</p>
    </footer>
</body>
</html>