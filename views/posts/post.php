<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ATIS/css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
</head>
<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
<?php include BASE_PATH . '/actions/display_errors.php'; ?>

<div class="container">
    <h1 class="post-title"><?= htmlspecialchars($post['title']); ?></h1>
    <div class="post-author">
        <p><em>By: <?= htmlspecialchars($post['username']); ?></em></p>
    </div>
    <div class="post-image">
        <img src="<?= htmlspecialchars($post['cover_photo_path']); ?>" alt="Cover Photo" class="img-fluid">
    </div>
    <div class="post-description">
        <p><?= nl2br(htmlspecialchars($post['description'])); ?></p>
    </div>
    
    <!-- Optional: You can add a back link to return to the blog list -->
    <a href="/ATIS/views/posts/blog" class="btn btn-primary" style="background-color: #16a085; margin-bottom:10px">Back to Blog</a>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
