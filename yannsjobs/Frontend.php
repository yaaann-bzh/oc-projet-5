<?php
namespace yannsjobs;

use framework\Application;

class Frontend extends Application
{
    public function run()
    {
        $controller = $this->getController();
        
        if (empty($_SESSION)) {
            $this->user->tryToReconnect('Member', $controller);
        }

        $controller->execute();
      
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
   
}
