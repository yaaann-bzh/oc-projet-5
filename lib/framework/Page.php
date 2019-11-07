<?php
namespace framework;


class Page extends ApplicationComponent
{
    protected $tabTitle;
    protected $activeNav;
    protected $template;
    protected $content;

    protected $vars = [];

    public function setActiveNav(string $nav)
    {
        $this->activeNav = $nav;
        $this->addVars('activeNav', $this->activeNav);
    }

    public function setTabTitle(string $title)
    {
        $this->tabTitle = $title;
        $this->addVars('tabTitle', $this->tabTitle);
    }

    public function getTabTitle()
    {
        return $this->tabTitle;
    }

    public function vars()
    {
        return $this->vars;
    }

    public function template()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        if (!is_string($template))
        {
            throw new \InvalidArgumentException('La vue spécifiée est invalide');
        }

        if (empty($template))
        {
            throw new \RuntimeException('La vue spécifiée n\'existe pas');
        }

        $this->template = $template;
    }

    public function getGenerated()
    {
        echo $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function addVars(array $values)
    {
        foreach ($values as $var => $value) {
            if (!is_string($var) || is_numeric($var) || empty($var))
            {
                throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractères non nulle');
            }

            $this->vars[$var] = $value;
        }
     }
}
