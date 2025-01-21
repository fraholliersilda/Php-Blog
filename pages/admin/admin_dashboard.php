<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../profile/profile.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

// Fetch all users
$stmt = $conn->prepare("SELECT id, username, email FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <?php include('../../navbar/navbar.php'); ?>
    <div class="admin_dashboard">
        <h1>Admin Dashboard</h1>
        <?php include('../../actions/display_errors.php'); ?>
        <div class="user-cards">
            <?php foreach ($users as $user): ?>
                <?php if ($user['id'] === $_SESSION['user_id']): ?>
                    <!-- Skip the admin's own account -->
                    <?php continue; ?><?php endif; ?>
                <div class="user-card">
                    <h3><?= htmlspecialchars($user['username']) ?></h3>
                    <p>Email: <?= htmlspecialchars($user['email']) ?></p>

                    <!-- Update Username and Email Form -->
                    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php">
                        <input type="hidden" name="action" value="update_user">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <label for="username">Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        <button type="submit" style="width: 100%;">Update User</button>
                    </form>

                    <!-- Delete User Form -->
                    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <button type="submit" class="update" style="width: 100%; background-color: #A72925;">Delete
                            User</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Create New User Form -->
        <h2>Create New User</h2>
<div class="create-user-form-container">
    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php" class="create-user-form">
        <input type="hidden" name="action" value="create_user">
        <label for="username">Username:</label>
        <input type="text" name="username" required placeholder="Enter username" class="form-control">
        
        <label for="email">Email:</label>
        <input type="email" name="email" required placeholder="Enter email" class="form-control">
        
        <label for="password">Password:</label>
        <input type="password" name="password" required placeholder="Enter password" class="form-control">
        <br>
        <button type="submit" class="btn btn-primary create-user-btn">Create User</button>
    </form>
</div>

    </div>
    <script src="../../js/delete_user.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>