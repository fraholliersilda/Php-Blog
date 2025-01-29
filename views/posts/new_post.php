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
<?php 

// include '../../navbar/navbar.php'; 
include BASE_PATH . '/navbar/navbar.php'; 
?>

<form method="POST" enctype="multipart/form-data" class="post-form" action="/ATIS/views/posts/new">
    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" ><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" ></textarea><br>

    <label for="cover_photo">Cover Photo:</label><br>
    <input type="file" id="cover_photo" name="cover_photo" ><br>

    <button type="submit">Add Post</button>

    <?php if (!empty($_SESSION['error_messages'])): ?>
    <div class="error-messages">
        <?php foreach ($_SESSION['error_messages'] as $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['error_messages']); ?>
<?php endif; ?>






</form>
<script src="../../js/script.js"></script>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
