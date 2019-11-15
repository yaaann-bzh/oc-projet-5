<?php

namespace yannsjobs;
use yannsjobs\modules\connexion\ConnexionController;


use framework\Application;

class Backend extends Application{
    
    protected $name = 'Backend';

    public function run()
    {
        //var_dump($this->user->isAuthenticated());
        if ($this->user->isAuthenticated())
        {
            $controller = $this->getController();
            $this->userConnect($controller);
        }
        else
        {
            $controller = new ConnexionController($this, 'connexion', 'index', $this->getDBConnexion());
        }

        $controller->execute();

        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
    
}
