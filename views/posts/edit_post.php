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
<h1 class="edit-post-title">Edit Post</h1>
<form action="/ATIS/views/posts/edit/<?php echo htmlspecialchars($post['id']); ?>" method="POST" enctype="multipart/form-data" class="edit-post-form">
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
