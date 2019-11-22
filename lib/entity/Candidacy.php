<?php
namespace entity;

class Candidacy 
{
    protected $id;
    protected $candidateId;
    protected $postId;
    protected $recruiterId;
    protected $cover;
    protected $sendDate;
    protected $isRead;
    protected $isArchived;
    protected $resumeFile;
    protected $candidate;

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
            throw new \Exception('Identifiant de candidature invalide');
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
    
    public function setRecruiterId($recruiterId)
    {
        if (!is_int($recruiterId) || empty($recruiterId))
        {
            throw new \Exception('Identifiant recruteur invalide');
        }

        $this->recruiterId = $recruiterId;
    }

    public function setCover($cover)
    {
        if (!is_string($cover) || empty($cover))
        {
            throw new \Exception('Contenu de la motivation invalide');
        }

        $this->cover = $cover;
    }

    public function setSendDate(\DateTime $sendDate)
    {
        $this->sendDate = $sendDate;
    }
    
    public function setIsRead($isRead)
    {
        if (!is_bool($isRead))
        {
            throw new \Exception('La valeur du statut "lue" doit être un booléen');
        }

        $this->isRead = $isRead;
    }
    
    public function setIsArchived($isArchived)
    {
        if (!is_bool($isArchived))
        {
            throw new \Exception('La valeur du statut "archivée" doit être un booléen');
        }

        $this->isArchived = $isArchived;
    }
    
    public function setCandidateName($firstname, $lastname)
    {
        if (!is_string($firstname) || !is_string($lastname))
        {
            throw new \Exception('Nom du candidat invalide');
        }
        if (!empty($firstname) OR !empty($lastname)) {
            $this->candidateName = $firstname . ' ' . $lastname;
        }
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
    
    public function setResumeFile($resumeFile) {
        $this->resumeFile = $resumeFile;
    }
    
    public function setCandidate(Member $candidate) {
        $this->candidate = $candidate;
    }

    // GETTERS //

    public function id()
    {
        return $this->id;
    }

    public function candidateId() {
        return $this->candidateId;
    }
    
    public function postId() {
        return $this->postId;
    }
        
    public function recruiterId()
    {
        return $this->recruiterId;
    }

    public function cover()
    {
        return $this->cover;
    }
    
    public function sendDate()
    {
        return $this->sendDate;
    }
    
    public function isRead()
    {
        return $this->isRead;
    }
    
    public function isArchived()
    {
        return $this->isArchived;
    }
    
    public function recruiterName()
    {
        return $this->recruiterName;
    }
    
    public function candidateName()
    {
        return $this->candidateName;
    }
    
    public function resumeFile()
    {
        return $this->resumeFile;
    }
    
    public function candidate() {
        return $this->candidate;
    }
    
}
