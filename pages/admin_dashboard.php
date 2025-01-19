<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: registration/admin_login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

// Fetch all users
$stmt = $conn->query("SELECT id, username, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<?php include('../navbar/navbar.php'); ?>
<div class="admin_dashboard">
    <h1>Admin Dashboard</h1>
    <?php if (!empty($_SESSION['messages'])): ?>
    <div class="messages">
        <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="<?= $type ?>"><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php unset($_SESSION['messages']); ?>
    </div>
<?php endif; ?>

    <div class="user-cards">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <h3><?= htmlspecialchars($user['username']) ?></h3>
                <p>Email: <?= htmlspecialchars($user['email']) ?></p>
                <p>Role: <?= htmlspecialchars($user['role']) ?></p>

                <!-- Update Role and User Info Form -->
                <form method="POST" action="/ATIS/actions/admin_dashboard.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    <label for="role">Role:</label>
                    <select name="role">
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <button type="submit">Update User</button>
                </form>

                <!-- Delete User Form -->
                <form method="POST" action="/ATIS/actions/admin_dashboard.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>

