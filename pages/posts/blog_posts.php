<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/posts/blog_posts.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<?php include('../../navbar/navbar.php'); ?>
<?php include('../../actions/display_errors.php'); ?>

<div class="container">
    <h1>Blog Posts</h1>
    <div class="row">
        <?php foreach ($posts as $post) { ?>
            <div class="col-md-12 post-card">
                <div class="post-content">
                    <div class="post-image">
                        <img src="<?php echo $post['cover_photo_path']; ?>" alt="Cover Photo" class="card-img-top">
                    </div>
                    <div class="post-details">
                        <h5 class="card-title"><?php echo $post['title']; ?></h5>
                        <p class="card-text"><?php echo substr($post['description'], 0, 100); ?>...</p>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary">Read More</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
