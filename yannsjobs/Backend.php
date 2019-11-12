<?php

namespace yannsjobs;
use yannsjobs\modules\connexion\ConnexionController;


use framework\Application;

class Backend extends Application{
    
    protected $name = 'Backend';

    public function run()
    {
        if ($this->user->isAuthenticated())
        {
            $this->userConnect();
            $controller = $this->getController();
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
