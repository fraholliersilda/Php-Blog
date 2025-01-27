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
        <form method="POST" action="/ATIS/views/registration/login">
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
            <?php
            if (isset($_SESSION['messages']['errors']) && !empty($_SESSION['messages']['errors'])) {
                foreach ($_SESSION['messages']['errors'] as $error) {
                    echo "<div class='error-message'>{$error}</div>";
                }
                unset($_SESSION['messages']['errors']); 
            }
            ?>

        </form>

    </div>
</body>

</html>