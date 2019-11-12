<?php
namespace yannsjobs;

use framework\Application;

class Frontend extends Application
{
    protected $name = 'Frontend';

    public function run()
    {
        $controller = $this->getController();
        $this->userConnect($controller);

        $controller->execute();
      
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}
