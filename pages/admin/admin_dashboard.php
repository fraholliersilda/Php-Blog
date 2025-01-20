<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../home.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

// Fetch all users
$stmt = $conn->prepare("SELECT u.id, u.username, u.email, r.role FROM users u JOIN roles r ON u.role = r.id");
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
                    <p>Role: <?= htmlspecialchars($user['role']) ?></p>

                    <!-- Update Role Form -->
                    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <label for="role">Role:</label>
                        <select name="role">
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit"  style="width: 100%;">Update Role</button>
                    </form>

                    <!-- Update Password Form -->
                    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php">
                        <input type="hidden" name="action" value="update_password">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <label for="password">New Password:</label>
                        <input type="password" name="password" required>
                        <button type="submit"  style="width: 100%;">Update Password</button>
                    </form>

                    <!-- Delete User Form -->
                    <form method="POST" action="/ATIS/actions/admin/admin_dashboard.php" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <button type="submit"  class="update" style="width: 100%; background-color: #A72925;">Delete User</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
    <script src="../../js/delete_user.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>