<?php 
require_once 'successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="/ATIS/css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
<?php displaySuccessMessages(); ?>
    <div class="container">
        <h1><b>BLOG POSTS</b></h1>
        <div class="row">
      <?php if (!empty($posts)) { ?>
    <?php foreach ($posts as $post) { ?>
        <div class="col-md-12 post-card">
            <div class="post-content">
                <div class="post-image">
                <img src="<?= htmlspecialchars($post['cover_photo_path'] ?? 'default_path.jpg'); ?>" alt="Cover Photo" class="card-img-top">
                </div>
                <div class="post-details">
                    <h5 class="card-title"><?= htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><em>By: <?= htmlspecialchars($post['username'] ?? 'Unknown'); ?></em></p>
                    <p class="card-text"><?= htmlspecialchars(substr($post['description'], 0, 300)); ?>...</p>
                    <a href="<?= BASE_URL ?>/views/posts/post/<?= $post['id']; ?>" class="btn btn-secondary">Read More</a>
                    <?php if ($is_admin || $_SESSION['user_id'] === $post['user_id']) { ?>
                        <a href="/ATIS/views/posts/edit/<?php echo $post['id']; ?>" class="btn btn-primary">Edit</a>
                        <form action="<?= BASE_URL ?>/posts/delete/<?= $post['id'] ?>" method="post" style="display: inline;">
        <input type="hidden" name="id" value="<?= $post['id']; ?>">
        <button type="submit" class="btn btn-danger" onclick="confirmDeletePost(event)">Delete</button>
    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>No posts found.</p>
<?php } ?>

    </div>


    <script src="../../js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>