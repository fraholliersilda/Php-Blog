<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../profile/profile.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/fetch_users.php';

$users = fetchUsersByRole($conn, 'user');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <?php include('../../navbar/navbar.php'); ?>
    <div class="admin_dashboard">
        <h1><b>Users</b></h1>
        <?php include('../../actions/display_errors.php'); ?>
        <div class="user-cards">
            <?php foreach ($users as $user): ?>
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
                        <button type="submit" >Update User</button>
                    </form>

                    <!-- Delete User Form -->
                    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <button type="submit" style="background-color: #A72925;">Delete User</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="../../js/script.js"></script>
</body>
</html>
