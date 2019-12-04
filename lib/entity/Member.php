<?php
namespace entity;

class Member
{
    protected $id;
    protected $username;
    protected $role;
    protected $lastname;
    protected $firstname;
    protected $email;
    protected $phone;
    protected $pass;
    protected $inscriptionDate;
    protected $deleteDate;
    protected $connexionId;
    protected $savedPosts;

    //Mother
    public function __construct(array $donnees = [])
    {
        if (!empty($donnees))
        {
            $this->hydrate($donnees);
        }
    }

    //Mother
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $attribut => $valeur)
        {
            $methode = 'set'.ucfirst($attribut);

            if (is_callable([$this, $methode]))
            {
                $this->$methode($valeur);
            }
        }
    }

    // SETTERS //
    public function setUsername($username)
    {
        if (!is_string($username) || empty($username))
        {
            throw new \Exception('Pseudo non valide');
        }

        $this->username = $username;
    }

    public function setRole($role)
    {
        if (!is_string($role) || empty($role))
        {
            throw new \Exception('Role non valide');
        }

        $this->role = $role;
    }
    
    public function setLastname($lastname)
    {
        if (!is_string($lastname) || empty($lastname))
        {
            throw new \Exception('Nom non valide');
        }

        $this->lastname = ucfirst(strtolower($lastname));
    }
    
    public function setFirstname($firstname)
    {
        if (!is_string($firstname) || empty($firstname))
        {
            throw new \Exception('Prénom non valide');
        }

        $this->firstname = ucfirst(strtolower($firstname));
    }
    
    public function setEmail($email)
    {
        if (!is_string($email) || empty($email))
        {
            throw new\ Exception('Adresse email non valide');
        }

        $this->email = $email;
    }

    public function setPhone($phone)
    {
        if(is_null($phone)){
            return $this->phone = null;
        }

        if (!is_string($phone))
        {
            throw new \Exception('Numéro de téléphone invalide');
        }

        $this->phone = $phone;
    }
    
    public function setPass($pass)
    {
        if (!is_string($pass) || empty($pass))
        {
            throw new\ Exception('Mot de passe non valide');
        }

        $this->pass = $pass;
    }

    public function setInscriptionDate(\DateTime $inscriptionDate)
    {
        $this->inscriptionDate = $inscriptionDate;
    }
    
    public function setDeleteDate(\DateTime $deleteDate) {
        $this->deleteDate = $deleteDate;
    }

    public function setConnexionId($connexionId)
    {
        if (!is_string($connexionId) || empty($connexionId))
        {
            throw new\ Exception('Le système a rencontré un problème : connexionId');
        }

        $this->connexionId = $connexionId;
    }

    public function setSavedPosts(array $savedPosts)
    {
        $this->savedPosts = $savedPosts;
    }
    
    // GETTERS //
    public function id()
    {
        return $this->id;
    }

    public function username()
    {
        return $this->username;
    }

    public function role()
    {
        return $this->role;
    }
    
    public function lastname()
    {
        return $this->lastname;
    }
    
    public function firstname()
    {
        return $this->firstname;
    }
    
    public function email()
    {
        return $this->email;
    }
    
    public function phone()
    {
        return $this->phone;
    }
    
    public function pass()
    {
        return $this->pass;
    }

    public function inscriptionDate()
    {
        return $this->inscriptionDate;
    }
    
    public function deleteDate()
    {
        return $this->deleteDate;
    }
    
    public function connexionId()
    {
        return $this->connexionId;
    }
    
    public function savedPosts() {
        return $this->savedPosts;
    }
}
