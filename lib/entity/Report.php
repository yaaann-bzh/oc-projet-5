<?php
namespace entity;

class Report 
{
    protected $id;
    protected $authorId;
    protected $commentId;
    protected $content;
    protected $commentContent;
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

    // SETTERS //

    public function setAuthorId($authorId)
    {
        if (!is_int($authorId) || empty($authorId))
        {
            throw new Exception('Identifiant auteur invalide');
        }

        $this->authorId = $authorId;
    }

    public function setCommentId($commentId)
    {
        if (!is_int($commentId) || empty($commentId))
        {
            throw new Exception('Identifiant commentaire invalide');
        }

        $this->commentId = $commentId;
    }

    public function setContent($content)
    {
        if (!is_string($content) || empty($content))
        {
            throw new Exception('Contenu du signalement invalide');
        }

        $this->content = $content;
    }

    public function setCommentContent($commentContent)
    {
        if (!is_string($commentContent) || empty($commentContent))
        {
            throw new Exception('Contenu du commentaire signalé invalide');
        }

        $this->commentContent = $commentContent;
    }

    public function setReportDate(\DateTime $reportDate)
    {
        $this->reportDate = $reportDate;
    }

    // GETTERS //

    public function id()
    {
        return $this->id;
    }

    public function authorId()
    {
        return $this->authorId;
    }

    public function commentId()
    {
        return $this->commentId;
    }

    public function content()
    {
        return $this->content;
    }

    public function commentContent()
    {
        return $this->commentContent;
    }

    public function reportDate()
    {
        return $this->reportDate;
    }

}