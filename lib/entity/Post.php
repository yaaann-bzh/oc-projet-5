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

    public function getExerpt($exerptLength)
    {
        $inlines = ['em', 'strong', 'span', 'a', 'i', 'th', 'td', 'img'];
        $blocks = ['p', 'h[1-6]', 'ul', 'ol', 'li', 'table', 'thead', 'tbody', 'tr'];

        $textContent = $this->content;
        $endRegex = '[a-zA-Z0-9_=";\s/:\.\-]*>#';
        foreach ($inlines as $inline) {
            $regexInline = '#<[/]*' . $inline . $endRegex;
            $textContent = preg_replace($regexInline, '', $textContent);
        }

        foreach ($blocks as $block) {
            $regexBlockBegin = '#<' . $block . $endRegex;
            $regexBlockEnd = '#</' . $block . $endRegex;
            $textContent = preg_replace($regexBlockBegin, ' ', $textContent);
            $textContent = preg_replace($regexBlockEnd, '<br/>', $textContent);
        }

        $fisrtBrPos = strpos($textContent, '<br/>');

        if ($fisrtBrPos <= $exerptLength) {
            $exerptLength = $fisrtBrPos;
        }
        return substr($textContent, 0, $exerptLength);
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
            throw new \Exception('Identifiant auteur invalide');
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
            throw new \Exception('Titre du post invalide');
        }

        $this->title = $title;
    }

    public function setContent($content)
    {
        if (!is_string($content) || empty($content))
        {
            throw new \Exception('Contenu du post invalide');
        }

        $this->content = $content;
    }

    public function setAddDate(\DateTime $addDate)
    {
        $this->addDate = $addDate;
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

}
