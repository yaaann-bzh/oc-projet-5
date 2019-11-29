<?php
namespace model;

use entity\Post;

class PostManager extends \framework\Manager 
{
    protected $entities = 'posts';

    public function getList($filters = [], $debut = null, $limit = null) {

        $sql = 'SELECT * FROM ' . $this->table;
        
        if (!empty($filters)) {
            $sql .= ' WHERE ';
            foreach ($filters as $key => $filter) {
                $sql .= $key . $filter . ' AND ';
            }
            $sql = substr($sql, 0, -5);
        }
        
        $sql .= ' ORDER BY addDate DESC';

        if ($debut !== null && $limit !== null) {
            $sql .= ' LIMIT ' .(int) $limit.' OFFSET '.(int) $debut; 
        }
        
        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Post');
        $posts = $req->fetchAll();

        foreach ($posts as $post)
        {
            $post->setAddDate(new \DateTime($post->addDate()));
            $post->setExpirationDate(new \DateTime($post->expirationDate()));
        }
        
        $req->closeCursor();
        
        return $posts;
    }

    public function getSingle($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';

        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $req->execute();
        
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Post');
        
        if ($post = $req->fetch())
        {
            $post->setAddDate(new \DateTime($post->addDate()));
            $post->setExpirationDate(new \DateTime($post->expirationDate()));
            return $post;
        }
        return null;  
    }
    
    public function process(Post $post) {
        if ($post->id() !== null) {
            $this->update($post);
        } else {
            $this->add($post);
        }
    }

    public function add(Post $post)
    {
        $sql = 'INSERT INTO ' . $this->table . ' SET recruiterId = :recruiterId, location = :location, title = :title, content = :content, addDate = NOW(), expirationDate = DATE_ADD(NOW(), INTERVAL :duration MONTH)';
        
        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':recruiterId', $post->recruiterId(), \PDO::PARAM_INT);
        $req->bindValue(':location', $post->location());
        $req->bindValue(':title', $post->title());
        $req->bindValue(':content', $post->content());
        $req->bindValue(':duration', $post->duration());
        
        $req->execute();

        $post->setId((int)$this->dao->lastInsertId());
    }
    
    public function update(Post $post) {
        $sql = 'UPDATE ' . $this->table . ' SET location = :location, title = :title, content = :content WHERE id = :id';
        
        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':location', $post->location());
        $req->bindValue(':title', $post->title());
        $req->bindValue(':content', $post->content());
        $req->bindValue(':id', $post->id(), \PDO::PARAM_INT);

        $req->execute();
    }

    public function delete($id) {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id = '.(int) $id;
        $this->dao->exec($sql);
    }
    
    public function setExpired($id) {
        $sql = 'UPDATE ' . $this->table . ' SET expirationDate = DATE_SUB(NOW(), interval 1 HOUR) WHERE id = :id';       
        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', $id, \PDO::PARAM_INT);
        $req->execute();
    }

    public function getIdList() {
        $sql = 'SELECT id FROM ' . $this->table;
        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_ASSOC);
        $res = $req->fetchAll();
        $idList = [];
        foreach ($res as $id) {
            $idList[] = (int)$id['id'];
        }
        return $idList;
    }

}

