<?php

namespace app\models;

use app\services\Database;

class Post
{
    private $db;


    public function __construct()
    {
        $this->db = new Database;
    }


    public function getPosts()
    {
        $this->db->query("SELECT * FROM posts ORDER BY id DESC");

        $results = $this->db->resultSet();

        return $results;

    }


    public function getPostById($id)
    {
        $this->db->query('SELECT * FROM posts WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;

    }


    public function addPost($data)
    {
        $this->db->query('INSERT INTO posts (id, title, content, content_date)
                              VALUES(:id, :title, :content, :content_date)');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':content_date', $data['content_date']);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function updatePost($data)
    {
        $this->db->query('UPDATE posts SET title = :title, content = :content WHERE id=:id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function deletePost($id)
    {
        $this->db->query('DELETE FROM posts WHERE id = :id');
        $this->db->bind(':id', $id);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

}