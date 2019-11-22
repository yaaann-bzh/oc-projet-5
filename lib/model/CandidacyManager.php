<?php
namespace model;

use entity\Candidacy;

class CandidacyManager extends \framework\Manager 
{
    protected $entities = 'candidacies';

    public function getList($filters = [], $debut = null, $limit = null) {

        $sql = 'SELECT * FROM ' . $this->table;
        
        if (!empty($filters)) {
            $sql .= ' WHERE ';
            foreach ($filters as $key => $filter) {
                $sql .= $key . $filter . ' AND ';
            }
            $sql = substr($sql, 0, -5);
        }
        
        $sql .= ' ORDER BY sendDate DESC';

        if ($debut !== null && $limit !== null) {
            $sql .= ' LIMIT ' .(int) $limit.' OFFSET '.(int) $debut; 
        }
        
        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Candidacy');
        $candidacies = $req->fetchAll();

        foreach ($candidacies as $candidacy)
        {
            $candidacy->setSendDate(new \DateTime($candidacy->sendDate()));
            $candidacy->setIsRead($candidacy->isRead() === '0' ? true : false);
            $candidacy->setIsArchived($candidacy->isArchived() === '0' ? true : false);
        }
        
        $req->closeCursor();
        
        return $candidacies;
    }

    public function getSingle($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';

        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $req->execute();
        
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Candidacy');
        
        if ($candidacy = $req->fetch())
        {
            $candidacy->setSendDate(new \DateTime($candidacy->sendDate()));
            $candidacy->setIsRead($candidacy->isRead() === '0' ? true : false);
            $candidacy->setIsArchived($candidacy->isArchived() === '0' ? true : false);

            return $candidacy;
        }
        return null;  
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

    public function add(Candidacy $candidacy)
    {
        $sql = 'INSERT INTO ' . $this->table . ' SET candidateId = :candidateId, postId = :postId, recruiterId = :recruiterId, cover = :cover, resumeFile = :resumeFile, sendDate = NOW()';
        
        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':candidateId', $candidacy->candidateId(), \PDO::PARAM_INT);
        $req->bindValue(':postId', $candidacy->postId(), \PDO::PARAM_INT);
        $req->bindValue(':recruiterId', $candidacy->recruiterId(), \PDO::PARAM_INT);
        $req->bindValue(':cover', $candidacy->cover());
        
        $resumeFile = !empty($candidacy->resumeFile())? $candidacy->resumeFile() :NULL;

        $req->bindValue(':resumeFile', $resumeFile);

        $req->execute();

        $candidacy->setId((int)$this->dao->lastInsertId());
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id = '.(int) $id;
        $this->dao->exec($sql);
    }
}

