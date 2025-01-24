<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <div class="title"><span>Login Form</span></div>
        <form method="POST" action="../../actions/registration/login.php">
            <div class="row">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="row">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <div class="row button">
                <input type="submit" value="Login">
            </div>
            <div class="signup-link">
                Not a member? <a href="signup">Signup now</a>
            </div>
        </form>

        <?php include BASE_PATH . '/actions/display_errors.php'; ?>
</div>
</body>
</html>
