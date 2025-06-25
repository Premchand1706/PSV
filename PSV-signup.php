<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email_query = "SELECT * FROM users1 WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already registered
        echo "<script>alert('Email already in use.');</script>";
    } else {
        // Insert new user into database
        $insert_query = "INSERT INTO users1 (firstName, lastName, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $fName, $lName, $email, $hashed_password);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: PSV-login.php");
            exit;
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PSV QuickBuy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
      /* Reset and Base Styles */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      
      body {
        font-family: "Playfair Display", serif;
        color: #333;
        background-color: #af9191;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow-x: hidden;
        position: relative;
      }

      /* Background Animation */
      .animated-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
        background: url('https://wallpapers.com/images/hd/e-commerce-1900-x-1118-wallpaper-r2k1ldol65vss423.jpg') no-repeat center center/cover;
      }

      .animated-background .circle {
        position: absolute;
        border-radius: 50%;
        opacity: 0.25;
        filter: blur(80px);
        animation: float 20s infinite ease-in-out;
      }

      .animated-background .circle:nth-child(1) {
        width: 500px;
        height: 500px;
        background-color: #8b7355;
        top: -5%;
        left: -5%;
        animation-delay: 0s;
      }

      .animated-background .circle:nth-child(2) {
        width: 600px;
        height: 600px;
        background-color: #d2c0a7;
        bottom: -10%;
        right: -10%;
        animation-delay: 4s;
      }

      .animated-background .circle:nth-child(3) {
        width: 400px;
        height: 400px;
        background-color: #6b5a45;
        top: 50%;
        right: 15%;
        animation-delay: 8s;
      }

      .animated-background .circle:nth-child(4) {
        width: 350px;
        height: 350px;
        background-color: #e6dfd5;
        bottom: 15%;
        left: 10%;
        animation-delay: 12s;
      }

      @keyframes float {
        0% {
          transform: translate(0, 0) scale(1);
        }
        33% {
          transform: translate(3%, 2%) scale(1.03);
        }
        66% {
          transform: translate(-2%, 3%) scale(0.97);
        }
        100% {
          transform: translate(0, 0) scale(1);
        }
      }

      h1, h2, h3 {
        font-family: "Cinzel", serif;
        font-weight: 700;
        letter-spacing: 0.5px;
      }

      a {
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
      }

      /* Logo Styles */
      .logo {
        position: absolute;
        top: 20px;
        left: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: "Cinzel", serif;
        font-size: 32px;
        font-weight: 700;
        color: #b6210a;
        letter-spacing: 3px;
      }

      .logo img {
        width: 160px;
        height: 75px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(107, 90, 69, 0.2);
      }

      /* Main Container */
      .main-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        max-width: 1000px;
        width: 100%;
        padding: 20px;
        margin-left: auto;
        margin-right: 1in;
      }

      /* Auth Container */
      .auth-container {
        max-width: 550px; /* Increased width */
        padding: 40px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        box-shadow: 0 15px 50px rgba(107, 90, 69, 0.12);
        text-align: center;
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(230, 223, 213, 0.8);
      }

      .auth-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(107, 90, 69, 0.18);
      }

      .auth-container h2 {
        font-size: 28px;
        margin-bottom: 30px;
        color: #6b5a45;
        position: relative;
        display: inline-block;
      }

      .auth-container h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 2px;
        background-color: #8b7355;
      }

      .auth-container form {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .input-group {
        position: relative;
        text-align: left;
      }

      .input-group label {
        position: absolute;
        top: 15px;
        left: 15px;
        font-size: 14px;
        color: #8b7355;
        transition: all 0.3s ease;
        pointer-events: none;
        opacity: 0;
      }

      .input-group input {
        width: 100%;
        padding: 15px;
        font-size: 16px;
        border: 1px solid #e6dfd5;
        border-radius: 8px;
        background-color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        font-family: "Playfair Display", serif;
      }

      .input-group input:focus {
        outline: none;
        border-color: #8b7355;
        box-shadow: 0 0 0 2px rgba(139, 115, 85, 0.2);
        background-color: #fff;
      }

      .input-group input:focus + label,
      .input-group input:not(:placeholder-shown) + label {
        top: -10px;
        left: 10px;
        font-size: 12px;
        background-color: #fff;
        padding: 0 5px;
        opacity: 1;
      }

      .btn {
        display: inline-block;
        padding: 15px 20px;
        background-color: #000000;
        color: #fff;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 1px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: "Cinzel", serif;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(139, 115, 85, 0.2);
        margin-top: 10px;
      }

      .btn:hover {
        background-color: #6b5a45;
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(139, 115, 85, 0.25);
      }

      .btn:active {
        transform: translateY(1px);
        box-shadow: 0 2px 3px rgba(139, 115, 85, 0.2);
      }

      .terms-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin: 10px 0;
        text-align: left;
      }

      .terms-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #8b7355;
        margin-top: 3px;
      }

      .terms-checkbox label {
        font-size: 14px;
        color: #666;
        cursor: pointer;
        line-height: 1.4;
      }

      .terms-checkbox a {
        color: #dd1e1e;
        text-decoration: underline;
      }

      .terms-checkbox a:hover {
        color: #9a100e;
      }

      .auth-container p {
        margin-top: 25px;
        font-size: 15px;
        color: #666;
      }

      .auth-container p a {
        color: #08a208;
        font-weight: 500;
      }

      .auth-container p a:hover {
        color: #6b5a45;
        text-decoration: underline;
      }

      /* Responsive Styles */
      @media (max-width: 768px) {
        .main-container {
          flex-direction: column;
          gap: 20px;
        }

        .auth-container {
          max-width: 100%;
        }
      }

      @media (max-width: 480px) {
        .auth-container {
          padding: 30px 20px;
        }
        
        .auth-container h2 {
          font-size: 24px;
        }
        
        .btn {
          padding: 12px 15px;
        }
      }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="animated-background">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <!-- Logo -->
    <div class="logo">
        <img src="psvlogo1.png" alt="PSV QuickBuy Logo">
        <b>PSV QuickBuy</b>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Auth Container -->
        <div class="auth-container">
            <h2>Create Account</h2>
            <form id="signup-form" action="PSV-signup.php" method="post">
                <div class="input-group">
                    <input type="text" name="fName" id="fName" placeholder="First Name" required>
                    <label for="fName">First Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="lName" id="lName" placeholder="Last Name" required>
                    <label for="lName">Last Name</label>
                </div>
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit" class="btn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="PSV-login.php">Login</a></p>
        </div>
    </div>

    <script>
      // Handle form submission
      document.getElementById('signup-form').addEventListener('submit', function(event) {
          event.preventDefault(); // Prevent the default form submission

          // Get form values
          const fName = document.getElementById('fName').value;
          const lName = document.getElementById('lName').value;
          const email = document.getElementById('email').value;
          const password = document.getElementById('password').value;

          // Basic validation
          if (!fName || !lName || !email || !password) {
              alert("Please fill in all fields.");
              return;
          }

          // If validation passes, submit the form
          this.submit();
      });
    </script>
</body>
</html>