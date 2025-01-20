<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
$id = $_GET['id'];

try {
    $query = "
        SELECT posts.title, posts.description, media.path AS cover_photo_path 
        FROM posts 
        LEFT JOIN media ON posts.cover_photo_id = media.id 
        WHERE posts.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title><?php echo $post['title']; ?></title>
</head>
<body>
<?php include('../navbar/navbar.php'); ?>
<div class="post">
    <h1><?php echo $post['title']; ?></h1>
    <img src="<?php echo $post['cover_photo_path']; ?>" alt="Cover Photo">
    <p class="description"><?php echo $post['description']; ?></p>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>