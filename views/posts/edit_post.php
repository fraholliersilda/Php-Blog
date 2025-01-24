<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/controlFunction.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user = getLoggedInUser();
$is_admin = isAdmin();

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Fetch the post details
    $stmt = $conn->prepare("
        SELECT p.*, m.user_id AS media_user_id, m.path AS cover_photo_path
        FROM posts p
        LEFT JOIN media m ON p.id = m.post_id AND m.photo_type = 'cover'
        WHERE p.id = :id LIMIT 1
    ");
    $stmt->execute(['id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        if (!$is_admin && $post['media_user_id'] !== $user['id']) {
            echo "You do not have permission to edit this post.";
            exit();
        }
    } else {
        echo "Post not found.";
        exit();
    }
} else {
    header("Location: /ATIS/views/posts/blog");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="/ATIS/css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
<?php include BASE_PATH . '/actions/display_errors.php'; ?>
    <h1 class="edit-post-title">Edit Post</h1>
    <form action="/ATIS/views/posts/edit" method="POST" enctype="multipart/form-data" class="edit-post-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($post['id']); ?>">
        <div class="form-group">
            <label for="title" class="form-label">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" id="description" class="form-textarea" required><?php echo htmlspecialchars($post['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="cover_photo" class="form-label">Change Cover Photo:</label>
            <input type="file" name="cover_photo" id="cover_photo" accept="image/*" class="form-file">
            <br>
            <?php if (isset($post['cover_photo_path']) && $post['cover_photo_path']): ?>
                <img src="<?php echo htmlspecialchars($post['cover_photo_path']); ?>" alt="Current Cover Photo" class="cover-photo" width="150">
            <?php endif; ?>
        </div>
        <div>
            <button type="submit" class="submit-button">Update Post</button>
        </div>
    </form>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
