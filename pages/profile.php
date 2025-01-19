<?php
include '../actions/profile/profile.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <?php include('../navbar/navbar.php'); ?>
    <!-- errors -->
    <?php if (isset($_SESSION["messages"]["errors"])) {
        foreach ($_SESSION["messages"]["errors"] as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        unset($_SESSION["messages"]["errors"]);
    }
    ?>
    <div class="container">
        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">

                            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Admin"
                                class="rounded-circle" width="150">
                            <div class="mt-3">
                                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                <p class="text-secondary mb-1">User Role: Basic User</p>
                                <p class="text-muted font-size-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-0">Full Name</div>
                            </div>
                            <div class="col-sm-9">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-0">Email</div>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-0">User ID</div>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php echo htmlspecialchars($user['id']); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-info" target="_self" href="edit_profile.php"
                                    style="background-color:#1abc9c; color: white; border-color: #1abc9c;">Edit
                                    Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>