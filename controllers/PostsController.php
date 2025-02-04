<?php
namespace Controllers;

use PDOException;
use Exception;
use Requests\PostsRequest;
use Exceptions\ValidationException;
use QueryBuilder\QueryBuilder;

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
        $posts = [];
        try {
            $queryBuilder = new QueryBuilder();

            $posts = $queryBuilder->table('posts')
                ->select([
                    'posts.id',
                    'posts.title',
                    'posts.description',
                    'media.path AS cover_photo_path',
                    'users.username',
                    'media.user_id'
                ])
                ->leftJoin('media', 'posts.id', '=', 'media.post_id')
                ->leftJoin('users', 'media.user_id', '=', 'users.id')
                ->where('media.size', '>', 0) 
                ->orderBy('posts.created_at', 'DESC')
                ->get();
        } catch (PDOException $e) {
            setErrors(["Error: " . $e->getMessage()]);
        }

        include BASE_PATH . '/views/posts/blog_posts.php';
    }


    public function viewPost($postId)
    {
        try {
            $post = (new QueryBuilder())
                ->table('posts')
                ->select(['posts.id', 'posts.title', 'posts.description', 'media.path AS cover_photo_path', 'users.username'])
                ->leftJoin('media', 'posts.id', '=', 'media.post_id')
                ->leftJoin('users', 'media.user_id', '=', 'users.id')
                ->where('posts.id', '=', $postId)
                ->limit(1)
                ->get();

            if (!empty($post)) {
                $post = $post[0];
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
            $queryBuilder = new QueryBuilder();

            $post = $queryBuilder
                ->table('posts as p')
                ->select(['p.*', 'm.id AS media_id', 'm.path AS cover_photo_path'])
                ->leftJoin('media as m', 'p.id', '=', 'm.post_id')
                ->where('p.id', '=', $postId)
                ->where('m.photo_type', '=', 'cover')
                ->limit(1)
                ->get();

            if ($post) {
                $post = $post[0];

                if (isset($_POST['title']) && isset($_POST['description'])) {
                    try {
                        PostsRequest::validate($_POST, true);

                        $queryBuilder
                            ->table('posts')
                            ->update([
                                'title' => $_POST['title'],
                                'description' => $_POST['description']
                            ])
                            ->where('id', '=', $postId)
                            ->execute();

                        if (!empty($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                            $mediaId = $this->uploadCoverPhoto($_FILES['cover_photo'], $postId);

                            if ($post['media_id']) {
                                $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $post['cover_photo_path'];
                                if (file_exists($deleteFile)) {
                                    unlink($deleteFile);
                                }

                                (new QueryBuilder())
                                    ->table('media')
                                    ->delete()
                                    ->where('id', '=', $post['media_id'])
                                    ->execute();
                            }
                        }

                        redirect("/ATIS/views/posts/post/$postId");
                    } catch (ValidationException $e) {
                        setErrors([$e->getMessage()]);
                    }
                }

                include BASE_PATH . '/views/posts/edit_post.php';
            } else {
                setErrors(["Post not found"]);
            }
        } catch (PDOException $e) {
            setErrors(["Database Error: " . $e->getMessage()]);
        }
    }


    public function createPost()
    {
        $this->checkLoggedIn();

        try {
            PostsRequest::validate($_POST + $_FILES);

            $postId = (new QueryBuilder)
                ->table('posts')
                ->insert([
                    'title' => $_POST['title'],
                    'description' => $_POST['description']
                ]);

            $mediaId = $this->uploadCoverPhoto($_FILES['cover_photo'], $postId);

            header("Location: /ATIS/views/posts/blog");
            exit();
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
            header("Location: /ATIS/views/posts/new");
            exit();
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
        }
    }

    public function deletePost($postId)
{
    $this->checkLoggedIn();
    $isAdmin = $this->isAdmin();
    $userId = $_SESSION['user_id'];

    $post = (new QueryBuilder)
        ->table('posts p')
        ->select(['p.id', 'm.id AS media_id', 'm.path AS cover_photo_path', 'm.user_id'])
        ->leftJoin('media m', 'p.id', '=', 'm.post_id AND m.photo_type = "cover"')
        ->leftJoin('users u', 'm.user_id', '=', 'u.id')
        ->where('p.id', '=', $postId)
        ->getOne(); 

    if ($post) {
        if ($isAdmin || $post['user_id'] === $userId) {
            (new QueryBuilder)
                ->table('posts')
                ->delete()
                ->where('id', '=', $postId)
                ->execute();

            if ($post['media_id']) {
                $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $post['cover_photo_path'];
                if (file_exists($deleteFile)) {
                    unlink($deleteFile);  
                }

                (new QueryBuilder)
                    ->table('media')
                    ->delete()
                    ->where('id', '=', $post['media_id'])
                    ->execute();
            }

            redirect("/ATIS/views/posts/blog");
        } else {
            setErrors(["You do not have permission to delete this post."]);
        }
    } else {
        setErrors(["Post not found."]);
    }
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
            $existingMedia = (new QueryBuilder)
                ->table('media')
                ->select(['id', 'path'])
                ->where('post_id', '=', $postId)
                ->where('photo_type', '=', 'cover')
                ->getOne();
    
            if ($existingMedia) {
                $deleteFile = $_SERVER['DOCUMENT_ROOT'] . $existingMedia['path'];
                if (file_exists($deleteFile)) {
                    unlink($deleteFile);
                }

                (new QueryBuilder)
                    ->table('media')
                    ->delete()
                    ->where('id', '=', $existingMedia['id'])
                    ->execute();
            }
        }
    
        $mediaId = (new QueryBuilder)
            ->table('media')
            ->insert([
                'original_name' => $coverPhoto['name'],
                'hash_name' => $hashName,
                'path' => $path,
                'size' => $coverPhoto["size"],
                'extension' => $extension,
                'user_id' => $_SESSION['user_id'],
                'photo_type' => 'cover',
                'post_id' => $postId
            ]);
    
        return $mediaId;
    }

}