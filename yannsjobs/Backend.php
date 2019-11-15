<?php

namespace yannsjobs;
use yannsjobs\modules\connexion\ConnexionController;


use framework\Application;

class Backend extends Application{
    
    public function run()
    {
        if ($this->user->isAuthenticated())
        {
            $controller = $this->getController();
            $this->userConnect('Member', $controller);
            $this->checkUserRole();
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
