<?php
namespace model;

class SavedPostManager extends \framework\Manager
{
    protected $entities = 'saved';

    public function getSingle($id)
    {

    }

    public function getList($filters = [], $debut = null, $limit = null) {
        
        $sql = 'SELECT * FROM ' . $this->table;
        
        if (!empty($filters)) {
            $sql .= ' WHERE ';
            foreach ($filters as $key => $filter) {
                $sql .= $key . $filter . ' AND ';
            }
            $sql = substr($sql, 0, -5);
        }
        
        if ($debut !== null && $limit !== null) {
            $sql .= ' LIMIT ' .(int) $limit.' OFFSET '.(int) $debut; 
        }
        
        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\SavedPost');
        $savedPosts = $req->fetchAll();
        
        $req->closeCursor();
        
        return $savedPosts;
    }
    
    public function getPostIdList(int $candidateId)
    {
        $sql = 'SELECT postId FROM ' . $this->table . ' WHERE candidateId = ' . $candidateId . ' ORDER BY id DESC';

        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_ASSOC);
        $res = $req->fetchAll();

        $idList = [];
        foreach ($res as $id) {
            $idList[] = (int)$id['postId'];
        }
        return $idList;
    }
    
    public function add(int $candidateId, int $postId) {
        
        $sql = 'INSERT INTO ' . $this->table . ' SET candidateId = :candidateId, postId = :postId';

        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':candidateId', $candidateId, \PDO::PARAM_INT);
        $req->bindValue(':postId', $postId, \PDO::PARAM_INT);
        
        $req->execute();
    }
    
    public function delete(int $candidateId, int $postId) {
        
        $sql = 'DELETE FROM ' . $this->table . ' WHERE candidateId = ' . $candidateId . ' AND postId = ' . $postId;
        $this->dao->exec($sql);
 
    }
    
    public function exists($candidateId, $postId) {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE candidateId = :candidateId AND postId = :postId';
        $req = $this->dao->prepare($sql);
        $req->bindValue(':candidateId', (int) $candidateId, \PDO::PARAM_INT);
        $req->bindValue(':postId', (int) $postId, \PDO::PARAM_INT);
        
        $req->execute();

        $res = $req->fetch();

        return is_array($res);
    }
}
