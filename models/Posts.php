<?php
namespace Models;

class Posts extends Model
{
    public $table = 'posts';
    public $fields = [
        'id',
        'title',
        'description'
    ];

    public function getAllPosts()
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
    return $this->queryBuilder
        ->table('posts') // Specify the table to perform the operation on (posts table)
        ->where('id', '=', $postId) // Add a condition to identify the post by its ID
        ->delete() // Delete the row matching the condition
        ->execute(); // Execute the query
}



}
