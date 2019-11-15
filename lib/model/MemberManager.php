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

            return $member;
        }
        
        return null; 
    }

    public function getList($debut, $limit, $id = null) {
        # code...
    }

    public function getId($var)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE email="' . $var . '"';
        return $this->dao->query($sql)->fetchColumn();
    }

    public function saveConnexionId($id, $connexionId)
    {
        $sql = 'UPDATE ' . $this->table . ' SET connexionId = :connexionId WHERE id = :id';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':connexionId', $connexionId);
        $q->bindValue(':id', $id, \PDO::PARAM_INT);

        $q->execute();
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

    public function add(Member $recruiter)
    {
        $sql = 'INSERT INTO ' . $this->table . ' SET pseudo = :pseudo, email = :email, pass = :pass, lastname = :lastname, firstname = :firstname, inscriptionDate = NOW()';

        $req = $this->dao->prepare($sql);
        
        $req->bindValue(':pseudo', $recruiter->pseudo());
        $req->bindValue(':email', $recruiter->email());
        $req->bindValue(':pass', $recruiter->pass());
        $req->bindValue(':lastname', $recruiter->lastname());
        $req->bindValue(':firstname', $recruiter->firstname());
        
        $req->execute();
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
}
