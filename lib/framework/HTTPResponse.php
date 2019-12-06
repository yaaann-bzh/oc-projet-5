<?php

namespace framework;

class HTTPResponse extends ApplicationComponent
{
    protected $page;

    // Ajouter un header spécifique :
    public function addHeader($header)
    {
        header($header);
    }

    // rediriger l'utilisateur :
    public function redirect($location)
    {
        header('Location:'.$location);
        exit;
    }
    
    public function send204()
    {
        $this->page = new Page($this->app);

        $this->addHeader('HTTP/1.0 204 No Content');
        
        $this->send();
    }

    public function redirect404()
    {
        $this->page = new Page($this->app);

        $this->page->setTemplate('errors/error_404.twig');
        $this->page->addVars(array('title' => 'Erreur 404 | YannsJobs'));

        $this->addHeader('HTTP/1.0 404 Not Found');
        
        $this->send();
    }

    public function redirect403()
    {
        $this->page = new Page($this->app);

        $this->page->setTemplate('errors/error_403.twig');
        $this->page->addVars(array('title' => 'Erreur 403 | YannsJobs'));

        $this->addHeader('HTTP/1.0 403 Forbidden');
        
        $this->send();
    }

    public function send()
    {
        exit($this->page->getGenerated());
    }

    /** La valeur par défaut du dernier paramètre de la méthode setCookie() est à true,
     * alors qu'elle est à false sur la fonction setcookie() de la bibliothèque standard de PHP.
     * Il s'agit d'une sécurité qu'il est toujours préférable d'activer. */
    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain= null, $secure = false, $httpOnly = true)
    {
        //$dump = 'setCookie : ' . $name;
        //var_dump($dump);
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    
    public function setPage($page)
    {
        $this->page = $page;
    }

}