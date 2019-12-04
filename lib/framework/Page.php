<?php

namespace framework;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extension\DebugExtension;

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
            'debug' => false,
            'cache' => __DIR__ . '/../../tmp'
        ]);
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addExtension(new Extension());
        $this->twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addExtension(new DebugExtension());
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
        if ($this->template !== null) {
            echo $this->twig->render($this->template, $this->vars);
        } elseif ($this->content !== null) {
            echo $this->content;
        } else {
            throw new \Exception('Aucun Template ni contenu défini pour la page');
        }
        
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
