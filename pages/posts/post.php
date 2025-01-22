<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/posts/post.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
</head>
<body>
<?php include('../../navbar/navbar.php'); ?>

<div class="container">
    <div class="post">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="author">By: <?php echo htmlspecialchars($post['username']); ?></p>
        <img src="<?php echo htmlspecialchars($post['cover_photo_path']); ?>" alt="Cover Photo" class="img-responsive">
        <p class="description"><?php echo htmlspecialchars($post['description']); ?></p>
    </div>
</div>
<script src="../../js/script.js"></script>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
