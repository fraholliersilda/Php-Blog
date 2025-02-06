<?php
namespace Controllers;

use PDOException;
use Exception;
use Requests\PostsRequest;
use Exceptions\ValidationException;

use Models\Post;
use Models\Media;

require_once 'redirect.php';
require_once 'errorHandler.php';

class PostsController extends BaseController
{

    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    public function listPosts()
    {
        try {
            $posts = (new Post)->getAllPost();
            include BASE_PATH . '/views/posts/blog_posts.php';
        } catch (PDOException $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }
    }

    public function viewPost($postId)
    {
        try {
            $post = (new Post)->getPostById($postId);

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
            $post = (new Post)->getPostById($postId);

            if (!$post) {
                setErrors(["Post not found"]);
                return;
            }

            $coverPhoto = (new Media)->getCoverPhotoByPostId($postId);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
                try {
                    PostsRequest::validate($_POST, true);

                    (new Post)->updatePost($postId, [
                        'title' => $_POST['title'],
                        'description' => $_POST['description']
                    ]);
                    if (!empty($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                        if ($coverPhoto) {
                            $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                            if (file_exists($deleteFile)) {
                                unlink($deleteFile);
                            }

                            (new Media)->deleteMediaById($coverPhoto['id']);
                        }

                        (new Media)->saveCoverPhoto($_FILES['cover_photo'], $postId);
                    }
                    redirect("/ATIS/views/posts/post/$postId");
                } catch (ValidationException $e) {
                    setErrors([$e->getMessage()]);
                    redirect("/ATIS/views/posts/post/$postId");
                }
            }

            include BASE_PATH . '/views/posts/edit_post.php';
        } catch (PDOException $e) {
            setErrors(["Database Error: " . $e->getMessage()]);
            redirect("/ATIS/views/posts/post/$postId");
        }
    }


    public function createPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
            try {
                PostsRequest::validate($_POST);

                $postId = (new Post)->createPost([
                    'title' => $_POST['title'],
                    'description' => $_POST['description']
                ]);

                if ($postId) {
                    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === 0) {
                        (new Media)->saveCoverPhoto($_FILES['cover_photo'], $postId);
                    }

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

        include BASE_PATH . '/views/posts/new_post.php';
    }

    public function showNewPost()
    {
        include BASE_PATH . '/views/posts/new_post.php';
        exit();
    }

    public function deletePost($postId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $post = (new Post)->getPostById($postId);

                if (!$post) {
                    setErrors(["Post not found."]);
                    return;
                }

                $coverPhoto = (new Media)->getCoverPhotoByPostId($postId);
                if ($coverPhoto) {
                    (new Media)->deleteMediaById($coverPhoto['id']);
                    $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $coverPhoto['path'];
                    if (file_exists($deleteFile)) {
                        unlink($deleteFile);
                    }
                }

                error_log("Before deleting post: $postId");
                (new Post)->deletePost($postId);
                error_log("Post deleted: $postId");

                redirect('/ATIS/views/posts/blog');
            } catch (Exception $e) {
                setErrors(["Error: " . $e->getMessage()]);
                redirect('/ATIS/views/posts/blog');
            }
        }
    }

}

