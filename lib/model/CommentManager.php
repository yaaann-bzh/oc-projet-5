<?php
namespace model;

class CommentManager extends \framework\Manager 
{
    protected $entities = 'comments';

    public function getList($debut, $limit, $filters=[])
    {
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
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Comment');
        $comments = $req->fetchAll();

        foreach ($comments as $comment)
        {
            $comment->setAddDate(new \DateTime($comment->addDate()));
            if ($comment->updateDate() != null) {
                $comment->setUpdateDate(new \DateTime($comment->updateDate()));
            }
            if ($comment->reportDate() != null) {
                $comment->setReportDate(new \DateTime($comment->reportDate()));
            }
        }
        
        $req->closeCursor();
        
        return $comments;
    }

    public function getSingle($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $req->execute();
        
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Comment');
        
        if ($comment = $req->fetch())
        {
            $comment->setAddDate(new \DateTime($comment->addDate()));
            if ($comment->updateDate() != null) {
                $comment->setUpdateDate(new \DateTime($comment->updateDate()));
            }
            if ($comment->reportDate() != null) {
                $comment->setReportDate(new \DateTime($comment->reportDate()));
            }
            return $comment;
        }
        
        return null;  
    }

    public function add($memberId, $postId, $content)
    {
        $sql = 'INSERT INTO ' . $this->table . ' SET memberId = :memberId, postId = :postId, content = :content, addDate = NOW()';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':memberId', $memberId);
        $q->bindValue(':postId', $postId);
        $q->bindValue(':content', $content);
        
        $q->execute();
    }

    public function update($id, $content)
    {
        $sql = 'UPDATE ' . $this->table . ' SET content = :content, updateDate = NOW() WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':content', $content);
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
    }

    public function delete($id)
    {
        $sql = 'UPDATE ' . $this->table . ' SET removed = 1, updateDate = NOW() WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
    }

    public function deleteFromPost($postId)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE postId = '.(int) $postId;

        $this->dao->exec($sql);
    }

    public function setReported($id)
    {
        $sql = 'UPDATE ' . $this->table . ' SET reportDate = NOW() WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
    }

    public function clearReports($id)
    {
        $sql = 'UPDATE ' . $this->table . ' SET reportDate = NULL WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
    }
}
