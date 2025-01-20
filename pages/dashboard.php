<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle cover photo upload
    $cover_photo = $_FILES['cover_photo'];
    $original_name = $cover_photo['name'];
    $hash_name = md5(uniqid()) . '.' . pathinfo($cover_photo['name'], PATHINFO_EXTENSION);
    $path = "../uploads/" . $hash_name;
    $size = $cover_photo['size'];
    $extension = pathinfo($cover_photo['name'], PATHINFO_EXTENSION);
    $photo_type = 'cover';
    $user_id = 1; // Replace with the logged-in user's ID

    try {
        // Upload the file
        if (move_uploaded_file($cover_photo['tmp_name'], $path)) {
            // Insert into media table
            $media_query = "
                INSERT INTO media (original_name, hash_name, path, size, extension, user_id, photo_type)
                VALUES (:original_name, :hash_name, :path, :size, :extension, :user_id, :photo_type)";
            $stmt = $conn->prepare($media_query);
            $stmt->execute([
                ':original_name' => $original_name,
                ':hash_name' => $hash_name,
                ':path' => $path,
                ':size' => $size,
                ':extension' => $extension,
                ':user_id' => $user_id,
                ':photo_type' => $photo_type
            ]);
            $cover_photo_id = $conn->lastInsertId();

            // Insert into posts table
            $post_query = "
                INSERT INTO posts (title, description, cover_photo_id) 
                VALUES (:title, :description, :cover_photo_id)";
            $stmt = $conn->prepare($post_query);
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':cover_photo_id' => $cover_photo_id
            ]);

            echo "Post added successfully!";
        } else {
            echo "Error uploading file.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
<?php include('../navbar/navbar.php'); ?>
    <?php include('../actions/display_errors.php'); ?>


    <form method="POST" enctype="multipart/form-data" class="post-form">
        <label for="title">Title:</label>
        <br>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="description">Description:</label>
        <br>
        <textarea id="description" name="description" required></textarea>
        <br>
        <label for="cover_photo">Cover Photo:</label>
        <br>
        <input type="file" id="cover_photo" name="cover_photo" required>
        <br>
        <button type="submit">Add Post</button>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>