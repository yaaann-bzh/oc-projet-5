<?php
namespace yannsjobs;

use framework\Application;

class Frontend extends Application
{
    public function run()
    {
        $controller = $this->getController();
        $this->userConnect('Member', $controller);

        $controller->execute();
      
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
   
}
