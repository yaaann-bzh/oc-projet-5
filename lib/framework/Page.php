<?php

namespace framework;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;

class Page extends ApplicationComponent
{
    protected $template;
    protected $content;
    protected $loader;
    protected $twig;
    protected $vars = [];

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($this->loader, [
            'debug' => true,
            'cache' => false //'/tmp'
        ]);
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addExtension(new Extension());
        $this->twig->addExtension(new IntlExtension());
    }

    public function setActiveNav(string $nav)
    {
        $this->activeNav = $nav;
        $this->addVars(array('activeNav' => $this->activeNav));
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
        $this->template = $template;
    }

    public function getGenerated()
    {
        echo $this->twig->render($this->template, $this->vars);
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
