<?php 
require_once __DIR__ . '/../../errorHandler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGN UP</title>
  <link rel="stylesheet" href="../../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>
<body>
  <div class="wrapper">
    <div class="title"><span>SignUp Form</span></div>
    <form method="POST" action="/ATIS/views/registration/signup">
      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required />
      </div>
      <div class="row">
      <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required />
      </div>
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required />
      </div>
      <div class="row button">
        <input type="submit" value="Sign Up" />
      </div>
      <div class="signup-link">Already a member? <a href="login">Login now</a></div>
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