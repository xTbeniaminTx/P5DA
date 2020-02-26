<?php

namespace app\models;


class Comment extends Manager
{


    public function getComments()
    {
        $this->db->query("SELECT *,
                                comments.comment_id as commentId,
                                chapters.id as chapterId
                              FROM comments
                              INNER JOIN chapters
                              ON comments.comment_chapter_id = chapters.id
                              ORDER BY commentId DESC
                              ");

        $results = $this->db->resultSet();

        return $results;

    }


    public function getCommentsById($id)
    {
        $this->db->query("SELECT *,
                                comments.comment_id as commentId,
                                chapters.id as chapterId
                              FROM comments
                              INNER JOIN chapters
                              ON comments.comment_chapter_id = chapters.id
                              WHERE comments.comment_chapter_id = :id
                              ORDER BY commentId DESC
                              ");
        $this->db->bind(':id', $id);
        $results = $this->db->resultSet();

        return $results;

    }


    public function addComment($data)
    {
        $this->db->query('INSERT INTO 
                                comments (comment_chapter_id, comment_author, comment_email, comment_content,comment_status, comment_date)
                              VALUES
                                    (:comment_chapter_id, :comment_author, :comment_email, :comment_content, :comment_status, :comment_date)');
        $this->db->bind(':comment_chapter_id', $data['comment_chapter_id']);
        $this->db->bind(':comment_author', $data['comment_author']);
        $this->db->bind(':comment_email', $data['comment_email']);
        $this->db->bind(':comment_content', $data['comment_content']);
        $this->db->bind(':comment_status', $data['comment_status']);
        $this->db->bind(':comment_date', $data['comment_date']);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function approuveStatus($id)
    {
        $this->db->query('UPDATE comments SET comment_status = "approuved" WHERE comment_id=:id');
        $this->db->bind(':id', $id);
        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function unapprouveStatus($id)
    {
        $this->db->query('UPDATE comments SET comment_status = "unapprouved" WHERE comment_id=:id');
        $this->db->bind(':id', $id);
        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function deleteComment($id)
    {
        $this->db->query('DELETE FROM comments WHERE comment_id = :id');
        $this->db->bind(':id', $id);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

}