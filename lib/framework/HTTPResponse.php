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

    // redirection 404 :
    public function redirect404()
    {
        // créer une instance de la classe Page que l'on stocke dans l'attribut correspondant.
        $this->page = new Page($this->app);

        // On assigne ensuite à la page le fichier qui fait office de vue à générer. 
        // Ce fichier contient le message d'erreur formaté. Vous pouvez placer tous ces fichiers dans le dossier /Errors par exemple, sous le nom code.html.
        // Le chemin menant au fichier contenant l'erreur 404 sera donc /Errors/404.html.
        $this->page->setContent(__DIR__.'/../../Errors/404.html');
        $this->page->setTabTitle('Erreur 404');

        // On ajoute un header disant que le document est non trouvé (HTTP/1.0 404 Not Found).
        $this->addHeader('HTTP/1.0 404 Not Found');
        
        // On envoie la réponse.
        $this->send();
    }

    // redirection 403 :
    public function redirect403()
    {
        $this->page = new Page($this->app);

        $this->page->setContent(__DIR__.'/../../Errors/403.html');
        $this->page->setTabTitle('Erreur 403');

        $this->addHeader('HTTP/1.0 403 Forbidden');
        
        $this->send();
    }

    // Envoyer la réponse en générant la page :
    public function send()
    {
        exit($this->page->getGenerated());
    }

    // Ajouter un cookie :
    /** La valeur par défaut du dernier paramètre de la méthode setCookie() est à true, 
     * alors qu'elle est à false sur la fonction setcookie() de la bibliothèque standard de PHP.
     * Il s'agit d'une sécurité qu'il est toujours préférable d'activer. */
    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain= null, $secure = false, $httpOnly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    
    public function setPage($page)
    {
        $this->page = $page;
    }

}