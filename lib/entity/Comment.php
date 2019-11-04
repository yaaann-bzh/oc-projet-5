<?php
namespace entity;

class Comment 
{
    protected $id;
    protected $removed;
    protected $postId;
    protected $memberId;
    protected $content;
    protected $addDate;
    protected $updateDate;
    protected $reportDate;

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

    // GETTERS //

    public function id()
    {
        return $this->id;
    }

    public function removed()
    {
        return $this->removed;
    }
    
    public function postId()
    {
        return $this->postId;
    }    
    
    public function memberId()
    {
        return $this->memberId;
    }    
    
    public function content()
    {
        return $this->content;
    }
    
    public function addDate()
    {
        return $this->addDate;
    }
    
    public function updateDate()
    {
        return $this->updateDate;
    }

    public function reportDate()
    {
        return $this->reportDate;
    }

    // Setters //
    
    public function setMemberId($memberId)
    {
        if (!is_int($memberId) || empty($memberId))
        {
            throw new \Exception('Identifiant auteur invalide');
        }

        $this->memberId = $memberId;
    }   
    
    public function setPostId($postId)
    {
        if (!is_int($postId) || empty($postId))
        {
            throw new \Exception('Article spécifié non valide');
        }

        $this->postId = $postId;
    }
    
    public function setContent($content)
    {
        if (!is_string($content) || empty($content))
        {
            throw new \Exception('Contenu du commentaire invalide');
        }

        $this->content = $content;
    }
    
    public function setAddDate(\DateTime $addDate)
    {
        $this->addDate = $addDate;
    }
    
    public function setUpdateDate(\DateTime $updateDate)
    {
        $this->updateDate = $updateDate;
    }

    public function setReportDate(\DateTime $reportDate)
    {
        $this->reportDate = $reportDate;
    }

}