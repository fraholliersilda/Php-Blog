<?php 
require_once 'errorHandler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <div class="title"><span>Admin Login</span></div>
        <form method="POST" action="/ATIS/views/admin/login">
            <div class="row">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Admin Email" required>
            </div>
            <div class="row">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="row button">
                <input type="submit" value="Login">
            </div>
            <div class="signup-link">
                <a href="../registration/login">User Login</a>
            </div>

            <!-- Display errors using displayErrors function -->
            <?php
            if (isset($_SESSION['messages']['errors']) && !empty($_SESSION['messages']['errors'])) {
                echo '<div class="error-message">';
                displayErrors();
                echo '</div>';
            }
            ?>

        </form>
    </div>
</body>
</html>
