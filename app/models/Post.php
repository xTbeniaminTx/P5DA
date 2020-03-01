<?php

namespace app\models;


use app\services\Paginator;

class Post extends Manager
{

    public function getPosts()
    {
        $this->db->query("SELECT * FROM posts ORDER BY content_date DESC");

        $results = $this->db->resultSet();

        return $results;

    }

    public function paginatePosts($num_of_records, $total_record, $object)
    {
        $posts = [];

        $pages = new Paginator($num_of_records, 'p');
        $pages->set_total($total_record);

        $this->db->query('SELECT * FROM posts' . $pages->get_limit());

        $data = $this->db->resultSet();


        foreach ($data as $item) {
            array_push(
                $posts, [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'content_date' => $item->content_date
                ]
            );
        }

        return [$posts, $pages->page_links($path = '?action=posts&')];

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
        $this->db->query(
            'INSERT INTO posts (author_id, title, content, content_date)
                              VALUES(:author_id, :title, :content, :content_date)'
        );
        $this->db->bind(':author_id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':content_date', $data['content_date']);

        //execute
        if ($this->db->execute()) {
            return true;
        }

        return false;

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
        }

        return false;

    }


    public function deletePost($id)
    {
        $this->db->query('DELETE FROM posts WHERE id = :id');
        $this->db->bind(':id', $id);

        //execute
        if ($this->db->execute()) {
            return true;
        }

        return false;

    }

}
