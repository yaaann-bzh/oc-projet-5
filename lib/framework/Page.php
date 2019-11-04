<?php
namespace framework;

class Page extends ApplicationComponent
{
    protected $tabTitle;
    protected $activeNav;
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
        $this->tabTitle .= ' | Blog de jean Forteroche';
        $this->addVars('tabTitle', $this->tabTitle);
    }

    public function getTabTitle()
    {
        return $this->tabTitle;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function generate()
    {
        if ($this->activeNav === null) {
            $this->setActiveNav('');
        }

        if (!file_exists($this->content))
        {
            throw new \RuntimeException('La vue spécifiée n\'existe pas');
        }

        $user = $this->app->user();

        extract($this->vars);

        ob_start();
        require $this->content;
        $content = ob_get_clean();

        ob_start();
        require __DIR__ . '/../../yannsjobs/templates/default.php';
        return ob_get_clean();
    }

    public function addVars($var, $value)
    {
        if (!is_string($var) || is_numeric($var) || empty($var))
        {
            throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractères non nulle');
        }

        $this->vars[$var] = $value;
    }
}
