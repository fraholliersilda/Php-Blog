<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

require_once '../actions/db.php'; 

$is_admin = false;


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user data 
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
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
        <a href="/ATIS/pages/home.php" class="dashboard-nav-item <?php echo ($current_page == 'home.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="/ATIS/pages/dashboard.php" class="dashboard-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <?php if ($is_admin): ?>
            <!-- show admiin dashboard -->
            <a href="/ATIS/pages/admin_dashboard.php" class="dashboard-nav-item <?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> Admin Dashboard
            </a>
        <?php endif; ?>
        <a href="/ATIS/pages/profile.php" class="dashboard-nav-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Profile
        </a>

     

        <div class="nav-item-divider log"></div>
        <a href="/ATIS/actions/logout.php" class="dashboard-nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>
