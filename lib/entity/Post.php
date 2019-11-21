<?php
namespace entity;

class Post 
{
    protected $id;
    protected $recruiterId;
    protected $recruiterName;
    protected $location;
    protected $title;
    protected $content;
    protected $addDate;
    protected $expirationDate;
    protected $duration;
    protected $saved;

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
            throw new \Exception('Identifiant de publication invalide');
        }

        $this->id = $id;
    }

    public function setRecruiterId($recruiterId)
    {
        if (!is_int($recruiterId) || empty($recruiterId))
        {
            throw new \Exception('Identifiant recruteur invalide');
        }

        $this->recruiterId = $recruiterId;
    }

    public function setRecruiterName($recruiterName)
    {
        if (!is_string($recruiterName))
        {
            throw new \Exception('Nom du recruteur invalide');
        }
        if (!empty($recruiterName)) {
            $this->recruiterName = $recruiterName;
        }
    }

    public function setLocation($location)
    {
        if (!is_string($location) || empty($location))
        {
            throw new \Exception('Localisation invalide');
        }

        $this->location = $location;
    }

    public function setTitle($title)
    {
        if (!is_string($title) || empty($title))
        {
            throw new \Exception('Titre de l\'annonce invalide');
        }

        $this->title = $title;
    }

    public function setContent($content)
    {
        if (!is_string($content) || empty($content))
        {
            throw new \Exception('Contenu de l\'annonce invalide');
        }

        $this->content = $content;
    }

    public function setAddDate(\DateTime $addDate)
    {
        $this->addDate = $addDate;
    }
    
    public function setExpirationDate(\DateTime $expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }
    
    public function setDuration($duration)
    {
        if ((int)$duration === 0 || empty($duration))
        {
            throw new \Exception('La durée précisée est invalide');
        }

        $this->duration = (int)$duration;
    }
    
    public function setSaved(bool $saved) {
        $this->saved = $saved;
    }

    // GETTERS //

    public function id()
    {
        return $this->id;
    }

    public function recruiterId()
    {
        return $this->recruiterId;
    }

    public function recruiterName()
    {
        return $this->recruiterName;
    }

    public function location()
    {
        return $this->location;
    }

    public function title()
    {
        return $this->title;
    }

    public function content()
    {
        return $this->content;
    }

    public function addDate()
    {
        return $this->addDate;
    }
    
    public function expirationDate()
    {
        return $this->expirationDate;
    }

    public function duration()
    {
        return $this->duration;
    }
    
    public function saved() {
        return $this->saved;
    }
}
