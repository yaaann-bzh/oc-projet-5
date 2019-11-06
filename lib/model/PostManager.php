<?php
namespace model;

use entity\Post;

class PostManager extends \framework\Manager 
{
    protected $entities = 'posts';

    public function getList($debut = null, $limit = null, $filters = []) {

        $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY addDate DESC';

        if (isset($debut) && isset($limit)) {
            $sql .= ' LIMIT ' .(int) $limit.' OFFSET '.(int) $debut; 
        }

        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Post');
        $posts = $req->fetchAll();

        foreach ($posts as $post)
        {
            $post->setAddDate(new \DateTime($post->addDate()));
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
            if ($post->updateDate() != null) {
                $post->setUpdateDate(new \DateTime($post->updateDate()));
            }
            return $post;
        }
        
        return null;  
    }

    public function add(Post $post)
    {
        $sql = 'INSERT INTO ' . $this->table . ' SET authorId = :authorId, title = :title, content = :content, addDate = NOW()';

        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':authorId', $post->authorId(), \PDO::PARAM_INT);
        $req->bindValue(':title', $post->title());
        $req->bindValue(':content', $post->content());
        
        $req->execute();

        $post->setId((int)$this->dao->lastInsertId());
    }

    public function update($id, $title, $content)
    {
        $sql = 'UPDATE ' . $this->table . ' SET title = :title, content = :content, updateDate = NOW() WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':title', $title);
        $q->bindValue(':content', $content);
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id = '.(int) $id;
        $this->dao->exec($sql);
    }

    public function getIdList()
    {
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

