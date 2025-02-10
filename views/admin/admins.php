<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
    <div class="admin_dashboard">
        <h1><b>Admins</b></h1>
        <div class="user-cards">
            <?php foreach ($admins as $admin): ?>
                <?php if ($admin['id'] === $_SESSION['user_id']): ?>
                    <?php continue; ?><?php endif; ?>
                <div class="user-card">
                    <h3><?= htmlspecialchars($admin['username']) ?></h3>
                    <p>Email: <?= htmlspecialchars($admin['email']) ?></p>

                    <!-- Update Username and Email Form -->
                    <form method="POST" action="/ATIS/views/admin/admins">
                        <input type="hidden" name="action" value="update_user">
                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                        <label for="username">Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        <button type="submit">Update Admin</button>
                    </form>

                    <!-- Delete Admin Form -->
                    <form method="POST" action="/ATIS/views/admin/admins" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                        <button type="submit" style="background-color: #A72925;">Delete Admin</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="../../js/script.js"></script>
</body>
</html>
