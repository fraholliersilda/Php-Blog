<?php
namespace Models;

class Post extends Model
{
    public $table = 'posts';
    public $fields = [
        'id',
        'title',
        'description'
    ];

    public function getAllPost()
    {
        return $this->queryBuilder
            ->table('posts')
            ->select(['posts.id', 'posts.title', 'posts.description', 'media.path AS cover_photo_path', 'users.username', 'media.user_id'])
            ->leftJoin('media', 'posts.id', '=', 'media.post_id')
            ->leftJoin('users', 'media.user_id', '=', 'users.id')
            ->where('media.size', '>', 0)
            ->orderBy('posts.created_at', 'DESC')
            ->get();
    }

    public function getLatestPosts()
{
    return $this->queryBuilder
    ->table('posts')
    ->select(['posts.id', 'posts.title', 'posts.description', 'media.path AS cover_photo_path', 'users.username', 'media.user_id'])
    ->leftJoin('media', 'posts.id', '=', 'media.post_id')
    ->leftJoin('users', 'media.user_id', '=', 'users.id')
    ->where('media.size', '>', 0)
    ->orderBy('posts.created_at', 'DESC')
    ->limit(2)
    ->get();

}


    public function getPostById($postId)
    {
        $result = $this->queryBuilder
            ->table('posts')
            ->select(['posts.id', 'posts.title', 'posts.description', 'media.path AS cover_photo_path', 'users.username'])
            ->leftJoin('media', 'posts.id', '=', 'media.post_id')
            ->leftJoin('users', 'media.user_id', '=', 'users.id')
            ->where('posts.id', '=', $postId)  
            ->limit(1) 
            ->get();
    
        return !empty($result) ? $result[0] : null; 
    }
    

    

    public function updatePost($postId, $data)
    {
        return $this->queryBuilder
            ->table('posts')
            ->update([
                'title' => $data['title'],
                'description' => $data['description']
            ])
            ->where('id', '=', $postId)
            ->execute();
    }


    public function createPost($data)
{
    return $this->queryBuilder
        ->table('posts')
        ->insert($data);
}

public function deletePost($postId)
{
    error_log("Deleting post with ID: $postId");
    return $this->queryBuilder
        ->table('posts')
        ->where('id', '=', $postId)
        ->delete()
        ->execute();
}







}
