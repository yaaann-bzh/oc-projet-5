<?php
namespace entity;

class SavedPost 
{
    protected $id;
    protected $candidateId;
    protected $postId;


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

    public function setId($id)
    {
        if (!is_int($id) || empty($id))
        {
            throw new \Exception('Identifiant invalide');
        }

        $this->id = $id;
    }

    public function setCandidateId($candidateId)
    {
        if (!is_int($candidateId) || empty($candidateId))
        {
            throw new \Exception('Identifiant candidat invalide');
        }

        $this->candidateId = $candidateId;
    }

    public function setPostId($postId)
    {
        if (!is_int($postId) || empty($postId))
        {
            throw new \Exception('Identifiant publication invalide');
        }

        $this->postId = $postId;
    }


    // GETTERS //

    public function id()
    {
        return $this->id;
    }

    public function candidateId()
    {
        return $this->candidateId;
    }

    public function postId()
    {
        return $this->postId;
    }

}
