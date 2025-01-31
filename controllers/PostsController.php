<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;
use Requests\PostsRequest;
use Exceptions\ValidationException;

require_once __DIR__ . '/BaseController.php';

class PostsController extends BaseController
{
    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    public function listPosts()
    {
        $posts = [];
        try {
            $query = "SELECT posts.id, posts.title, posts.description,
                             media.path AS cover_photo_path, users.username, media.user_id
                      FROM posts
                      LEFT JOIN media ON posts.id = media.post_id AND media.photo_type = 'cover'
                      LEFT JOIN users ON media.user_id = users.id
                      ORDER BY posts.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        include BASE_PATH . '/views/posts/blog_posts.php';
    }

    public function viewPost($postId)
    {
        try {
            $query = "SELECT posts.id, posts.title, posts.description,
                             media.path AS cover_photo_path, users.username
                      FROM posts
                      LEFT JOIN media ON posts.id = media.post_id AND media.photo_type = 'cover'
                      LEFT JOIN users ON media.user_id = users.id
                      WHERE posts.id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post) {
                include BASE_PATH . '/views/posts/post.php';
            } else {
                echo "Post not found.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function editPost($postId)
    {
        require_once __DIR__ . '/../Requests/PostsRequest.php';
    
        try {
            $query = "SELECT p.*, m.id AS media_id, m.user_id AS media_user_id, m.path AS cover_photo_path
                      FROM posts p
                      LEFT JOIN media m ON p.id = m.post_id AND m.photo_type = 'cover'
                      WHERE p.id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($post) {
                if (isset($_POST['title']) && isset($_POST['description'])) {
                    try {
                        PostsRequest::validate($_POST);
    
                        $updateQuery = "UPDATE posts SET title = :title, description = :description WHERE id = :id";
                        $stmt = $this->conn->prepare($updateQuery);
                        $stmt->execute([
                            ':title' => $_POST['title'],
                            ':description' => $_POST['description'],
                            ':id' => $postId
                        ]);
    
                        if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                            $mediaId = $this->uploadCoverPhoto($_FILES['cover_photo'], $postId);
    
                            if ($post['media_id']) {
                                $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $post['cover_photo_path'];
                                if (file_exists($deleteFile)) {
                                    unlink($deleteFile);
                                }
    
                                $deleteMediaStmt = $this->conn->prepare("DELETE FROM media WHERE id = :media_id");
                                $deleteMediaStmt->execute(['media_id' => $post['media_id']]);
                            }
                            header("Location: /ATIS/views/posts/post/$postId");
                            exit();
                        }
                    } catch (ValidationException $e) {
                        $_SESSION['error_messages'] = ['validation' => $e->getMessage()];
                    }
                }
                include BASE_PATH . '/views/posts/edit_post.php';
            } else {
                echo "Post not found.";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
    

    public function createPost()
    {
        $this->checkLoggedIn();
        require_once __DIR__ . '/../Requests/PostsRequest.php';
    
        try {
            PostsRequest::validate($_POST);
    
            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                PostsRequest::validate(['cover_photo' => $_FILES['cover_photo']]);
            } else {
                throw new ValidationException("Cover photo is required.");
            }
    
            $postQuery = "INSERT INTO posts (title, description) VALUES (:title, :description)";
            $stmt = $this->conn->prepare($postQuery);
            $stmt->execute([
                ':title' => $_POST['title'],
                ':description' => $_POST['description']
            ]);
            $postId = $this->conn->lastInsertId();
    
            $mediaId = $this->uploadCoverPhoto($_FILES['cover_photo'], $postId);
    
            if ($mediaId !== null) {
                header("Location: /ATIS/views/posts/blog");
                exit();
            } else {
                throw new ValidationException("Failed to upload cover photo.");
            }
        } catch (ValidationException $e) {
            $_SESSION['error_messages'] = ['validation' => $e->getMessage()];
            header("Location: /ATIS/views/posts/new");
            exit();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
       public function deletePost($postId)
    {
        $this->checkLoggedIn();
        $isAdmin = $this->isAdmin();
        $userId = $_SESSION['user_id'];

        $stmt = $this->conn->prepare("SELECT p.id, m.id AS media_id, m.path AS cover_photo_path, m.user_id as user_id
                                  FROM posts p
                                  LEFT JOIN media m ON p.id = m.post_id AND m.photo_type = 'cover'
                                  LEFT JOIN users u ON m.user_id = u.id
                                  WHERE p.id = :id");
        $stmt->execute(['id' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            if ($isAdmin || $post['user_id'] === $userId) {
                $deletePostStmt = $this->conn->prepare("DELETE FROM posts WHERE id = :id");
                $deletePostStmt->execute(['id' => $postId]);

                if ($post['media_id']) {
                    $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $post['cover_photo_path'];
                    if (file_exists($deleteFile)) {
                        unlink($deleteFile);
                    }

                    $deleteMediaStmt = $this->conn->prepare("DELETE FROM media WHERE id = :media_id");
                    $deleteMediaStmt->execute(['media_id' => $post['media_id']]);
                }

                header("Location: /ATIS/views/posts/blog");
                exit();
            } else {
                echo "You do not have permission to delete this post.";
            }
        } else {
            echo "Post not found.";
        }
    }
    
    public function showNewPost()
    {
        include BASE_PATH . '/views/posts/new_post.php';
        exit();
    }

    private function uploadCoverPhoto($coverPhoto, $postId = null)
    {
        $extension = strtolower(pathinfo($coverPhoto['name'], PATHINFO_EXTENSION));
        $hashName = md5(uniqid(time(), true)) . "." . $extension;
        $fileSize = $coverPhoto["size"];

        if (!in_array($extension, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/ATIS/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . $hashName;
        $path = "/ATIS/uploads/" . $hashName;

        if (!move_uploaded_file($coverPhoto['tmp_name'], $targetFile)) {
            throw new Exception("Failed to upload the cover photo.");
        }

        if ($postId) {
            $checkMediaQuery = "SELECT id FROM media WHERE post_id = :post_id AND photo_type = 'cover'";
            $stmt = $this->conn->prepare($checkMediaQuery);
            $stmt->execute([':post_id' => $postId]);
            $existingMedia = $stmt->fetch();

            if ($existingMedia) {
                $mediaId = $existingMedia['id'];
                $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $existingMedia['path'];
                if (file_exists($deleteFile)) {
                    unlink($deleteFile);
                }

                $deleteMediaQuery = "DELETE FROM media WHERE id = :media_id";
                $stmt = $this->conn->prepare($deleteMediaQuery);
                $stmt->execute([':media_id' => $mediaId]);
            }
        }

        $photoType = 'cover';
        $mediaQuery = "INSERT INTO media (original_name, hash_name, path, size, extension, user_id, photo_type, post_id)
                       VALUES (:original_name, :hash_name, :path, :size, :extension, :user_id, :photo_type, :post_id)";
        $stmt = $this->conn->prepare($mediaQuery);
        $stmt->execute([
            ':original_name' => $coverPhoto['name'],
            ':hash_name' => $hashName,
            ':path' => $path,
            ':size' => $fileSize,
            ':extension' => $extension,
            ':user_id' => $_SESSION['user_id'],
            ':photo_type' => $photoType,
            ':post_id' => $postId
        ]);

        return $this->conn->lastInsertId();
    }
    }

