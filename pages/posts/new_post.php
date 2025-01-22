<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/posts/new_post.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<?php include('../../navbar/navbar.php'); ?>
<?php include('../../actions/display_errors.php'); ?>

<form method="POST" enctype="multipart/form-data" class="post-form" action="../../actions/posts/new_post.php">
    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" required><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" required></textarea><br>

    <label for="cover_photo">Cover Photo:</label><br>
    <input type="file" id="cover_photo" name="cover_photo" required><br>

    <button type="submit">Add Post</button>
</form>
<script src="../../js/script.js"></script>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
