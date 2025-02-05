<?php
namespace Models;

class Media extends Model
{
    public $table = 'media';
    public $fields = [
        'id', 
        'original_name', 
        'hash_name', 
        'path', 
        'size', 
        'extension', 
        'photo_type', 
        'user_id', 
        'post_id'
    ];

    public function getProfilePicture($userId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['path'])
            ->where('user_id', '=', $userId)
            ->where('photo_type', '=', 'profile')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->getOne();
    }

    public function getCoverPhotoByPostId($postId)
{
    return $this->queryBuilder
        ->table('media')
        ->select(['id', 'path'])
        ->where('post_id', '=', $postId)
        ->where('photo_type', '=', 'cover')
        ->limit(1)
        ->getOne();
}


    public function updateCoverPhoto($coverPhoto, $path, $postId)
    {
        $existingMedia = $this->queryBuilder
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

            $this->queryBuilder
                ->table('media')
                ->delete()
                ->where('id', '=', $existingMedia['id'])
                ->execute();
        }
    }
    public function saveCoverPhoto($coverPhoto, $postId)
    {
        $hashName = md5(uniqid(time(), true)) . "." . strtolower(pathinfo($coverPhoto['name'], PATHINFO_EXTENSION));
        
        $path = '/ATIS/uploads/' . $hashName; 
        move_uploaded_file($coverPhoto['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path);

        return $this->queryBuilder
            ->table('media')
            ->insert([
                'original_name' => $coverPhoto['name'],
                'hash_name' => $hashName,
                'path' => $path,
                'size' => $coverPhoto['size'],
                'extension' => strtolower(pathinfo($coverPhoto['name'], PATHINFO_EXTENSION)),
                'user_id' => $_SESSION['user_id'],  
                'photo_type' => 'cover',
                'post_id' => $postId
            ]);
    }


    public function deleteMediaById($mediaId)
{
    return $this->queryBuilder
        ->table('media')
        ->delete()
        ->where('id', '=', $mediaId)
        ->execute();
}



}
