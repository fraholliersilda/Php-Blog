<?php
namespace Controllers;

use PDOException;
use Exception;
use Requests\PostsRequest;
use Exceptions\ValidationException;

use Models\Posts;
use Models\Media;

require_once 'redirect.php';
require_once 'errorHandler.php';

class PostsController extends BaseController
{
    private $postsModel;
    private $mediaModel;
    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->postsModel = new Posts();
        $this->mediaModel = new Media();
    }

    public function listPosts()
    {
        try {
            $posts = $this->postsModel->getAllPosts();
            include BASE_PATH . '/views/posts/blog_posts.php';
        } catch (PDOException $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }
    }

    public function viewPost($postId)
    {
        try {
            $post = $this->postsModel->getPostById($postId);

            if ($post) {
                include BASE_PATH . '/views/posts/post.php';
            } else {
                setErrors(["Post not found."]);
            }
        } catch (PDOException $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }
    }

    public function editPost($postId)
{
    try {
        $post = $this->postsModel->getPostById($postId);

        if (!$post) {
            setErrors(["Post not found"]);
            return;
        }

        $coverPhoto = $this->mediaModel->getCoverPhotoByPostId($postId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
            try {
                PostsRequest::validate($_POST, true);

                $this->postsModel->updatePost($postId, [
                    'title' => $_POST['title'],
                    'description' => $_POST['description']
                ]);

                if (!empty($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                    $mediaId = $this->uploadCoverPhoto($_FILES['cover_photo'], $postId);

                    if ($coverPhoto) {

                        $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                        if (file_exists($deleteFile)) {
                            unlink($deleteFile);
                        }

                        $this->mediaModel->deleteMediaById($coverPhoto['id']);
                    }
                }

                redirect("/ATIS/views/posts/post/$postId");
            } catch (ValidationException $e) {
                setErrors([$e->getMessage()]);
            }
        }

        include BASE_PATH . '/views/posts/edit_post.php';
    } catch (PDOException $e) {
        setErrors(["Database Error: " . $e->getMessage()]);
    }
}


public function createPost()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
        try {
            // Validate input data
            PostsRequest::validate($_POST);

            // Create the post
            $postId = $this->postsModel->createPost([
                'title' => $_POST['title'],
                'description' => $_POST['description']
            ]);

            if ($postId) {
                // If there's a cover photo, save it
                if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === 0) {
                    $this->mediaModel->saveCoverPhoto($_FILES['cover_photo'], $postId);
                }

                // Redirect after successful creation
                redirect('/ATIS/views/posts/blog');
            } else {
                setErrors(['Failed to create post.']);
            }
        } catch (Exception $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }
    } else {
        setErrors(['Please fill out all fields.']);
    }

    include BASE_PATH . '/views/posts/new';
}



    public function showNewPost()
    {
        include BASE_PATH . '/views/posts/new_post.php';
        exit();
    }
    
    private function uploadCoverPhoto($coverPhoto, $postId = null)
{
    if (!isset($coverPhoto['tmp_name']) || $coverPhoto['error'] !== UPLOAD_ERR_OK) {
        throw new ValidationException("Error uploading the cover photo.");
    }

    $extension = strtolower(pathinfo($coverPhoto['name'], PATHINFO_EXTENSION));
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/ATIS/uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $hashName = md5(uniqid(time(), true)) . "." . $extension;
    $targetFile = $targetDir . $hashName;
    $path = "/ATIS/uploads/" . $hashName;  

    if (!move_uploaded_file($coverPhoto['tmp_name'], $targetFile)) {
        throw new ValidationException("Failed to upload the cover photo.");
    }

    if ($postId) {
        $this->mediaModel->updateCoverPhoto($coverPhoto, $path, $postId);
    }

    return $this->mediaModel->saveCoverPhoto($coverPhoto, $postId);
}


public function deletePost($postId)
{
    try {
        // Check if the post exists
        $post = $this->postsModel->getPostById($postId);

        if (!$post) {
            setErrors(["Post not found."]);
            return;
        }

        // Check and delete the cover photo from the media table
        $coverPhoto = $this->mediaModel->getCoverPhotoByPostId($postId);
        if ($coverPhoto) {
            $this->mediaModel->deleteMediaById($coverPhoto['id']);
            $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
            if (file_exists($deleteFile)) {
                unlink($deleteFile);
            }
        }

        // Delete the post
        $this->postsModel->deletePost($postId);

        // Redirect after deletion
        redirect('/ATIS/views/posts/blog');
    } catch (Exception $e) {
        setErrors(["Error: " . $e->getMessage()]);
    }
}

}

