<?php
namespace model;

use entity\Member;

class MemberManager extends \framework\Manager
{
    protected $entities = 'members';

    public function getSingle($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';

        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $req->execute();
        
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Member');
        
        if ($member = $req->fetch())
        {
            $member->setInscriptionDate(new \DateTime($member->inscriptionDate()));
            if ($member->savedPosts() === null) {
                $member->setSavedPosts([]);
            } else {
                $member->setSavedPosts(explode(',', $member->savedPosts()));
            }

            return $member;
        }
        
        return null; 
    }

    public function getList($filters = [], $debut = null, $limit = null) {
        # code...
    }

    public function saveConnexionId($id, $connexionId)
    {
        if ($connexionId !== null) {
            $sql = 'UPDATE ' . $this->table . ' SET connexionId = :connexionId WHERE id = :id';

            $q = $this->dao->prepare($sql);

            $q->bindValue(':connexionId', $connexionId);
            $q->bindValue(':id', $id, \PDO::PARAM_INT);

            $q->execute();
        }
    }

    public function checkConnexionId($connexionId)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE connexionId = :connexionId';

        $req = $this->dao->prepare($sql);
        $req->bindValue(':connexionId', $connexionId);
        $req->execute();
        
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Member');
        
        if ($member = $req->fetch())
        {
            $member->setInscriptionDate(new \DateTime($member->inscriptionDate()));
            return $member;
        }
        
        return null; 
    }

    public function add(Member $member)
    {        
        $sql = 'INSERT INTO ' . $this->table . ' SET username = :username, role = :role, lastname = :lastname, firstname = :firstname, email = :email, phone = :phone, pass = :pass, inscriptionDate = NOW()';

        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':username', $member->username());
        $req->bindValue(':role', $member->role());
        $req->bindValue(':lastname', $member->lastname());
        $req->bindValue(':firstname', $member->firstname());
        $req->bindValue(':email', $member->email());
        $req->bindValue(':phone', $member->phone());        
        $req->bindValue(':pass', $member->pass());
        
        $req->execute();
    }
    
    public function checkUniqColumn(Member $member) {
        $columns = array(
            'ce nom d\'utilisateur' => $member->username(),
            'cette adresse mail' => $member->email(),
            'ce numéro de téléphone' => $member->phone()
        );
        
        foreach ($columns as $key => $$column) {
            
        }
    }

    public function update($id, array $values)
    {
        $sql = 'UPDATE ' . $this->table . ' SET ';

        foreach ($values as $key => $value) {
            $sql .= $key . ' = :' . $key . ', ';
        }

        $sql = substr($sql, 0, -2);

        $sql .= ' WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        foreach ($values as $key => $value) {
            $qkey = ':' . $key;
            $q->bindValue($qkey , $value);
        }
        
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id = '.(int) $id;
        $this->dao->exec($sql);
    }
    
    public function savePost(int $id, int $postId) {
        
        $select = 'SELECT savedPosts FROM ' . $this->table . ' WHERE id = ' . $id;

        $savedPosts = explode(',', $this->dao->query($select)->fetchColumn());

        if (empty($savedPosts) OR $savedPosts[0] === ''){
            $this->update($id, array('savedPosts' => $postId));
        } elseif (!in_array($postId, $savedPosts)){
            $savedPosts[] = $postId;
            $saved = implode(',', $savedPosts);
            $this->update($id, array('savedPosts' => $saved));
        }        
    }
    
    public function removePost(int $id, int $postId) {
        
        $select = 'SELECT savedPosts FROM ' . $this->table . ' WHERE id = ' . $id;

        $savedPosts = explode(',', $this->dao->query($select)->fetchColumn());

        if (in_array($postId, $savedPosts)){
            $key = array_search($postId, $savedPosts);
            unset($savedPosts[$key]);
            if (!empty($savedPosts)){
                $saved = implode(',', $savedPosts);
            } else {
                $saved = NULL;
            }
            $this->update($id, array('savedPosts' => $saved));
        } 
    }
    
    public function actualizeSavedPosts(string $tableName, Member $member) {
        
        $sql = 'SELECT id FROM ' . $tableName . ' ORDER BY id';
    
        $req = $this->dao->query($sql);
        $posts = $req->fetchAll(\PDO::FETCH_COLUMN, 0);
        
        $savedPosts = $member->savedPosts();
       
        foreach ($savedPosts as $key => $id) {
            if(!in_array($id, $posts)) {
                var_dump($key);
                unset($savedPosts[$key]);
            }
        }   

        if ($savedPosts !== $member->savedPosts()) {
            $member->setSavedPosts($savedPosts);
            if (!empty($savedPosts)){
                $saved = implode(',', $savedPosts);
            } else {
                $saved = NULL;
            }
            $this->update($member->id(), array('savedPosts' => $saved));
        }
    } 
}
