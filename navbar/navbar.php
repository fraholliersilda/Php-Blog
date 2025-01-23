<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

$is_admin = false;


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user data 
    $stmt = $conn->prepare("SELECT u.*, r.role FROM users u
    INNER JOIN roles r ON u.role = r.id
    WHERE u.id = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $is_admin = ($user['role'] === 'admin'); 
    }
} else {
    header("Location: /ATIS/pages/registration/index.php");
    exit();
}
?>

<!-- navbar -->
<div class="dashboard-nav">
    <header>
        <a href="#" class="brand-logo"><i class="fas fa-anchor"></i> <span>PROJECT</span></a>
    </header>
    <nav class="dashboard-nav-list">
        <a href="/ATIS/pages/posts/blog_posts.php" class="dashboard-nav-item <?php echo ($current_page == 'blog_posts.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Blog Posts
        </a>
        <?php if (!$is_admin): ?>
            <!-- show new post only if normal user -->
        <a href="/ATIS/pages/posts/new_post.php" class="dashboard-nav-item <?php echo ($current_page == ' new_post.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> New post
        </a>
        <?php endif; ?>

        <?php if ($is_admin): ?>
    <div class="dropdown">
        <button class="dashboard-nav-item" style="background-color: #16a085; width: 100%; color: white; border: none;">
            <i class="fas fa-cogs"></i>Admin Dashboard 
            <i class="fa-solid fa-caret-down"  style="margin-left: 2px;"></i>
        </button>
        <div class="dropdown-menu" style="background-color: #16a085;  width: 100%;">
            <a href="/ATIS/pages/admin/admins.php" class="dashboard-nav-item <?php echo ($current_page == 'admins.php') ? 'active' : ''; ?>" style="color: white;">
                <i class="fas fa-user-shield"></i> Admins
            </a>
            <a href="/ATIS/pages/admin/users.php" class="dashboard-nav-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>" style="color: white;">
                <i class="fas fa-users"></i> Users
            </a>
        </div>
    </div>
<?php endif; ?>



        <a href="/ATIS/pages/profile/profile.php" class="dashboard-nav-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Profile
        </a>

     

        <div class="nav-item-divider log"></div>
        <a href="/ATIS/actions/logout.php" class="dashboard-nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>
